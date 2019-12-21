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
    public function pay($config,$partner_trade_no,$enc_bank_no, $enc_true_name,$bank_code,$amount,$desc)
    {
        $url = "https://api.mch.weixin.qq.com/mmpaysptrans/pay_bank";

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


}