<?php

namespace JRest\Services\Notification\Channels\Push;

use JRest\Models\Notification_Object;
use Namshi\Notificator\Notification;


class PushNotification extends Notification
{

    public $body;
    public $title;
    public $subject;
    public $data = array();
    public $receipents = [];
    public $notification_obj = null;
    public $use_template = true;
    public function __construct($receipents, $no_obj, $paramenters = null)
    {
        $this->receipents = $receipents;
        $obj = Notification_Object::find($no_obj);
        $this->notification_obj = $obj;
        $this->template = $obj->template_code;
        $this->item_id = $obj->item_id;

        if (isset($paramenters['data'])) {
            $this->data = $paramenters['data'];
        }
        if (!empty($paramenters)) {
            $this->use_template = false;
        }
        if (empty($this->template)) {
            $this->title  = $paramenters['title'];
            $this->body     = $paramenters['body'];
            $this->subject  = $paramenters['subject'];
        } else {
            // $tmlp = Notification_Tmpl::where('code', $template);
        }
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getTemplate()
    {
        return $this->template;
    }
    public function getTitle()
    {
        return $this->title;
    }
}
