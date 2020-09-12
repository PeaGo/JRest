<?php
namespace JRest\Services\Notification;

use Psr\Container\ContainerInterface;

class Notification {
    
    protected $db;
    public function __construct(ContainerInterface $container)
    {
        // var_dump($container);
    }
}