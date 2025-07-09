<?php

namespace App\Application\Controller;

use App\Application\Services\CobmaisService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

class CobmaisController
{
    private CobmaisService $cobmaisService;
    private LoggerInterface $logger;

    public function __construct(CobmaisService $cobmaisService, LoggerInterface $logger)
    {
        $this->cobmaisService = $cobmaisService;
        $this->logger = $logger;
    }

    public function criarCobranca(Request $request, Response $response): Response
    {
        try {
            $dados = $request->getParsedBody();
            
            $this->logger->info('Iniciando criação de cobrança no Cobmais', $dados);
            
            $resultado = $this->cobmaisService->criarAcordo($dados);
            
            $this->logger->info('Cobrança criada com sucesso no Cobmais', [
                'id_radar' => $dados['id_radar'] ?? null,
                'id_cobmais' => $resultado['id'] ?? null
            ]);
            
            return $this->jsonResponse($response, $resultado);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao criar cobrança no Cobmais', [
                'error' => $e->getMessage(),
                'dados' => $dados ?? null
            ]);
            
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function receberPagamento(Request $request, Response $response): Response
    {
        try {
            $dados = $request->getParsedBody();
            
            $this->logger->info('Recebendo notificação de pagamento do Cobmais', $dados);
            
            $resultado = $this->cobmaisService->processarPagamento($dados);
            
            $this->logger->info('Pagamento processado com sucesso', [
                'id_acordo' => $dados['id_acordo'] ?? null,
                'status' => $resultado['status'] ?? null
            ]);
            
            return $this->jsonResponse($response, $resultado);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao processar pagamento', [
                'error' => $e->getMessage(),
                'dados' => $dados ?? null
            ]);
            
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    private function jsonResponse(Response $response, array $data): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function errorResponse(Response $response, string $message): Response
    {
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
} 