<?php


namespace Inesadt\Wechat\Trade;

use Inesadt\Wechat\Api;

/***
 * @too JS支付
 * Class Jsapi
 * @package Inesadt\Wechat\Trade
 */
class Jsapi
{
    const VERSION = 0.1;

    protected $logger = NULL;

    public function __construct($logger)
    {
        $this->logger = $logger;
        Api::setLogger($this->logger);
    }

    /***
     * @todo 下单
     * @param $config
     * @param $params
     * @return bool|mixed
     */
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

    /***
     * @todo 查询订单状态
     * @param $config
     * @param $out_trade_no
     * @return bool|mixed
     */
    public function query($config, $out_trade_no)
    {
        $result = Api::orderquery($config, $out_trade_no);
        return $result;
    }

    /**
     * @todo 关闭订单
     * @param $config
     * @param $out_trade_no
     * @return bool|mixed
     */
    public function close($config, $out_trade_no)
    {
        $result = Api::closeorder($config, $out_trade_no);
        return $result;
    }

    /***
     * @todo 申请退款
     * @param $config
     * @param $params
     * @return bool|mixed
     */
    public function refund($config, $params)
    {
        if(empty($params['out_trade_no']) || empty($params['refund_trade_no']) || empty($params['total_fee']) || empty($params['refund_fee']))
        {
            return false;
        }
        return Api::refund($config,$params);
    }

    /***
     * @todo 退款查询
     * @param $config
     * @param $refund_trade_no
     * @param int $offset
     * @return bool|mixed
     */
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
        return 'JSAPI';
    }

    public function getVersion()
    {
        return self::VERSION;
    }
}