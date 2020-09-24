<?php

use JRest\Controllers\Auth\AuthenController;
use JRest\Controllers\Auth\RegisterController;
use JRest\Controllers\UserController;
use Slim\Http\Request;
use Slim\Http\Response;


// Api Routes
$app->group('/api/v1', function () {
    /** @var \Slim\App $this */
    $jwtMiddleware = $this->getContainer()->get('jwt');
    $optionalAuth = $this->getContainer()->get('optionalAuth');

    // Auth Routes
    $this->group('/auth', function () use ($jwtMiddleware) {
        $this->get('', AuthenController::class . ':auth')->add($jwtMiddleware)
            ->setName('auth.getAuth');

        $this->post('/register', RegisterController::class . ':register')->setName('auth.register');
        $this->post('/login', \JRest\Controllers\Auth\LoginController::class . ':login')->setName('auth.login');
    });

    // Users
    $this->group('/users', function () {
        $this->get('', UserController::class . ':index')->setName('users.list');
        $this->get('/{id}', UserController::class . ':detail')->setName('users.detail');
        $this->post('', UserController::class . ':create')->setName('users.create');
        $this->put('/{id}', UserController::class . ':update')->setName('users.update');
        $this->delete('', UserController::class . ':deleteMany')->setName('users.deleteMany');
        $this->delete('/{id}', UserController::class . ':delete')->setName('users.delete');
    });


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
