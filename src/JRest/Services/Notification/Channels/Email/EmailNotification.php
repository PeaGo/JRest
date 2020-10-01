<?php

namespace JRest\Services\Notification\Channels\Email;

use JRest\Models\Notification_Object;
use JRest\Models\Notification_Tmpl;
use Namshi\Notificator\Notification;


class EmailNotification extends Notification
{
    public $address;
    public $body;
    public $title;
    public $subject;
    public $receipents = [];
    public $notification_obj = null;
    public function __construct($receipents, $no_obj, $paramenters = null)
    {
        $this->receipents = $receipents;
        $obj = Notification_Object::find($no_obj);
        $this->notification_obj = $obj;
        $this->template = $obj->template_code;
        $this->item_id = $obj->item_id;
        if (empty($this->template)) {
            $this->title  = $paramenters['title'];
            $this->body     = $paramenters['body'];
            $this->subject  = $paramenters['subject'];
        } else {
            // $tmlp = Notification_Tmpl::where('code', $template);
        }
    }

    public function getAddress()
    {
        return $this->address;
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
}
