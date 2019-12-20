<?php

namespace Inesadt\Wechat\Tools;

/**
 * @todo 代金券
 * Class Coupon
 * @package Inesadt\Wechat\Tools
 */
class Coupon
{


    public function sendCoupon()
    {
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/send_coupon";
    }

    public function queryCouponStock()
    {
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/query_coupon_stock";
    }

    public function queryCouponsInfo()
    {
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/querycouponsinfo";
    }

}