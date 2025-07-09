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
    private string $base;
    private string $user;
    private string $password;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->apiUrl = $_ENV['RADAR_URL'] ?? '';
        $this->base = $_ENV['RADAR_BASE'] ?? '';
        $this->user = $_ENV['RADAR_USER'] ?? '';
        $this->password = $_ENV['RADAR_PASSWORD'] ?? '';
        
        $this->client = new Client([
            'base_uri' => $this->apiUrl,
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'auth' => [$this->user, $this->password]
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
                    'valor_pago' => $dados['valor_pago'],
                    'base' => $this->base
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
            $response = $this->client->get("/api/cobrancas/{$idCobranca}", [
                'query' => [
                    'base' => $this->base
                ]
            ]);
            
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