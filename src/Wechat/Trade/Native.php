<?php


namespace Inesadt\Wechat\Trade;


use Inesadt\Wechat\Api;

class Native
{

    const VERSION = 0.2;

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

    public function getName()
    {
        return 'NATIVE';
    }

    public function getVersion()
    {
        return self::VERSION;
    }

}