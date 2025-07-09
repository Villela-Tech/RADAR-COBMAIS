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
            $this->connection->insert('referencias_cobrancas', [
                'id_radar' => $dados['id_radar'],
                'id_cobmais' => $dados['id_cobmais'],
                'numero_acordo' => $dados['numero_acordo'],
                'status' => $dados['status'],
                'data_criacao' => date('Y-m-d H:i:s'),
                'ultima_atualizacao' => date('Y-m-d H:i:s')
            ]);
            
            return (int) $this->connection->lastInsertId();
        } catch (\Exception $e) {
            $this->logger->error('Erro ao criar referência', [
                'error' => $e->getMessage(),
                'dados' => $dados
            ]);
            
            throw new \Exception('Erro ao criar referência: ' . $e->getMessage());
        }
    }

    public function buscarPorIdRadar(string $idRadar): ?array
    {
        try {
            $qb = $this->connection->createQueryBuilder();
            
            $result = $qb
                ->select('*')
                ->from('referencias_cobrancas')
                ->where('id_radar = :id_radar')
                ->setParameter('id_radar', $idRadar)
                ->executeQuery()
                ->fetchAssociative();
            
            return $result ?: null;
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
} 