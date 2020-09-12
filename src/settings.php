<?php

// Define root path
defined('DS') ?: define('DS', DIRECTORY_SEPARATOR);
defined('ROOT') ?: define('ROOT', dirname(__DIR__) . DS);

// Load .env file
if (file_exists(ROOT . '.env')) {
    $dotenv =  Dotenv\Dotenv::createImmutable(ROOT);
    $dotenv->load();
}


return [
    'settings' => [
        'displayErrorDetails'    => $_ENV['APP_DEBUG'] === 'true' ? true : false, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // App Settings
        'app'                    => [
            'name' =>  $_ENV['APP_NAME'],
            'url'  => $_ENV['APP_URL'],
            'env'  => $_ENV['APP_ENV'],
        ],

        // Renderer settings
        'renderer'               => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger'                 => [
            'name'  => $_ENV['APP_NAME'],
            'path'  => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // Database settings
        'database'               => [
            'driver'    => $_ENV['DB_CONNECTION'],
            'host'      => $_ENV['DB_HOST'],
            'database'  => $_ENV['DB_DATABASE'],
            'username'  => $_ENV['DB_USERNAME'],
            'password'  => $_ENV['DB_PASSWORD'],
            'port'      => $_ENV['DB_PORT'],
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],

        'cors' => null !== $_ENV['CORS_ALLOWED_ORIGINS'] ?  $_ENV['CORS_ALLOWED_ORIGINS'] : '*',

        // jwt settings
        'jwt'  => [
            'attribute' => 'token',
            'secret' => $_ENV['JWT_SECRET'],
            'secure' => false,
            "header" => "Authorization",
            // "regexp" => "/Token\s+(.*)$/i",
            'passthrough' => ['/api/jwt'],
            "error" => function ($response, $arguments) {
                $data['status_code'] = 401;
                $data["status"] = "error";
                $data["message"] = $arguments["message"];
                return $response
                    ->withHeader("Content-Type", "application/json")
                    ->getBody()
                    ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            }
        ],
    ],
];
