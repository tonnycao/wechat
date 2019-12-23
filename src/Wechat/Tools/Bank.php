<?php


namespace Inesadt\Wechat\Tools;

use Inesadt\Wechat\Api;
use Inesadt\Wechat\Util;

/**
 * @todo 企业付款到银行卡
 * Class Bank
 * @package Inesadt\Wechat\Tools
 */
class Bank extends ToolBase
{

    /***
     * @todo 企业付款 需要证书
     * @param $config
     * @param $partner_trade_no
     * @param $bank_no
     * @param $true_name
     * @param $bank_code
     * @param $amount
     * @param $desc
     * @return bool|mixed
     */
    public function pay($config,$partner_trade_no,$bank_no, $true_name,$bank_code,$amount,$desc)
    {
        $url = "https://api.mch.weixin.qq.com/mmpaysptrans/pay_bank";

        if(empty($config['bank_pub_key_path']) || empty( $config['cert_path']) || empty($config['key_path'])){
            return false;
        }
        $pub_key = file_get_contents($config['bank_pub_key_path']);
        if(empty($pub_key)){
            $pub_key = $this->getPublicKey($config);
        }
        if(empty($pub_key)){
            return false;
        }
        $enc_bank_no = Util::rsaOpensslEncode($bank_no,$pub_key);
        if(!$enc_bank_no){
            return false;
        }
        $enc_true_name = Util::rsaOpensslEncode($true_name,$pub_key);
        if(!$enc_true_name){
            return false;
        }
        $data = [
            'mch_id'=>$this->mch_id,
            'partner_trade_no'=>$partner_trade_no,
            'enc_bank_no'=>$enc_bank_no,
            'enc_true_name'=>$enc_true_name,
            'bank_code'=>$bank_code,
            'amount'=>(int)$amount,
            'desc'=>$desc,
            'nonce_str'=>Util::getNonceStr()
        ];

        $sign = Util::makeSign($this->key, $data);
        $data['sign'] = $sign;
        $xml = Util::toXml($data);

        $response = Api::postXmlCurl($xml,$url,2, true, $config['cert_path'], $config['key_path']);
        if(!$response){
            return false;
        }
        $return = Util::fromXml($response);
        return $return;
    }

    /***
     * @todo 查询付款情况
     * @param $config
     * @param $partner_trade_no
     * @return bool|mixed
     */
    public function query($config,$partner_trade_no)
    {
        $url = "https://api.mch.weixin.qq.com/mmpaysptrans/query_bank";

        $data = [
            'mch_id'=>$this->mch_id,
            'partner_trade_no'=>$partner_trade_no,
            'nonce_str'=>Util::getNonceStr()
        ];

        $sign = Util::makeSign($this->key, $data);
        $data['sign'] = $sign;
        $xml = Util::toXml($data);

        $response = Api::postXmlCurl($xml,$url,2, true, $config['cert_path'], $config['key_path']);
        if(!$response){
            return false;
        }
        $return = Util::fromXml($response);
        return $return;

    }

    /***
     * @todo 获取微信支付的RSA公钥
     * @param $config
     * @return bool|string
     */
    public function getPublicKey($config){
        $pub_key = '';
        $url = "https://fraud.mch.weixin.qq.com/risk/getpublickey";
        $param = [
            'mch_id'=>$this->mch_id,
            'nonce_str'=>Util::getNonceStr(),
            'sign_type'=>'MD5'
        ];
        $param['sign'] = Util::makeSign($this->key, $param);
        $xml = Util::toXml($param);
        $response = Api::postXmlCurl($xml,$url,2,true, $config['cert_path'], $config['key_path']);
        if(!$response){
            return false;
        }
        $return = Util::fromXml($response);
        if(!empty($config['bank_pub_key_path'])){
            $pub_key = $this->savePublicKey($config['bank_pub_key_path'], $return);
        }
        return $pub_key;
    }

    /***
     * @todo 获取公钥 记得PKCS#1 转 PKCS#8 openssl rsa -RSAPublicKey_in -in pcs1.pem -out  pcs8.pem
     * @param $path
     * @param $data
     * @return bool
     */
    protected function savePublicKey($path, $data){
        $flag = false;
        if($data['return_code'] == "SUCCESS" && $data['result_code'] == "SUCCESS") {
            file_put_contents($path, $data['pub_key']);
            return $data['pub_key'];
        }
        return $flag;
    }
}