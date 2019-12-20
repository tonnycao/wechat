<?php


namespace Inesadt\Wechat\Trade;

use Inesadt\Wechat\Api;

/***
 * @todo 付款码支付
 * Class Micropay
 * @package Inesadt\Wechat\Trade
 */

class Micropay
{
    const VERSION = 0.1;

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
        if(empty($params['out_trade_no'])||empty($params['auth_code'])||
            empty($params['total_fee'])||empty($param['body'])){
            return false;
        }

        return Api::micropay($config, $params);
    }

    /**
     * @todo 查询订单状态
     * @param $config
     * @param $out_trade_no
     * @return bool|mixed
     */
    public function query($config, $out_trade_no)
    {
        if(empty($out_trade_no)){
            return false;
        }
        $result = Api::orderQuery($config, $out_trade_no);
        return $result;
    }

    /**
     * @todo 撤销订单
     * @param $config
     * @param $transaction_id
     * @param $out_trade_no
     * @return bool|mixed
     */
    public function reverse($config, $transaction_id, $out_trade_no)
    {
        if(empty($transaction_id)||empty($out_trade_no)){
            return false;
        }
        return Api::reverse($config,$transaction_id, $out_trade_no);
    }

    /***
     * @todo 申请退款
     * @param $config
     * @param $params
     * @return bool|mixed
     */
    public function refund($config, $params)
    {
        if(empty($params['out_trade_no']) || empty($params['refund_trade_no']) ||
            empty($params['total_fee']) || empty($params['refund_fee']))
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

    /***
     * @todo 获取支付场景
     * @return string
     */
    public function getName()
    {
        return 'micropay';
    }

    /***
     * @todo 获取版本
     * @return float
     */
    public function getVersion()
    {
        return self::VERSION;
    }
}