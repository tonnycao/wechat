<?php


namespace Inesadt\Wechat\Trade;

use Inesadt\Wechat\Api;

class Jsapi
{
    const VERSION = 0.1;

    protected $logger = NULL;

    public function __construct($logger)
    {
        $this->logger = $logger;
        Api::setLogger($this->logger);
    }

    //下单
    public function order($config, $params)
    {
        if(!empty($params['openid']))
        {
            return false;
        }
        $params['trade_type'] = $this->getName();

        $result = Api::unifiedorder($config, $params);
        if(!$result)
        {
            return false;
        }
        return $result;
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
        return 'JSAPI';
    }

    public function getVersion()
    {
        return self::VERSION;
    }
}