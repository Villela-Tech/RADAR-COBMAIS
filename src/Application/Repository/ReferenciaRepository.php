<?php

namespace App\Application\Repository;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

class ReferenciaRepository
{
    private Connection $connection;
    private LoggerInterface $logger;

    public function __construct(Connection $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger = $logger;
    }

    public function criar(array $dados): int
    {
        try {
            // Se já existe uma referência ativa para este ID do Radar, desativa
            if (isset($dados['id_radar'])) {
                $this->desativarReferenciasAnteriores($dados['id_radar']);
            }

            $this->connection->insert('referencias_cobrancas', [
                'id_radar' => $dados['id_radar'],
                'id_cobmais' => $dados['id_cobmais'],
                'numero_acordo' => $dados['numero_acordo'],
                'status' => $dados['status'],
                'data_criacao' => date('Y-m-d H:i:s'),
                'ultima_atualizacao' => date('Y-m-d H:i:s'),
                'ativo' => true,
                'observacao' => $dados['observacao'] ?? null
            ]);
            
            $novoId = (int) $this->connection->lastInsertId();
            
            $this->logger->info('Nova referência criada', [
                'id' => $novoId,
                'id_radar' => $dados['id_radar'],
                'id_cobmais' => $dados['id_cobmais']
            ]);
            
            return $novoId;
        } catch (\Exception $e) {
            $this->logger->error('Erro ao criar referência', [
                'error' => $e->getMessage(),
                'dados' => $dados
            ]);
            
            throw new \Exception('Erro ao criar referência: ' . $e->getMessage());
        }
    }

    private function desativarReferenciasAnteriores(string $idRadar): void
    {
        try {
            $this->connection->update(
                'referencias_cobrancas',
                [
                    'ativo' => false,
                    'ultima_atualizacao' => date('Y-m-d H:i:s'),
                    'observacao' => 'Desativado devido à criação de nova referência'
                ],
                [
                    'id_radar' => $idRadar,
                    'ativo' => true
                ]
            );
        } catch (\Exception $e) {
            $this->logger->warning('Erro ao desativar referências anteriores', [
                'error' => $e->getMessage(),
                'id_radar' => $idRadar
            ]);
        }
    }

    public function buscarPorIdRadar(string $idRadar): array
    {
        try {
            $qb = $this->connection->createQueryBuilder();
            
            $result = $qb
                ->select('*')
                ->from('referencias_cobrancas')
                ->where('id_radar = :id_radar')
                ->andWhere('ativo = :ativo')
                ->setParameter('id_radar', $idRadar)
                ->setParameter('ativo', true)
                ->executeQuery()
                ->fetchAllAssociative();
            
            return $result;
        } catch (\Exception $e) {
            $this->logger->error('Erro ao buscar referência por ID Radar', [
                'error' => $e->getMessage(),
                'id_radar' => $idRadar
            ]);
            
            throw new \Exception('Erro ao buscar referência: ' . $e->getMessage());
        }
    }

    public function buscarPorIdCobmais(int $idCobmais): ?array
    {
        try {
            $qb = $this->connection->createQueryBuilder();
            
            $result = $qb
                ->select('*')
                ->from('referencias_cobrancas')
                ->where('id_cobmais = :id_cobmais')
                ->setParameter('id_cobmais', $idCobmais)
                ->executeQuery()
                ->fetchAssociative();
            
            return $result ?: null;
        } catch (\Exception $e) {
            $this->logger->error('Erro ao buscar referência por ID Cobmais', [
                'error' => $e->getMessage(),
                'id_cobmais' => $idCobmais
            ]);
            
            throw new \Exception('Erro ao buscar referência: ' . $e->getMessage());
        }
    }

    public function atualizarStatus(int $id, string $status, string $dataPagamento = null): bool
    {
        try {
            $dados = [
                'status' => $status,
                'ultima_atualizacao' => date('Y-m-d H:i:s')
            ];
            
            if ($dataPagamento) {
                $dados['data_pagamento'] = $dataPagamento;
            }
            
            $affected = $this->connection->update(
                'referencias_cobrancas',
                $dados,
                ['id' => $id]
            );
            
            if ($affected > 0 && $status === 'PAGO') {
                // Se foi pago, desativa todas as outras referências do mesmo radar
                $referencia = $this->buscarPorId($id);
                if ($referencia) {
                    $this->desativarOutrasReferencias($id, $referencia['id_radar']);
                }
            }
            
            return $affected > 0;
        } catch (\Exception $e) {
            $this->logger->error('Erro ao atualizar status da referência', [
                'error' => $e->getMessage(),
                'id' => $id,
                'status' => $status
            ]);
            
            throw new \Exception('Erro ao atualizar status: ' . $e->getMessage());
        }
    }

    private function buscarPorId(int $id): ?array
    {
        try {
            $qb = $this->connection->createQueryBuilder();
            
            $result = $qb
                ->select('*')
                ->from('referencias_cobrancas')
                ->where('id = :id')
                ->setParameter('id', $id)
                ->executeQuery()
                ->fetchAssociative();
            
            return $result ?: null;
        } catch (\Exception $e) {
            $this->logger->error('Erro ao buscar referência por ID', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
            
            return null;
        }
    }

    private function desativarOutrasReferencias(int $idAtual, string $idRadar): void
    {
        try {
            $this->connection->update(
                'referencias_cobrancas',
                [
                    'ativo' => false,
                    'ultima_atualizacao' => date('Y-m-d H:i:s'),
                    'observacao' => 'Desativado devido ao pagamento de outra referência'
                ],
                [
                    'id_radar' => $idRadar,
                    'id <> ?' => $idAtual,
                    'ativo' => true
                ]
            );
        } catch (\Exception $e) {
            $this->logger->warning('Erro ao desativar outras referências', [
                'error' => $e->getMessage(),
                'id_atual' => $idAtual,
                'id_radar' => $idRadar
            ]);
        }
    }
} 