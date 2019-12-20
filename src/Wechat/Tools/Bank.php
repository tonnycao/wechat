<?php


namespace Inesadt\Wechat\Tools;

/**
 * @todo 企业付款到银行卡
 * Class Bank
 * @package Inesadt\Wechat\Tools
 */
class Bank
{
    public function pay()
    {
        $url = "https://api.mch.weixin.qq.com/mmpaysptrans/pay_bank";
    }

    public function query()
    {
        $url = "https://api.mch.weixin.qq.com/mmpaysptrans/query_bank";
    }
}