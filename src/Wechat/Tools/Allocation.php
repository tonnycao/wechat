<?php


namespace Inesadt\Wechat\Tools;

/**
 * @todo 直连分账
 * Class Allocation
 * @package Inesadt\Wechat\Tools
 */
class Allocation
{
    public function profitSharing()
    {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/profitsharing";

    }

    public function MultiProfitSharing()
    {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/multiprofitsharing";
    }

    public function profitSharingQuery()
    {
        $url = "https://api.mch.weixin.qq.com/pay/profitsharingquery";
    }

    public function addReceiver()
    {
        $url = "https://api.mch.weixin.qq.com/pay/profitsharingaddreceiver";
    }

    public function removeReceiver()
    {
        $url = "https://api.mch.weixin.qq.com/pay/profitsharingremovereceiver";
    }


    public function profitSharingFinish()
    {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/profitsharingfinish";
    }

    public function profitSharingReturn()
    {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/profitsharingreturn";
    }

    public function profitSharingReturnQuery()
    {
        $url = "https://api.mch.weixin.qq.com/pay/profitsharingreturnquery";
    }
}
