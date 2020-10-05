<?php

namespace JRest\Services\Loyalty;

use Cake\Collection\CollectionInterface;
use DateInterval;
use Illuminate\Database\Capsule\Manager;
use JRest\Helpers\JDate;
use JRest\Helpers\JUtil;
use JRest\Models\Coupon;
use JRest\Models\Loyal_Activity;
use JRest\Models\Loyal_Rule;
use JRest\Models\Point_Activities;
use JRest\Models\Point_Rule;
use Psr\Container\ContainerInterface;


class Loyalty
{

    const SUBJECT_IDENTIFIER = 'id';

    /**
     * @var \Illuminate\Database\Capsule\Manager
     */
    private $db;
    /**
     * @var array
     */
    private $appConfig;

    private $container;

    /**
     * Undocumented function
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->db = $container->get('db');
        $this->appConfig = $container->get('settings');
    }

    /**
     * Create a activity for an account, for example: When account is registered by invitation
     *
     * @return void
     */
    public function createActivity($uid, $rule_code, $referral_id)
    {
        $rule = Loyal_Rule::where(
            'code',
            $rule_code
        )->first();

        if ($rule->expired_day > 0) {
            $expired = new JDate();
            $expired->add(new DateInterval('P' . $rule->expired_day . 'D'))->toSql();
        } else {
            $expired = $rule->expired;
        }

        $activity = array(
            'cid' => $uid,
            'rule_id' => $rule->id,
            'points' => $rule->points,
            'referral_id' => $referral_id, //Invitation code
            'expired' => $expired,
            'status' => $rule->autoapproved
        );

        $new = new Loyal_Activity($activity);
        $new->save();
        $act = Loyal_Activity::find($new->id);

        // create voucher auto
        if ($rule->autoapproved) {

            $coupon = array(
                'cid' => $uid,
                'title' => $rule->title,
                'code' => JUtil::getRandomCode(6),
                'min_order' => $rule->min_order,
                'amount' => $act->points,
                'expired' => $act->expired,
                'referral_id' => $act->id,
            );
            $new = new Coupon($coupon);
            $new->save();
        }
    }

    /**
     * List usable coupon for an account
     */
    public function listUsableCoupons($uid)
    {
        $coupons = Coupon::select('coupons.*')
            ->where('coupons.cid', '=', $uid)->where('coupons.status', Coupon::ACTIVE['value'])->where('expired', '>=', new JDate())
            ->orderBy('expired', 'asc')
            ->get();
        return $coupons;
    }

    public function listUnusableCoupons($uid)
    {
        $coupons = Coupon::select('coupons.*')
            ->where('coupons.cid', '=', $uid)->where('coupons.status', Coupon::USED['value'])
            ->orderBy('expired', 'desc')
            ->get();
        return $coupons;
    }

    public function getRuleFromCouponCode($code)
    {
        $rule = Loyal_Rule::select('point_rule.*')->where('point_rule.code', '=', $code)->first();
        return $rule;
    }

    public function createCoupon($uid, $referral_id)
    {
        $act = Loyal_Activity::find($referral_id);
        $coupon = array(
            'cid' => $uid,
            'title' => $act->title,
            'code' => JUtil::getRandomCode(6),
            'min_order' => '',
            'amount' => $act->points,
            'expired' => $act->expired,
            'status' => Coupon::ACTIVE['value']
        );
        $new = new Coupon($coupon);
        $new->save();
        $coupon = Coupon::find($new->id);
        return $coupon;
    }

    public function redeemCoupon($uid, $code)
    {
        //check redeem coupon or not
        $coupon = Coupon::findByCode($code, $uid);
        if (count($coupon) > 0) {
            return array(
                'code' => Coupon::ALREADY_REDEEMED_COUPON_CODE,
                'message' => "This promo code has already been redeemed. You can find it in the promo code list below."
            );
        } else {
            $coupon = Coupon::where('code', $code)
                ->where('cid', null)->where('type', Coupon::UNASSIGNED['value'])->first();
            if ($coupon) {
                //check coupon is expired
                if (new JDate($coupon->expired) < new JDate()) {
                    return array(
                        "code" => Coupon::EXPIRED_COUPON,
                        "message" => "This promo code has already expired"
                    );
                }

                //TODO check max redeemptions

                $newCoupon = $coupon->replicate();
                $newCoupon->cid = $uid;
                $newCoupon->type = Coupon::ASSIGNED['value'];
                $newCoupon->status = Coupon::ACTIVE['value'];
                $newCoupon->save();
                return array(
                    "code" => Coupon::REDEEM_COUPON_SUCCESS,
                    "message" => "Redeem coupon success"
                );
            } else {
                return array(
                    "code" => Coupon::INVALID_COUPON_CODE,
                    "message" => "This promo code is not valid"
                );
            }
        }
    }

    public function applyCoupon($code, $uid)
    {
        $coupon = Coupon::findByCode($code, $uid);
        if ($coupon) {
            $coupon->status = Coupon::USED['value'];
            $coupon->save();
            return array(
                "status" => true
            );
        } else {
            return array(
                "status" => false,
                "error" => Coupon::INVALID_COUPON_CODE
            );
        }
    }
}
