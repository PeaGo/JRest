<?php

namespace JRest\Services\Notification;

use Psr\Container\ContainerInterface;
use Namshi\Notificator\Notification\Handler\NotifySend as NotifySendHandler;
use Namshi\Notificator\Manager;
use Namshi\Notificator\Notification\NotifySend\NotifySendNotification;

class Notification
{

    protected $db;
    public function __construct(ContainerInterface $container)
    {
        $this->db = $container->get('db');
    }
    public function trigger(array $channels, $receipents = null, $paramenters = array())
    {
        foreach ($channels as $channel) {
            $manager = new Manager();
            $handlerClass = "\JRest\Services\Notification\Channels\\" . ucwords($channel) . "\\" . ucwords($channel) . "NotificationHandler";
            $handler = new $handlerClass();
            $manager->addHandler($handler);
            $notificationInterfaceClass = "\JRest\Services\Notification\Channels\\" . ucwords($channel) . "\\" . ucwords($channel) . "NotificationInterface";
            if (array_key_exists($channel, $paramenters)) {
                $notification = new $notificationInterfaceClass($paramenters[$channel]);
            } else {
                $notification = new $notificationInterfaceClass();
            }
            $manager->trigger($notification);
        }
        // $notification = new NotifySendNotification("...whatever message... hahah");
        // $manager->trigger($notification);
    }
}
