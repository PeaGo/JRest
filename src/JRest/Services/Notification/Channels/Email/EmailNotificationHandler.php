<?php

namespace JRest\Services\Notification\Channels\Email;

use JRest\Helpers\JUtil;
use JRest\Helpers\TemplateBuilder;
use JRest\Models\Notification;
use JRest\Models\User;
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

        foreach ($notification->receipents as $receipent) {
            $user = User::find($receipent);
            if (empty($user) || empty($user->email)) continue;
            if ($user && $user->email) {
                if ($notification->template) {

                    $tmpl = new TemplateBuilder($notification->template, $receipent, $notification->item_id);

                    $body = $tmpl->getBodyEmail();
                }
                $toAddress = $user->email;
            }
            if ($toAddress) {
                if ($notification->notification_obj->persistent) {
                    Notification::insert([
                        'cid' => $user->id,
                        'message' => $body,
                        'object_id' => $notification->notification_obj->id
                    ]);
                }
                $this->mailer->sendMail($from, $fromName, $toAddress,  $subject, $body, true);
            }
        }
    }
}
