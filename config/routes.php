<?php

use App\Application\Controller\CobmaisController;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
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