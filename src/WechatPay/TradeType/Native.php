<?php


namespace Inesadt\WechatPay\TradeType;


use Inesadt\WechatPay\Api;

class Native
{

    const VERSION = 0.1;

    public function order($config, $params)
    {
        $result = Api::unifiedorder($config, $params);
        if(!$result)
        {
            return false;
        }
        $return = self::shortUrl();
        return $return;
    }

    public function shortUrl()
    {

    }

    public function getName()
    {
        return 'Native';
    }

    public function getVersion()
    {
        return self::VERSION;
    }

}