<?php

namespace App\Helper;

class ErrorCodes
{
    const INVALID_COUPON_CODE = ['message' => 'Invalid coupon code', 'code' => '100'];
    const ALREADY_EXIST_COUPON_CODE = ['message' => 'Coupon code is already exist', 'code' => '101'];
    const ALREADY_REDEEMED_COUPON_CODE = ['message' => 'Coupon code is already redeemed', 'code' => '102'];
    const EXPIRED_COUPON = ['message' => 'Expired coupon code', 'code' => '103'];
    public static function getAll()
    {
        $oClass = new \ReflectionClass(__CLASS__);
        return $oClass->getConstants();
    }
    public static function getDisplay($code)
    {
        if (isset($code)) {
            $oClass = new \ReflectionClass(__CLASS__);
            $constants = $oClass->getConstants();
            foreach ($constants as $item) {
                if ($item['code'] == $code) return $item['message'];
            }
        }
        return false;
    }
}
