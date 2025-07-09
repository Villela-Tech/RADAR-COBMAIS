<?php

namespace App\Application\Repository;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class CobmaisRepository
{
    private Client $client;
    private LoggerInterface $logger;
    private string $apiUrl;
    private string $subscriptionKey;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->apiUrl = $_ENV['COBMAIS_API_URL'];
        $this->subscriptionKey = $_ENV['COBMAIS_SUBSCRIPTION_KEY'];
        
        $this->client = new Client([
            'base_uri' => $this->apiUrl,
            'headers' => [
                'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    public function criarAcordo(array $dados): array
    {
        try {
            $response = $this->client->post('/cobranca/acordos', [
                'json' => $dados
            ]);
            
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            $this->logger->error('Erro ao criar acordo no Cobmais', [
                'error' => $e->getMessage(),
                'dados' => $dados
            ]);
            
            throw new \Exception('Erro ao criar acordo no Cobmais: ' . $e->getMessage());
        }
    }

    public function consultarAcordo(int $idAcordo): array
    {
        try {
            $response = $this->client->get("/consulta/acordos/{$idAcordo}");
            
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            $this->logger->error('Erro ao consultar acordo no Cobmais', [
                'error' => $e->getMessage(),
                'id_acordo' => $idAcordo
            ]);
            
            throw new \Exception('Erro ao consultar acordo no Cobmais: ' . $e->getMessage());
        }
    }

    public function consultarCriticas(string $cpfCnpj): array
    {
        try {
            $response = $this->client->get("/carga/clientes/{$cpfCnpj}/criticas");
            
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            $this->logger->error('Erro ao consultar críticas no Cobmais', [
                'error' => $e->getMessage(),
                'cpf_cnpj' => $cpfCnpj
            ]);
            
            throw new \Exception('Erro ao consultar críticas no Cobmais: ' . $e->getMessage());
        }
    }
} 