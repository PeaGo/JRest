<?php

namespace JRest\Services\Loyalty;

use Psr\Container\ContainerInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class LoyaltyServiceProvider implements ServiceProviderInterface
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
        $pimple['loyalty'] = function (ContainerInterface $c) {

            return new Loyalty($c);
        };
    }
}
