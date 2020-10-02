<?php

namespace JRest\Controllers\Auth;

use JRest\Helpers\JResponse;
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

    /** @var \JRest\Services\Authen\Authen */
    protected $authen;

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
        $this->authen = $container->get('authen');
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
        $params = $request->getParams();
        $credentials = $params['data'];
        $channel = $params['option']['type'];
        

        $result = $this->authen->login($credentials, $channel);
        // return $result;
        if ($result['status']) {
            $user = User::find($result['uid']);
            $token = $this->authen->generateToken($user);
            return JResponse::success($response, array(
                "token" => $token,
                "user" => $user
            ), "Login success");
        } else {
            return JResponse::error($response, 400, [], $result['message']);
        }
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
