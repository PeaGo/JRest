<?php

namespace JRest\Services\Notification;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Psr\Container\ContainerInterface;

class NotificationServiceProvider implements ServiceProviderInterface
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
        $pimple['notification'] = function (ContainerInterface $c) {

            return new Notification($c);
        };
    }
}
