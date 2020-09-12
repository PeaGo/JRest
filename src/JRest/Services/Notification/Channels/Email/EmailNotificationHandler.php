<?php

namespace JRest\Services\Notification\Channels\Email;

use JRest\Helpers\JUtil;
use Namshi\Notificator\Notification\Handler\HandlerInterface;
use Namshi\Notificator\NotificationInterface;
use Namshi\Notificator\Exception\ExecutableNotFoundException;

class EmailNotificationHandler implements HandlerInterface
{
    protected $mailer;
    public function __construct()
    {
        $this->mailer = JUtil::getMailer();
    }
    public function shouldHandle(NotificationInterface $notification)
    {
        return true;
    }

    public function handle(NotificationInterface $notification)
    {
        $toAddress = $notification->address;
        $body = $notification->body;
        $subject = $notification->subject;
        $this->mailer->sendMail('peagoflash@gmail.com', 'Peago', $toAddress,  $subject , $body, true);
    }
}
