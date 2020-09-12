<?php

namespace JRest\Controllers\Auth;

use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use JRest\Models\User;
use Respect\Validation\Validator as v;

class LoginController
{

    /**
     * @var \Interop\Container\ContainerInterface
     */
    protected $container;

    /** @var \Illuminate\Database\Capsule\Manager */
    protected $db;

    /** @var \JRest\Services\Auth\Auth */
    protected $auth;

    /** @var \JRest\Validation\Validator */
    protected $validator;



    /**
     * BaseController constructor.
     *
     * @param \Interop\Container\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->auth = $container->get('auth');
        $this->db = $container->get('db');
        $this->validator = $container->get('validator');
    }


    /**
     * Return token after successful login
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     *
     * @return \Slim\Http\Response
     */
    public function login(Request $request, Response $response)
    {
        $userParams = $request->getParams();
        $validation = $this->validateLoginRequest($userParams = $request->getParams());

        if ($validation->failed()) {
            return $response->withJson(['errors' => ['email or password' => ['is invalid']]], 422);
        }

        if ($user = $this->auth->attempt($userParams['email'], $userParams['password'])) {
            $user->token = $this->auth->generateToken($user);
            // $data = $this->fractal->createData(new Item($user, new UserTransformer()))->toArray();

            return $response->withJson(['user' => $user]);
        };

        return $response->withJson(['errors' => ['email or password' => ['is invalid']]], 422);
    }

    /**
     * @param array
     *
     * @return \Conduit\Validation\Validator
     */
    protected function validateLoginRequest($values)
    {
        return $this->validator->validateArray(
            $values,
            [
                'email'    => v::noWhitespace()->notEmpty(),
                'password' => v::noWhitespace()->notEmpty(),
            ]
        );
    }
}
