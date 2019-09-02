<?php


namespace Inesadt\Wechat;


class Notify
{
    //0网络异常1正常-1验签失败
    public static function handle($key, $xml, &$data)
    {
        $result = [];
        $response = Util::fromXml($xml);
        if($response['return_code']=='SUCCESS')
        {
            $sign = Util::makeSign($key,$response);
            if($sign !== $response['sign'])
            {
                $result['code'] = -1;
                $result['msg'] = 'valid sign';
                return $result;
            }
            $result['code'] = 1;
            $result['msg'] = 'ok';
            if($response['return_code']=='SUCCESS')
            {
                $data = $response;
            }

        }else{
            $result['code'] = 0;
            $result['msg'] = $response['return_msg'];
        }
        return $result;
    }


    public static function replyOk()
    {
        $xml = "<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>";
        echo $xml;
    }

    public static function replyFail($msg='fail')
    {
        $xml = "<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[".$msg."]]></return_msg></xml>";
        echo $xml;
    }

}