<?php

namespace JRest\Middleware;

use Psr\Container\ContainerInterface;
use Slim\DeferredCallable;

class OptionalAuth
{

    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * OptionalAuth constructor.
     *
     * @param \Psr\Container\ContainerInterface $container
     *
     * @internal param \Slim\Middleware\JwtAuthentication $jwt
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * OptionalAuth middleware invokable class to verify JWT token when present in Request
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable             r                    $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        if ($request->hasHeader('HTTP_AUTHORIZATION')) {

            $callable = new DeferredCallable($this->container->get('jwt'), $this->container);

            // return call_user_func($callable, $request, $response, $next);
            // var_dump('ok');
            var_dump($request->getHeader('Authorization')[0]);
        }
        // var_dump('ko');

        return $next($request, $response);
    }
}
