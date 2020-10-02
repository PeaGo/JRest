<?php

namespace JRest\Services\Authen;

use JRest\Models\User;
use DateTime;
use Error;
use Firebase\JWT\JWT;
use Illuminate\Database\Capsule\Manager;
use Psr\Container\ContainerInterface;
use Slim\Collection;
use Slim\Http\Request;

class Authen
{

    const SUBJECT_IDENTIFIER = 'id';

    /**
     * @var \Illuminate\Database\Capsule\Manager
     */
    private $db;
    /**
     * @var array
     */
    private $appConfig;

    private $container;

    protected $channel;

    /**
     * Auth constructor.
     *
     * @param \Illuminate\Database\Capsule\Manager $db
     * @param array|\Slim\Collection               $appConfig
     */
    public function __construct(ContainerInterface $container, string $channel = '')
    {
        $this->container = $container;
        $this->db = $container->get('db');
        $this->appConfig = $container->get('settings');
        $this->channel = $channel;
    }

    /**
     * Generate a new JWT token
     *
     * @param \JRest\Models\User $user
     *
     * @return string
     * @internal param string $subjectIdentifier The username of the subject user.
     *
     */
    public function generateToken(User $user)
    {
        $now = new DateTime();
        $future = new DateTime("now +7 days");

        $payload = [
            "iat" => $now->getTimeStamp(),
            "exp" => $future->getTimeStamp(),
            "jti" => base64_encode(random_bytes(16)),
            'iss' => $this->appConfig['app']['url'],  // Issuer
            "sub" => $user->{self::SUBJECT_IDENTIFIER},
        ];

        $secret = $this->appConfig['jwt']['secret'];
        $token = JWT::encode($payload, $secret, "HS256");

        return $token;
    }

    /**
     * Attempt to find the user based on email and verify password
     *
     * @param $email
     * @param $password
     *
     * @return bool|\JRest\Models\User
     */
    public function login($credentials, $channel)
    {
        if (empty($channel)) {
            throw new Error('Channel login supported : [email, facebook, phone]');
            return;
        }
        $authClass =  "\JRest\Services\Authen\Channels\\" . ucwords($channel) . "Authen";
        $authClass  = new $authClass($this->container);
        $result = $authClass->login($credentials);
        return $result;
    }

    public function register($credentials, $channel)
    {
        if (empty($channel)) {
            throw new Error('Channel login supported : [email, facebook, phone]');
            return;
        }
        $authClass =  "\JRest\Services\Authen\Channels\\" . ucwords($channel) . "Authen";
        $authClass  = new $authClass($this->container);
        $result = $authClass->register($credentials);
        return $result;
    }

    /**
     * Retrieve a user by the JWT token from the request
     *
     * @param \Slim\Http\Request $request
     *
     * @return User|null
     */
    public function requestUser(Request $request)
    {
        // Should add more validation to the present and validity of the token?
        // var_dump($request->getAttribute('token'));
        if ($token = $request->getAttribute('token')) {
            return User::where(static::SUBJECT_IDENTIFIER, '=', $token['sub'])->first();
        };
    }
}
