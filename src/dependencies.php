<?php
// DIC configuration

/** @var Pimple\Container $container */

$container = $app->getContainer();


// Error Handler
// $container['errorHandler'] = function ($c) {
//     return new \Conduit\Exceptions\ErrorHandler($c['settings']['displayErrorDetails']);
// };

// App Service Providers
$container->register(new \JRest\Services\Database\EloquentServiceProvider());
$container->register(new \JRest\Services\Auth\AuthServiceProvider());
$container->register(new \JRest\Services\Notification\NotificationServiceProvider());

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));

    return $logger;
};

// Jwt Middleware
$container['jwt'] = function ($c) {

    $jws_settings = $c->get('settings')['jwt'];

    return new \Tuupola\Middleware\JwtAuthentication($jws_settings);
};

$container['optionalAuth'] = function ($c) {
    return new JRest\Middleware\OptionalAuth($c);
};


// Request Validator
$container['validator'] = function ($c) {
    return new \JRest\Validation\Validator();
};

