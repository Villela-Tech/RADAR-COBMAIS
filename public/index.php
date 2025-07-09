<?php

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

// Carregar variáveis de ambiente
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Configurar container
$containerBuilder = new ContainerBuilder();

// Adicionar definições do container
$dependencies = require __DIR__ . '/../config/container.php';
$dependencies($containerBuilder);

// Construir container
$container = $containerBuilder->build();

// Criar aplicação
AppFactory::setContainer($container);
$app = AppFactory::create();

// Configurar base path
$app->setBasePath('/radar-cobmais');

// Adicionar middleware para parsing de JSON
$app->addBodyParsingMiddleware();

// Configurar rotas
$routes = require __DIR__ . '/../config/routes.php';
$routes($app);

// Adicionar middleware de tratamento de erros
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Executar aplicação
$app->run(); 