<?php

use App\Application\Controller\CobmaisController;
use App\Application\Repository\CobmaisRepository;
use App\Application\Repository\RadarRepository;
use App\Application\Repository\ReferenciaRepository;
use App\Application\Services\CobmaisService;
use DI\ContainerBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        // Logger
        LoggerInterface::class => function () {
            $logger = new Logger('app');
            $logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));
            return $logger;
        },
        
        // Database
        Connection::class => function () {
            return DriverManager::getConnection([
                'driver' => 'pdo_mysql',
                'host' => $_ENV['DB_HOST'],
                'port' => $_ENV['DB_PORT'],
                'dbname' => $_ENV['DB_DATABASE'],
                'user' => $_ENV['DB_USERNAME'],
                'password' => $_ENV['DB_PASSWORD'],
                'charset' => 'utf8mb4'
            ]);
        },
        
        // Repositories
        CobmaisRepository::class => function (ContainerInterface $c) {
            return new CobmaisRepository($c->get(LoggerInterface::class));
        },
        
        RadarRepository::class => function (ContainerInterface $c) {
            return new RadarRepository($c->get(LoggerInterface::class));
        },
        
        ReferenciaRepository::class => function (ContainerInterface $c) {
            return new ReferenciaRepository(
                $c->get(Connection::class),
                $c->get(LoggerInterface::class)
            );
        },
        
        // Services
        CobmaisService::class => function (ContainerInterface $c) {
            return new CobmaisService(
                $c->get(CobmaisRepository::class),
                $c->get(RadarRepository::class),
                $c->get(ReferenciaRepository::class),
                $c->get(LoggerInterface::class)
            );
        },
        
        // Controllers
        CobmaisController::class => function (ContainerInterface $c) {
            return new CobmaisController(
                $c->get(CobmaisService::class),
                $c->get(LoggerInterface::class)
            );
        }
    ]);
}; 