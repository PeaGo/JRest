<?php

namespace JRest\Services\Authen;

use Psr\Container\ContainerInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class AuthenServiceProvider implements ServiceProviderInterface
{

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple['authen'] = function (ContainerInterface $c) {

            return new Authen($c);
        };
    }
}
