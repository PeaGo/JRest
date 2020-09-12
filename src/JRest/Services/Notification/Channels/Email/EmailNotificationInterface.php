<?php

namespace JRest\Services\Notification\Channels\Email;

use Namshi\Notificator\Notification;
use Namshi\Notificator\NotificationInterface;


class EmailNotificationInterface extends Notification
{
    public $address;
    public $body;
    public $subject;
    public $template = null;
    public function __construct($paramenters = null)
    {
        if (!empty($paramenters)) {
            array_key_exists('address', $paramenters) ?: $this->address  = $paramenters['address'];
            array_key_exists('body', $paramenters) ?: $this->body     = $paramenters['body'];
            array_key_exists('subject', $paramenters) ?: $this->subject  = $paramenters['subject'];
            array_key_exists('message', $paramenters) ?: $this->message  = $paramenters['message'];
            array_key_exists('template', $paramenters) ?: $this->template = $paramenters['template'];
        } else {
            // return parent::__construct('Email message');
            $this->address  = 'dongoctien97@gmail.com';
            $this->body     = 'Body';
            $this->subject  = 'Subject';
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

    public function getMessage()
    {
        return $this->message;
    }
    public function getTemplate()
    {
        return $this->template;
    }
}
