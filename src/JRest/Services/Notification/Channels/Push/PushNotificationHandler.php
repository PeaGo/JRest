<?php

namespace JRest\Services\Notification\Channels\Push;

use JRest\Helpers\JUtil;
use JRest\Helpers\TemplateBuilder;
use JRest\Models\Notification;
use JRest\Models\User;
use Namshi\Notificator\Notification\Handler\HandlerInterface;
use Namshi\Notificator\NotificationInterface;
use Namshi\Notificator\Exception\ExecutableNotFoundException;

class PushNotificationHandler implements HandlerInterface
{
    protected $pusher;
    public function __construct()
    {
        $this->pusher = JUtil::getOnesignal();
    }
    public function shouldHandle(NotificationInterface $notification)
    {
        return true;
    }

    public function handle(NotificationInterface $notification)
    {
        foreach ($notification->receipents as $receipent) {
            $playerid = '';
            $user = User::find($receipent);
            if (empty($user)) continue;
            if ($user) {
                if ($notification->template) {

                    $tmpl = new TemplateBuilder($notification->template, $receipent, $notification->item_id);

                    // $body = $tmpl->getBodyEmail();
                }
                $playerid = ['e0c52db8-aa30-412a-b0dd-c8cf334ce07e'];
            }
            if ($playerid) {
                if ($notification->notification_obj->persistent) {
                    Notification::insert([
                        'cid' => $user->id,
                        'message' => $notification->body,
                        'object_id' => $notification->notification_obj->id
                    ]);
                }
                $this->pusher->notify($playerid, [
                    'title' => $notification->title,
                    'body' => $notification->body
                ]);
            }
        }
    }
}
