<?php

namespace JRest\Services\Authen\Channels;

use Error;
use JRest\Models\User;

class FacebookAuthen
{
    public $status_success = 0;
    public $status_failure = 0;
    public $message = '';
    public $uid = null;
    protected $credentials = [];
    protected $fb_uid = null;
    protected $fb_acc_data = null;


    public function __construct()
    {
    }

    public function login($credentials)
    {
        $isValidFBAccount = $this->FbAPIService($credentials['token']);
        if (!$isValidFBAccount) {
            // throw new Error($this->message);
            $this->status_success = 0;
            $this->status_failure = 1;
            return array(
                "status" => false,
                "message" => $this->message
            );
        }
        // check account is exist in system
        $user = User::where('facebook_id', $this->fb_uid)->first();
        if ($user) {
            // account exist
            $this->status_success = 1;
            $this->status_failure = 0;
            return array(
                "status" => true,
                "uid" => $user->id,
                "message" => $this->message
            );
        } else {
            // account is not exist
            $this->status_success = 1;
            $this->status_failure = 0;

            // TODO add new user
            $names = explode(" ", $this->fb_acc_data, 2);
            $new_user = array(
                "firstname" => $names[0],
                "lastname" => $names[1],
                "facebook_id" => $this->fb_uid,
                "signup_type" => "FACEBOOK"
            );
            $new_user = new User($new_user);
            $new_user->save();

            // $add_role = ;

            return array(
                "status" => true,
                "uid" => $new_user->id,
                "message" => $this->message
            );
        }
    }

    public function signup()
    {
    }

    private function FbAPIService($token)
    {
        $url = 'https://graph.facebook.com/v4.0/me?access_token=' . $token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5000);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $data_ = json_decode(curl_exec($ch));
        curl_close($ch);
        if (empty($data_->id)) {
            $this->message = 'Not found user from Facebook API Service';
            return false;
        } else {
            $this->fb_uid = $data_->id;
            $this->fb_acc_data = $data_->name;
            return true;
        }
    }
}
