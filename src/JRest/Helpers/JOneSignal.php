<?php

namespace JRest\Helpers;

use Psr\Container\ContainerInterface;

class JOneSignal
{
    /** @var array */
    protected $appConfigOnesignal;

    public function __construct(ContainerInterface $container)
    {
        var_dump($container->get('settings'));
    }
}
