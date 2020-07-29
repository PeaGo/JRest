<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
date_default_timezone_set('UTC');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
    header("HTTP/1.1 200 OK");
    die();
}
require __DIR__ . '/vendor/autoload.php';
include './config.php';
include './route_config.php';

require __DIR__ . '/vendor/autoload.php';
include __DIR__ . '/helpers/JApi.php';
include __DIR__ . '/helpers/JLog.php';
include __DIR__ . '/helpers/JUpload.php';
include __DIR__ . '/helpers/JCommon.php';
include __DIR__ . '/helpers/JString.php';

use \Firebase\JWT\JWT;

use App\Model\Customer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use RKA\Middleware\IpAddress;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;



$app = AppFactory::create();
$app->setBasePath(BASE_PATH);
$app->addRoutingMiddleware();


$capsule = new Illuminate\Database\Capsule\Manager;
$capsule->addConnection($dbConfig);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

$app->add(function (Request $request, RequestHandlerInterface $handler) use ($app) {
    // var_dump($request->getUri()->getPath());
    $uri = str_replace(BASE_PATH, '', $request->getUri()->getPath());

    if (is_auth_route($uri)) {

        if (!isset($_SERVER['HTTP_AUTHORIZATION']) || $_SERVER['HTTP_AUTHORIZATION'] == '') {
            $response = $app->getResponseFactory()->createResponse(401, 'Authentication Error');
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'status_code' => 401,
                'message' => 'Missing token authorization'
            ]), JSON_UNESCAPED_UNICODE);
            return $response;
        }
        try {
            if (isset($_SERVER['HTTP_AUTHORIZATION']) && $_SERVER['HTTP_AUTHORIZATION'] != '') {
                $token_headers =  $_SERVER['HTTP_AUTHORIZATION'];
                $token_headers = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
                $decoded = JWT::decode($token_headers, 'myjwt@secr3t!', array('HS256'));
                $request = $request->withAttribute('decoded_token_data', $decoded);
            }
        } catch (\Throwable $th) {
            $response = $app->getResponseFactory()->createResponse(401, 'Authentication Error');
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'status_code' => 401,
                'message' => $th->getMessage()
            ]), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            return $response;
        }
    }

    $response = $handler->handle($request);
    return $response;
});

$app->add(function (Request $request, RequestHandlerInterface $handler) use ($app) {
    try {
        $starttime = microtime(true) * 1000;
        $response = $handler->handle($request);
        // $response->getBody()->write('<--->'.(round(microtime(true)*1000 - $starttime, 3)) .'<--->');
    } catch (\Throwable $th) {
        return $response;
    }
    return $response;
});



// $checkProxyHeaders = true; // Note: Never trust the IP address for security processes!
$trustedProxies = ['10.0.0.1', '10.0.0.2']; // Note: Never trust the IP address for security processes!
$app->add(new IpAddress(false, $trustedProxies));

$app->add(function (Request $request, RequestHandlerInterface $handler) {
    $response = $handler->handle($request);
    $response = $response->withHeader("Content-Type", "application/json");
    $response = $response->withHeader('Access-Control-Allow-Headers', '*');
    $response = $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    return $response;
});

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

$app->get('/agent', function (Request $request, Response $response, $args) {
});



// require route file
foreach (glob(APP_PATH . '/routes/*.php') as $filename) {
    require $filename;
}


$app->run();
