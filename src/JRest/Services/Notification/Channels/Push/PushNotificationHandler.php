<?php

namespace JRest\Services\Notification\Channels\Push;

use JRest\Helpers\JUtil;
use Namshi\Notificator\Notification\Handler\HandlerInterface;
use Namshi\Notificator\NotificationInterface;
use Namshi\Notificator\Exception\ExecutableNotFoundException;

class PushNotificationHandler implements HandlerInterface
{
    protected $pusher;
    public function __construct()
    {
        $this->pusher = JUtil::getMailer();
    }
    public function shouldHandle(NotificationInterface $notification)
    {
        return true;
    }

    public function handle(NotificationInterface $notification)
    {
      
    }
}
