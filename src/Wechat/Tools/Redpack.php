<?php


namespace Inesadt\Wechat\Tools;


use Inesadt\Wechat\Api;
use Inesadt\Wechat\Util;

/***
 * @todo 红包
 * Class Redpack
 * @package Inesadt\Wechat\Tools
 */
class Redpack extends ToolBase
{

    public function send($config,$mch_name,$openid,$mch_billno,$total_amount,$wishing,$act_name,$remark)
    {
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack";

        $data = [
            'wxappid'=>$this->appid,
            'mch_id'=>$this->mch_id,
            'mch_name'=>$mch_name,
            're_openid'=>$openid,
            'mch_billno'=>$mch_billno,
            'total_amount'=>$total_amount,
            'total_num'=>1,
            'client_ip'=>Util::getServerIp(),
            'wishing'=>$wishing,
            'act_name'=>$act_name,
            'remark'=>$remark,
            'nonce_str'=>Util::getNonceStr()
        ];
        $data['sign'] = Util::makeSign($this->key,$data);
        $xml = Util::toXml($data);
        Api::setLogger($this->logger);
        $response = Api::postXmlCurl($xml, $url, 2, true ,$config['cert_path'],$config['key_path']);
        if(!$response){
            return false;
        }
        $return = Util::fromXml($response);
        $return['client_ip'] = $data['client_ip'];
        return $return;
    }

    public function sendGroup($config,$mch_name,$openid,$mch_billno,$total_amount,$wishing,$act_name,$remark,$total_num)
    {
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendgroupredpack";

        $data = [
            'wxappid'=>$this->appid,
            'mch_id'=>$this->mch_id,
            'mch_name'=>$mch_name,
            're_openid'=>$openid,
            'mch_billno'=>$mch_billno,
            'total_amount'=>$total_amount,
            'total_num'=>$total_num,
            'client_ip'=>Util::getServerIp(),
            'wishing'=>$wishing,
            'act_name'=>$act_name,
            'remark'=>$remark,
            'nonce_str'=> Util::getNonceStr(),
            'amt_type'=>'ALL_RAND'
        ];

        $data['sign'] = Util::makeSign($this->key, $data);
        $xml = Util::toXml($data);
        Api::setLogger($this->logger);
        $response = Api::postXmlCurl($xml, $url, 2, true ,$config['cert_path'],$config['key_path']);
        if(!$response){
            return false;
        }
        $return = Util::fromXml($response);
        $return['client_ip'] = $data['client_ip'];
        return $return;

    }

    public function getHbInfo($mch_billno,$bill_type)
    {
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo";
        $data = [
            'appid'=>$this->appid,
            'mch_id'=>$this->mch_id,
            'mch_billno'=>$mch_billno,
            'bill_type'=>$bill_type,
            'nonce_str'=>Util::getNonceStr()
        ];
        $data['sign'] = Util::makeSign($this->key, $data);

        $xml = Util::toXml($data);
        Api::setLogger($this->logger);

        $response = Api::postXmlCurl($xml, $url);

        if(!$response){
            return false;
        }
        $return = Util::fromXml($response);

        return $return;
    }


    public function sendMiniProgram($config,$mch_name,$openid,$mch_billno, $total_amount,$wishing,$act_name,$remark)
    {
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendminiprogramhb";
        $data = [
            'wxappid'=>$this->appid,
            'mch_id'=>$this->mch_id,
            'mch_name'=>$mch_name,
            're_openid'=>$openid,
            'mch_billno'=>$mch_billno,
            'total_amount'=>$total_amount,
            'total_num'=>1,
            'client_ip'=>Util::getServerIp(),
            'wishing'=>$wishing,
            'act_name'=>$act_name,
            'remark'=>$remark,
            'nonce_str'=>Util::getNonceStr(),
            'notify_way'=>'MINI_PROGRAM_JSAPI'
        ];
        $data['sign'] = Util::makeSign($this->key,$data);
        $xml = Util::toXml($data);
        Api::setLogger($this->logger);
        $response = Api::postXmlCurl($xml, $url, 2, true ,$config['cert_path'],$config['key_path']);
        if(!$response){
            return false;
        }
        $return = Util::fromXml($response);
        $return['client_ip'] = $data['client_ip'];
        return $return;

    }


}