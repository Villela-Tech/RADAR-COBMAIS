<?php

use App\Application\Controller\CobmaisController;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (App $app) {
    // Rota raiz para health check
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write(json_encode([
            'status' => 'online',
            'service' => 'Radar-Cobmais Integration',
            'version' => '1.0.0'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->group('/api', function (RouteCollectorProxy $group) {
        // Rotas para integração com Cobmais
        $group->group('/cobmais', function (RouteCollectorProxy $group) {
            // Criar cobrança no Cobmais
            $group->post('/cobrancas', [CobmaisController::class, 'criarCobranca']);
            
            // Webhook para receber notificações de pagamento
            $group->post('/webhook/pagamentos', [CobmaisController::class, 'receberPagamento']);
        });
    });
}; 