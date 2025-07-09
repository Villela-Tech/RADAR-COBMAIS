<?php

namespace App\Application\Repository;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class RadarRepository
{
    private Client $client;
    private LoggerInterface $logger;
    private string $apiUrl;
    private string $apiKey;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->apiUrl = $_ENV['RADAR_API_URL'];
        $this->apiKey = $_ENV['RADAR_API_KEY'];
        
        $this->client = new Client([
            'base_uri' => $this->apiUrl,
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    public function atualizarStatus(array $dados): array
    {
        try {
            $response = $this->client->post('/api/cobrancas/atualizar-status', [
                'json' => [
                    'id_cobranca' => $dados['id_radar'],
                    'status' => $dados['status'],
                    'data_pagamento' => $dados['data_pagamento'],
                    'valor_pago' => $dados['valor_pago']
                ]
            ]);
            
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            $this->logger->error('Erro ao atualizar status no Radar', [
                'error' => $e->getMessage(),
                'dados' => $dados
            ]);
            
            throw new \Exception('Erro ao atualizar status no Radar: ' . $e->getMessage());
        }
    }

    public function consultarCobranca(string $idCobranca): array
    {
        try {
            $response = $this->client->get("/api/cobrancas/{$idCobranca}");
            
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            $this->logger->error('Erro ao consultar cobrança no Radar', [
                'error' => $e->getMessage(),
                'id_cobranca' => $idCobranca
            ]);
            
            throw new \Exception('Erro ao consultar cobrança no Radar: ' . $e->getMessage());
        }
    }
} 