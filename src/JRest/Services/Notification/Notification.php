<?php

namespace JRest\Services\Notification;

use Error;
use JRest\Models\Notification_Object;
use Psr\Container\ContainerInterface;
use Namshi\Notificator\Notification\Handler\NotifySend as NotifySendHandler;
use Namshi\Notificator\Manager;
use Namshi\Notificator\Notification\NotifySend\NotifySendNotification;

class Notification
{

    protected $db;
    protected $support_channels = ['email', 'push'];
    public function __construct(ContainerInterface $container)
    {
        $this->db = $container->get('db');
    }
    // public function trigger(array $channels, $receipents = null, string $template = null, $item_id = null, $persistent = true, $paramenters = array())
    public function trigger(array $params)
    {
        $channels = $params['channels'];
        $receipents = $params['receipents'];
        $template = isset($params['template']) ? $params['template'] : null;
        $item_id = isset($params['item_id']) ? $params['item_id'] : null;
        $persistent = isset($params['persistent']) ? $params['persistent'] : true;
        $customs = isset($params['customs']) ? $params['customs'] : [];

        // valid params
        if (empty($channels)) {
            throw new Error('Channels must be required.');
            return;
        }
        if (empty($receipents)) {
            throw new Error('Receipents must be required.');
            return;
        }
        foreach ($channels as $channel) {
            if (!in_array($channel, $this->support_channels)) {
                throw new Error('Channels ' . $channel . ' is not supported.');
                return;
            }
        }
        foreach ($channels as $channel) {
            $n_obj = new Notification_Object([
                'type' => $channel,
                'item_id' => $item_id,
                'template_code' => $template,
                'persistent' => $persistent ? 1 : 0
            ]);
            $n_obj->save();
            $manager = new Manager();
            $handlerClass = "\JRest\Services\Notification\Channels\\" . ucwords($channel) . "\\" . ucwords($channel) . "NotificationHandler";
            $handler = new $handlerClass();
            $manager->addHandler($handler);
            $notificationInterfaceClass = "\JRest\Services\Notification\Channels\\" . ucwords($channel) . "\\" . ucwords($channel) . "Notification";
            if (array_key_exists($channel, $customs)) {
                $notification = new $notificationInterfaceClass($receipents, $n_obj->id, $customs[$channel]);
            } else {
                $notification = new $notificationInterfaceClass($receipents, $n_obj->id, $item_id);
            }
            $manager->trigger($notification);
        }

        // $notification = new NotifySendNotification("...whatever message... hahah");
        // $manager->trigger($notification);
    }
}
