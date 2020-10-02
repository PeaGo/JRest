<?php

namespace JRest\Controllers\Auth;

use JRest\Helpers\JResponse;
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
     * @var \JRest\Services\Authen\Authen
     */
    protected $authen;

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
        $this->authen = $container->get('authen');
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
        $credentials = $request->getParam('data');
        $channel = $request->getParam('option')['type'];
        // $validation = $this->validateRegisterRequest($credentials);

        // if ($validation->failed()) {
        //     return $response->withJson(['errors' => $validation->getErrors()], 422);
        // }
        // $user->token = $this->auth->generateToken($user);
        // $user->password = password_hash($userParams['password'], PASSWORD_DEFAULT);
        // $user->save();

        $register = $this->authen->register($credentials, $channel);
        if ($register['status']) {
            $user = User::find($register['uid']);
            return JResponse::success($response, $user, $register['message']);
        } else {
            return  JResponse::error($response, 400, [], $register['message']);
        }
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
                // 'username' => V::noWhitespace()->notEmpty()->existsInTable($this->db->table('users'), 'username'),
                'password' => V::noWhitespace()->notEmpty(),
            ]
        );
    }
}
