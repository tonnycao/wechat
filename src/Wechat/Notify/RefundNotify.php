<?php


namespace Inesadt\Wechat\Notify;


use Inesadt\Wechat\Notify;
use Inesadt\Wechat\Util;

/***
 * @todo 退款通知处理类
 * Class RefundNotify
 * @package Inesadt\Wechat\Notify
 */
class RefundNotify extends NotifyBase
{

    /**
     *处理异步post数据
     *
     */
    public function handle()
    {
        $xml = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        $result = [];
        $response = Util::fromXml($xml);
        $this->raw_xml = $response;

        if($response['return_code']=='SUCCESS')
        {

            $result['code'] = 1;
            $result['msg'] = 'ok';
            $result['req_info'] = $this->decode($response['req_info']);
        }else{
            $result['code'] = 0;
            $result['msg'] = $response['return_msg'];
        }

        $this->result = $result;

        if($result['code']==1){
            Notify::replyOk();
        }else{
            Notify::replyFail();
        }

    }

    /**
     * @todo 解密
     * @param $req_info
     * @return string
     */
    protected function decode($req_info)
    {
        $raw_info = base64_decode($req_info,true);
        $md5_key = strtolower(md5($this->key));
        $data = openssl_decrypt($raw_info, 'aes-256-ecb', $md5_key, OPENSSL_RAW_DATA);
        return $data;
    }

}