<?php

namespace JRest\Controllers\Auth;

use JRest\Models\User;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as V;

class RegisterController
{

    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * @var \Illuminate\Database\Capsule\Manager 
     */
    protected $db;

    /**
     * @var \JRest\Services\Auth\Auth
     */
    protected $auth;

    /**
     * @var \JRest\Validation\Validator
     */
    protected $validator;

    /**
     * BaseController constructor.
     *
     * @param \Psr\Container\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->db = $container->get('db');
        $this->auth = $container->get('auth');
        $this->validator = $container->get('validator');
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param Response $response
     * 
     * @return Response
     */
    public function register(Request $request, Response $response)
    {
        $validation = $this->validateRegisterRequest($userParams = $request->getParams());

        if ($validation->failed()) {
            return $response->withJson(['errors' => $validation->getErrors()], 422);
        }
        $user = new User($userParams = $request->getParsedBody());
        // $user->token = $this->auth->generateToken($user);
        $user->password = password_hash($userParams['password'], PASSWORD_DEFAULT);
        $user->save();

        // $resource = new Item($user, new UserTransformer());
        // $user = $this->fractal->createData($resource)->toArray();

        return $response->withJson(
            [
                'token' => $this->auth->generateToken($user),
                'user' => $user,
            ]
        );
    }

    /**
     *
     * @param array
     * 
     * @return \JRest\Validation\Validator
     */
    protected function validateRegisterRequest($values)
    {
        return $this->validator->validateArray(
            $values,
            [
                'email'    => V::noWhitespace()->notEmpty()->email()->existsInTable($this->db->table('users'), 'email'),
                'username' => V::noWhitespace()->notEmpty()->existsInTable($this->db->table('users'), 'username'),
                'password' => V::noWhitespace()->notEmpty(),
            ]
        );
    }
}
