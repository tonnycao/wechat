<?php


namespace Inesadt\WechatPay\TradeType;


use Inesadt\WechatPay\Api;

class Native
{

    const VERSION = 0.1;

    //下单
    public function order($config, $params)
    {
        $result = Api::unifiedorder($config, $params);
        if(!$result)
        {
            return false;
        }
        $result['short_url'] = '';
        if($result['return_code']=='SUCCESS' && $result['result_code']=='SUCCESS' )
        {
            $url = self::shortUrl($config, $result['code_url']);
            if(!empty($url))
            {
                $result['short_url'] = $url;
            }
        }

        return $result;
    }

    //缩短长地址
    public function shortUrl($config, $long_url)
    {
        $url = '';
        $response = Api::shorturl($config, $long_url);
        if(!$response)
        {
            return $url;
        }

        if($response['return_code'] =='SUCCESS' && $response['result_code']=='SUCCESS')
        {
            $url = $response['short_url'];
        }

        return $url;
    }

    //查询订单
    public function query($config, $out_trade_no)
    {
        $result = Api::orderquery($config, $out_trade_no);
        return $result;
    }

    //关闭订单
    public function close($config, $out_trade_no)
    {
        $result = Api::closeorder($config, $out_trade_no);
        return $result;
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