<?php

namespace JRest\Services\Notification\Channels\Push;

use Namshi\Notificator\Notification;
use Namshi\Notificator\NotificationInterface;


class PushNotificationInterface extends Notification
{

    public function __construct($paramenters = null)
    {
        if (!empty($paramenters)) {
        } else {
        }
    }
}
