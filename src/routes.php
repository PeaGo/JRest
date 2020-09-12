<?php

use JRest\Controllers\Auth\RegisterController;
use Slim\Http\Request;
use Slim\Http\Response;


// Api Routes
$app->group('/api', function () {
    /** @var \Slim\App $this */
    $jwtMiddleware = $this->getContainer()->get('jwt');
    $optionalAuth = $this->getContainer()->get('optionalAuth');

    // Auth Routes
    $this->post('/users/register', RegisterController::class . ':register')->setName('auth.register');
    $this->post('/users/login', \JRest\Controllers\Auth\LoginController::class . ':login')->setName('auth.login');

    // Welcome route
    $this->get('', \JRest\Controllers\WelcomeController::class . ':index')->setName('app.welcome');
    $this->group('/admin', function () {
        $this->get('', \JRest\Controllers\WelcomeController::class . ':index')->setName('app.admin.welcome');
    });
});


// Routes

$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("JRest '/' route");
    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
    // return 'Route not exist!';
});
