<?php

namespace JRest\Services\Authen\Channels;

use JRest\Models\User;

class PhoneAuthen
{
    public function __construct()
    {
    }

    public function login($credentials)
    {

        // check account is exist in system
        $user = User::where('firebase_id', $credentials['firebase_id'])->first();
        if ($user) {
            // account exist
            $this->status_success = 1;
            $this->status_failure = 0;
            return array(
                "status" => true,
                "uid" => $user->id,
                // "message" => $this->message
            );
        } else {
            // account is not exist
            $this->status_success = 1;
            $this->status_failure = 0;

            // TODO add new user
            $new_user = array(
                "phone" => $credentials['phone'],
                "phone_code" => $credentials['phone_code'],
                "firebase_id" => $credentials['firebase_id'],
                "signup_type" => "PHONE"
            );
            $new_user = new User($new_user);
            $new_user->save();

            // $add_role = ;

            return array(
                "status" => true,
                "uid" => $new_user->id,
                // "message" => $this->message
            );
        }
    }

    public function signup()
    {
    }
}
