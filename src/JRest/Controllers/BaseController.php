<?php

namespace JRest\Controllers;

use Psr\Container\ContainerInterface;

class BaseController
{

    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * BaseController constructor.
     *
     * @param \Psr\Container\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

}