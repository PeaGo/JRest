<?php

namespace JRest\Services\Authen\Channels;

use Error;
use JRest\Helpers\JResponse;
use JRest\Models\User;
use Psr\Container\ContainerInterface;
use Respect\Validation\Validator as V;

class EmailAuthen
{
    public $status_success = 0;
    public $status_failure = 0;
    public $message = '';
    public $uid = null;
    protected $fb_uid = null;
    protected $fb_acc_data = null;
    protected $container;
    public function __construct()
    {
    }

    public function login($credentials)
    {
        $attemp = User::where('email', '=', $credentials['email'])->first();
        if ($attemp) {
            $match = password_verify($credentials['password'], $attemp->password);
            if ($match === true) {
                $this->status_success = 1;
                $this->status_failure = 0;
                return array(
                    "status" => true,
                    "uid" => $attemp->id,
                    "message" => $this->message
                );
            } else {
                $this->status_success = 0;
                $this->status_failure = 1;
                return array(
                    "status" => false,
                    "message" => "Authentication Failed!",
                );
            }
        } else if (empty($attemp)) {
            $this->status_success = 0;
            $this->status_failure = 1;
            return array(
                "status" => false,
                "message" => "Account doesn't exist",
            );
        }
    }

    public function register($credentials)
    {

        // Firebase register
        // $firebase_user =  JFirebase::createUser([
        //     'email' => $credentials['email'],
        //     'emailVerified' => false,
        //     'password' => $credentials['password'],
        // ]);
        // $credentials['firebase_id'] = "";
        $credentials["signup_type"] =  "EMAIL";
        if (User::where('email', $credentials['email'])->exists()) {
            return array(
                "status" => false,
                "message" => 'Email has already taken!'
            );
        }

        $new_user = new User($credentials);
        $new_user->save();

        return array(
            "status" => true,
            "uid" => $new_user->id,
            "message" => $this->message
        );
    }
}
