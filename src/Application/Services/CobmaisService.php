<?php

namespace App\Application\Services;

use App\Application\Repository\CobmaisRepository;
use App\Application\Repository\RadarRepository;
use App\Application\Repository\ReferenciaRepository;
use Psr\Log\LoggerInterface;

class CobmaisService
{
    private CobmaisRepository $cobmaisRepository;
    private RadarRepository $radarRepository;
    private ReferenciaRepository $referenciaRepository;
    private LoggerInterface $logger;

    public function __construct(
        CobmaisRepository $cobmaisRepository,
        RadarRepository $radarRepository,
        ReferenciaRepository $referenciaRepository,
        LoggerInterface $logger
    ) {
        $this->cobmaisRepository = $cobmaisRepository;
        $this->radarRepository = $radarRepository;
        $this->referenciaRepository = $referenciaRepository;
        $this->logger = $logger;
    }

    public function criarAcordo(array $dadosRadar): array
    {
        try {
            // Converter dados do Radar para formato do Cobmais
            $dadosCobmais = $this->converterParaFormatoCobmais($dadosRadar);
            
            // Criar acordo no Cobmais
            $acordoCobmais = $this->cobmaisRepository->criarAcordo($dadosCobmais);
            
            // Salvar referência cruzada com mais detalhes
            $this->referenciaRepository->criar([
                'id_radar' => $dadosRadar['id_radar'],
                'id_cobmais' => $acordoCobmais['id'],
                'numero_acordo' => $acordoCobmais['numero'],
                'status' => 'PENDENTE',
                'data_criacao' => date('Y-m-d H:i:s'),
                'ultima_atualizacao' => date('Y-m-d H:i:s')
            ]);
            
            $this->logger->info('Acordo criado com sucesso', [
                'id_radar' => $dadosRadar['id_radar'],
                'id_cobmais' => $acordoCobmais['id'],
                'numero_acordo' => $acordoCobmais['numero']
            ]);
            
            return $acordoCobmais;
        } catch (\Exception $e) {
            $this->logger->error('Erro ao criar acordo', [
                'error' => $e->getMessage(),
                'dados_radar' => $dadosRadar
            ]);
            throw $e;
        }
    }

    private function converterParaFormatoCobmais(array $dadosRadar): array
    {
        return [
            'cpfcnpj' => $dadosRadar['cpf_cnpj'],
            'data_calculo' => date('Y-m-d'),
            'quantidade_parcelas' => $dadosRadar['num_parcelas'] ?? 1,
            'valor_entrada' => 0.00,
            'forma_pagamento' => 'Boleto',
            'parcelas_originais' => [
                [
                    'id_contrato' => $dadosRadar['id_radar'],
                    'numero_contrato' => $dadosRadar['numero_contrato'],
                    'id_negociacao' => $dadosRadar['id_negociacao'] ?? 1,
                    'valor' => $dadosRadar['valor'],
                    'vencimento' => $dadosRadar['data_vencimento']
                ]
            ],
            'descontos' => [
                'desconto_maximo' => false,
                'principal' => $dadosRadar['desconto_principal'] ?? 0,
                'multa' => $dadosRadar['desconto_multa'] ?? 0,
                'juros' => $dadosRadar['desconto_juros'] ?? 0,
                'honorarios' => $dadosRadar['desconto_honorarios'] ?? 0
            ],
            // Adicionando campos de referência extras
            'dados_adicionais' => [
                [
                    'nome' => 'id_radar',
                    'valor' => $dadosRadar['id_radar']
                ],
                [
                    'nome' => 'origem_cobranca',
                    'valor' => 'RADAR'
                ],
                [
                    'nome' => 'data_criacao_radar',
                    'valor' => $dadosRadar['data_criacao'] ?? date('Y-m-d H:i:s')
                ],
                [
                    'nome' => 'numero_contrato_radar',
                    'valor' => $dadosRadar['numero_contrato']
                ]
            ]
        ];
    }

    public function processarPagamento(array $dadosPagamento): array
    {
        // Buscar referência
        $referencia = $this->referenciaRepository->buscarPorIdCobmais($dadosPagamento['id_acordo']);
        
        if (!$referencia) {
            throw new \Exception('Acordo não encontrado no sistema Radar');
        }
        
        // Atualizar status no Radar
        $resultadoRadar = $this->radarRepository->atualizarStatus([
            'id_radar' => $referencia['id_radar'],
            'status' => 'PAGO',
            'data_pagamento' => $dadosPagamento['data_pagamento'],
            'valor_pago' => $dadosPagamento['valor_pagamento']
        ]);
        
        // Atualizar referência
        $this->referenciaRepository->atualizarStatus(
            $referencia['id'],
            'PAGO',
            $dadosPagamento['data_pagamento']
        );
        
        return $resultadoRadar;
    }
} 