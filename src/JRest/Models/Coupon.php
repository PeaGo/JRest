<?php

namespace JRest\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table = 'coupons';
    protected $guarded = [];
    const UPDATED_AT = 'updated_at';
    const CREATED_AT = 'created_at';


    const ACTIVE = ['display' => 'Active', 'value' => '1'];
    const USED = ['display' => 'Used', 'value' => '2'];
    const ASSIGNED = ['display' => 'Assigned', 'value' => '1'];
    const UNASSIGNED = ['display' => 'UnAssigned', 'value' => '2'];

    const INVALID_COUPON_CODE = ['message' => 'Invalid coupon code', 'code' => '100'];
    const ALREADY_EXIST_COUPON_CODE = ['message' => 'Coupon code is already exist', 'code' => '101'];
    const ALREADY_REDEEMED_COUPON_CODE = ['message' => 'Coupon code is already redeemed', 'code' => '102'];
    const EXPIRED_COUPON = ['message' => 'Expired coupon code', 'code' => '103'];
    const REDEEM_COUPON_SUCCESS = ['message' => 'Redeem coupon success', 'code' => '200'];

    //override save to auto filling some default attribute

    public function save(array $options = [])
    {
        return parent::save($options);
    }

    public static  function findByCode($code, $uid = null)
    {

        $where = array('code' => $code);
        if ($uid)
            $where['cid'] = $uid;
        return  Coupon::where($where)->get();
    }
}
