<?php

namespace Inesadt\Wechat\Tools;

use Inesadt\Wechat\Api;
use Inesadt\Wechat\Util;

/**
 * @todo 代金券
 * Class Coupon
 * @package Inesadt\Wechat\Tools
 */
class Coupon extends ToolBase
{

    /***
     * @todo 发放代金劵
     * @param $config
     * @param $param
     * @return bool|mixed
     */
    public function sendCoupon($config,$param)
    {
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/send_coupon";

        if(empty($param['coupon_stock_id'])|| empty($param['partner_trade_no'])|| empty($param['openid'])){
            return false;
        }
        if(empty($config['cert_path']) || empty($config['key_path']) ||
            !file_exists($config['cert_path'])|| !file_exists($config['key_path'])){
            return false;
        }
        $data = $param;
        $data['openid_count'] = 1;
        $data['mch_id'] = $this->mch_id;
        $data['appid'] = $this->appid;
        $data['nonce_str'] = Util::getNonceStr();
        $data['sign'] = Util::makeSign($this->key,$data);
        $xml = Util::toXml($data);
        Api::setLogger($this->logger);
        $response = Api::postXmlCurl($xml,$url, 2 , true, $config['cert_path'], $config['key_path']);
        if(!$response){
            return false;
        }
        $return = Util::fromXml($response);
        return $return;
    }

    public function queryCouponStock($coupon_stock_id)
    {
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/query_coupon_stock";
        $data = [
            'coupon_stock_id'=>$coupon_stock_id,
            'mch_id'=>$this->mch_id,
            'appid'=>$this->appid,
            'nonce_str'=>Util::getNonceStr(),
        ];

        $data['sigin'] = Util::makeSign($this->key,$data);
        $xml = Util::toXml($data);
        Api::setLogger($this->logger);
        $response = Api::postXmlCurl($xml,$url);

        if(!$response){
            return false;
        }
        $return = Util::fromXml($response);
        return $return;

    }

    public function queryCouponsInfo($coupon_id, $openid, $stock_id)
    {
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/querycouponsinfo";

        $data = [
            'coupon_id'=>$coupon_id,
            'openid'=>$openid,
            'stock_id'=>$stock_id,
            'mch_id'=>$this->mch_id,
            'appid'=>$this->appid,
            'nonce_str'=>Util::getNonceStr(),
        ];

        $data['sigin'] = Util::makeSign($this->key,$data);
        $xml = Util::toXml($data);
        Api::setLogger($this->logger);
        $response = Api::postXmlCurl($xml,$url);

        if(!$response){
            return false;
        }
        $return = Util::fromXml($response);
        return $return;
    }

}