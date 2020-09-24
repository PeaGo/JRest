<?php

namespace JRest\Services\Notification\Channels\Email;

use JRest\Helpers\JUtil;
use Namshi\Notificator\Notification\Handler\HandlerInterface;
use Namshi\Notificator\NotificationInterface;
use Namshi\Notificator\Exception\ExecutableNotFoundException;

class EmailNotificationHandler implements HandlerInterface
{
    protected $mailer;
    protected $sendmail_config;
    public function __construct()
    {
        $this->mailer = JUtil::getMailer();
        $this->sendmail_config = JUtil::getConfig('sendmail');
    }
    public function shouldHandle(NotificationInterface $notification)
    {
        return true;
    }

    public function handle($notification)
    {
        $from = array_key_exists('mailfrom', $this->sendmail_config) ? $this->sendmail_config['mailfrom'] : "";
        $fromName = array_key_exists('fromname', $this->sendmail_config) ? $this->sendmail_config['fromname'] : "";
        $toAddress = $notification->address;
        $body = $notification->body;
        $subject = $notification->subject;
        $this->mailer->sendMail($from, $fromName, $toAddress,  $subject, $body, true);
    }
}
