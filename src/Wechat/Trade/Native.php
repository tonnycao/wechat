<?php


namespace Inesadt\Wechat\Trade;


use Inesadt\Wechat\Api;

class Native
{

    const VERSION = 1.0;

    protected $logger = NULL;

    public function __construct($logger)
    {
        $this->logger = $logger;
        Api::setLogger($this->logger);
    }

    //下单
    public function order($config, $params)
    {
        $params['trade_type'] = $this->getName();

        $result = Api::unifiedOrder($config, $params);
        if(!$result)
        {
            return false;
        }
        return $result;
    }

    //缩短长地址
    public function shortUrl($config, $long_url)
    {
        $url = '';
        $response = Api::shortUrl($config, $long_url);
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
        $result = Api::orderQuery($config, $out_trade_no);
        return $result;
    }

    //关闭订单
    public function close($config, $out_trade_no)
    {
        $result = Api::closeOrder($config, $out_trade_no);
        return $result;
    }

    //退款
    public function refund($config, $params)
    {
        if(empty($params['out_trade_no']) || empty($params['refund_trade_no']) || empty($params['total_fee']) || empty($params['refund_fee']))
        {
            return false;
        }
        return Api::refund($config,$params);
    }

    //退款查询
    public function refundQuery($config,$refund_trade_no,$offset=0)
    {
        if(empty($refund_trade_no))
        {
            return false;
        }
        return Api::refundQuery($config,$refund_trade_no,$offset);
    }

    public function getName()
    {
        return 'NATIVE';
    }

    public function getVersion()
    {
        return self::VERSION;
    }

}