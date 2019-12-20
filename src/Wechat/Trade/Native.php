<?php


namespace Inesadt\Wechat\Trade;


use Inesadt\Wechat\Api;

/***
 * @todo 扫码支付
 * Class Native
 * @package Inesadt\Wechat\Trade
 */
class Native
{

    const VERSION = 1.0;

    protected $logger = NULL;

    public function __construct($logger)
    {
        $this->logger = $logger;
        Api::setLogger($this->logger);
    }

    /**
     * @todo 下单
     * @param $config
     * @param $params
     * @return bool|mixed
     */
    public function order($config, $params)
    {
        if(empty($params['product_id'])){
            return false;
        }
        $params['trade_type'] = $this->getName();

        $result = Api::unifiedOrder($config, $params);
        if(!$result)
        {
            return false;
        }
        return $result;
    }

    /**
     * @todo 缩短长地址
     * @param $config
     * @param $long_url
     * @return string
     */
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

    /***
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
     * @todo 关闭订单
     * @param $config
     * @param $out_trade_no
     * @return bool|mixed
     */
    public function close($config, $out_trade_no)
    {
        if(empty($out_trade_no)){
            return false;
        }
        $result = Api::closeOrder($config, $out_trade_no);
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

    /**
     * #todo 退款查询
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
        return 'NATIVE';
    }

    public function getVersion()
    {
        return self::VERSION;
    }

}