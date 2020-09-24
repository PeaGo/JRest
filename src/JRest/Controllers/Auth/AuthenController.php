<?php

namespace JRest\Controllers\Auth;

use JRest\Helper\JResponse;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class AuthenController
{

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->db = $container->get('db');
        $this->validator = $container->get('validator');
        $this->auth = $container->get('auth');
    }

    public function auth(Request $request, Response $response, array $args)
    {
        try {
            //code...
            $user = $this->auth->requestUser($request);
            $new_token = $this->auth->generateToken($user);
            return JResponse::success($response, ['token' => $new_token, 'user' => $user], 'Authenticate success');
        } catch (\Throwable $th) {
            //throw $th;
            return JResponse::err401($response, [], $th->getMessage());
        }
    }
}
