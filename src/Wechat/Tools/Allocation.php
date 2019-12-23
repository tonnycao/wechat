<?php


namespace Inesadt\Wechat\Tools;

use Inesadt\Wechat\Api;
use Inesadt\Wechat\Util;

/**
 * @todo 直连分账 1.添加分账账号 2.统一下单带上profit_sharing=Y 3.请求分账 4.查询分账结果/异步接收分账信息
 * Class Allocation
 * @package Inesadt\Wechat\Tools
 */
class Allocation extends ToolBase
{

    protected $account_type_map = [
        'MERCHANT_ID', //商户ID
        'PERSONAL_WECHATID' //个人微信号
        ,'PERSONAL_OPENID' //个人openid
    ];

    protected $relation_type_map = [
        'SERVICE_PROVIDER',//服务商
        'STORE',//门店
        'STAFF',//员工
        'STORE_OWNER',//店主
        'PARTNER',//合作伙伴
        'HEADQUARTER',//总部
        'BRAND',//品牌方
        'DISTRIBUTOR',//分销商
        'USER',//用户
        'SUPPLIER',//供应商
        'CUSTOM',//自定义
    ];

    /***
     * @todo 添加收款方
     * @param $receiver
     * @return bool|mixed
     */
    public function addReceiver($receiver)
    {
        $url = "https://api.mch.weixin.qq.com/pay/profitsharingaddreceiver";
        if(empty($receiver['type']) || empty($receiver['account']) || empty($receiver['name']) ||
            empty($receiver['relation_type']) || empty($receiver['custom_relation'])){
            return false;
        }

        if(!in_array($receiver['type'],$this->account_type_map,true)){
            return false;
        }
        if(!in_array($receiver['relation_type'],$this->relation_type_map,true)){
            return false;
        }

        $param = [
            'mch_id'=>$this->mch_id,
            'appid'=>$this->appid,
            'nonce_str'=>Util::getNonceStr(),
            'receiver'=>$receiver,
            'sign_type'=>'HMAC-SHA256'
        ];

        $param['sign'] = Util::makeHmacSign($this->key, $param);
        $xml = Util::toXml($param);
        $response = Api::postXmlCurl($xml,$url);
        if(!$response){
            return false;
        }
        $result = Util::fromXml($response);
        return $result;
    }

    /***
     * @todo 删除收款方
     * @param $receiver
     * @return bool|mixed
     */
    public function removeReceiver($receiver)
    {
        $url = "https://api.mch.weixin.qq.com/pay/profitsharingremovereceiver";
        if(empty($receiver['type'])||empty($receiver['account']) ||
            !in_array($receiver['account'],$this->account_type_map,true)){
            return false;
        }
        $param = [
            'mch_id'=>$this->mch_id,
            'appid'=>$this->appid,
            'nonce_str'=>Util::getNonceStr(),
            'receiver'=>$receiver,
            'sign_type'=>'HMAC-SHA256'
        ];

        $param['sign'] = Util::makeHmacSign($this->key, $param);
        $xml = Util::toXml($param);
        $response = Api::postXmlCurl($xml,$url);
        if(!$response){
            return false;
        }
        $result = Util::fromXml($response);
        return $result;
    }

    /**
     * @todo 单个订单单次分账
     * @param $config
     * @param $out_trade_no
     * @param $transaction_id
     * @param $receiver
     * @return bool|mixed
     */
    public function profitSharing($config,$out_trade_no,$transaction_id,$receiver)
    {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/profitsharing";
        if(empty($config['cert_path'])||empty($config['key_path']) || empty($out_trade_no) || empty($transaction_id)
            || empty($receiver)){
            return false;
        }

        $param = [
            'mch_id'=>$this->mch_id,
            'appid'=>$this->appid,
            'out_trade_no'=>$out_trade_no,
            'transaction_id'=>$transaction_id,
            'nonce_str'=>Util::getNonceStr(),
            'receiver'=>$receiver,
            'sign_type'=>'HMAC-SHA256'
        ];

        $param['sign'] = Util::makeHmacSign($this->key, $param);
        $xml = Util::toXml($param);
        $response = Api::postXmlCurl($xml,$url,2,true,$config['cert_path'],$config['key_path']);
        if(!$response){
            return false;
        }
        $result = Util::fromXml($response);
        return $result;

    }

    /***
     * @todo 单个订单多次分账
     * @param $config
     * @param $out_trade_no
     * @param $transaction_id
     * @param $receiver
     * @return bool|mixed
     */
    public function MultiProfitSharing($config,$out_trade_no,$transaction_id,$receiver)
    {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/multiprofitsharing";
        if(empty($config['cert_path'])||empty($config['key_path']) || empty($out_trade_no) || empty($transaction_id)
            || empty($receiver)){
            return false;
        }

        $param = [
            'mch_id'=>$this->mch_id,
            'appid'=>$this->appid,
            'out_trade_no'=>$out_trade_no,
            'transaction_id'=>$transaction_id,
            'nonce_str'=>Util::getNonceStr(),
            'receiver'=>$receiver,
            'sign_type'=>'HMAC-SHA256'
        ];

        $param['sign'] = Util::makeHmacSign($this->key, $param);
        $xml = Util::toXml($param);
        $response = Api::postXmlCurl($xml,$url,2,true,$config['cert_path'],$config['key_path']);
        if(!$response){
            return false;
        }
        $result = Util::fromXml($response);
        return $result;
    }

    /***
     * @todo 查询分账
     * @param $transaction_id
     * @param $out_trade_no
     * @return bool|mixed
     */
    public function profitSharingQuery($transaction_id,$out_trade_no)
    {
        $url = "https://api.mch.weixin.qq.com/pay/profitsharingquery";
        $param = [
            'mch_id'=>$this->mch_id,
            'appid'=>$this->appid,
            'out_trade_no'=>$out_trade_no,
            'transaction_id'=>$transaction_id,
            'nonce_str'=>Util::getNonceStr(),
            'sign_type'=>'HMAC-SHA256'
        ];

        $param['sign'] = Util::makeHmacSign($this->key, $param);
        $xml = Util::toXml($param);
        $response = Api::postXmlCurl($xml,$url);
        if(!$response){
            return false;
        }
        $result = Util::fromXml($response);
        return $result;

    }

    /***
     * @todo 完成冻结分账余额
     * @param $config
     * @param $out_trade_no
     * @param $transaction_id
     * @param $desc
     * @return bool|mixed
     */
    public function profitSharingFinish($config,$out_trade_no,$transaction_id,$desc)
    {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/profitsharingfinish";
        if(empty($config['cert_path'])||empty($config['key_path']) ||
            empty($out_trade_no) || empty($transaction_id) || empty($desc)){
            return false;
        }
        $param = [
            'mch_id'=>$this->mch_id,
            'appid'=>$this->appid,
            'out_trade_no'=>$out_trade_no,
            'transaction_id'=>$transaction_id,
            'description'=>$desc,
            'nonce_str'=>Util::getNonceStr(),
            'sign_type'=>'HMAC-SHA256'
        ];

        $param['sign'] = Util::makeHmacSign($this->key, $param);
        $xml = Util::toXml($param);
        $response = Api::postXmlCurl($xml,$url,2,true,$config['cert_path'],$config['key_path']);
        if(!$response){
            return false;
        }
        $result = Util::fromXml($response);
        return $result;
    }

    /***
     * @todo 分账回退 主要用在退款已经分账的订单
     * @param $config
     * @param $param
     * @return bool|mixed
     */
    public function profitSharingReturn($config,$param)
    {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/profitsharingreturn";
        if(empty($config['cert_path']) || empty($config['key_path']) || empty($param['order_id']) || empty($param['out_return_no'])
            ||empty($param['return_account_type']) || empty($param['return_account']) ||empty($param['return_amount']) || empty($param['description'])){
            return false;
        }

        $param = [
            'mch_id'=>$this->mch_id,
            'appid'=>$this->appid,
            'order_id'=>$param['order_id'],
            'out_return_no'=>$param['out_return_no'],
            'return_account_type'=>$param['return_account_type'],
            'return_account'=>$param['return_account'],
            'return_amount'=>$param['return_amount'],
            'description'=>$param['description'],
            'nonce_str'=>Util::getNonceStr(),
            'sign_type'=>'HMAC-SHA256'
        ];

        $param['sign'] = Util::makeHmacSign($this->key, $param);
        $xml = Util::toXml($param);
        $response = Api::postXmlCurl($xml,$url,2,true,$config['cert_path'],$config['key_path']);
        if(!$response){
            return false;
        }
        $result = Util::fromXml($response);
        return $result;
    }

    /***
     * @todo 回退查询
     * @param $order_id
     * @param $out_return_no
     * @return bool|mixed
     */
    public function profitSharingReturnQuery($order_id,$out_return_no)
    {
        $url = "https://api.mch.weixin.qq.com/pay/profitsharingreturnquery";
        $param = [
            'mch_id'=>$this->mch_id,
            'appid'=>$this->appid,
            'order_id'=>$order_id,
            'out_return_no'=>$out_return_no,
            'nonce_str'=>Util::getNonceStr(),
            'sign_type'=>'HMAC-SHA256'
        ];

        $param['sign'] = Util::makeHmacSign($this->key, $param);
        $xml = Util::toXml($param);
        $response = Api::postXmlCurl($xml,$url);
        if(!$response){
            return false;
        }
        $result = Util::fromXml($response);
        return $result;
    }
}
