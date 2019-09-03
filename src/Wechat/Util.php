<?php


namespace Inesadt\Wechat;


class Util
{
    public static function makeHmacSign($key, $params)
    {
        ksort($params);
        $string = self::toUrlParams($params);
        $string = hash_hmac('sha256',$string,$key);
        $result = strtoupper($string);
        return $result;
    }

    public static function handleGzipData($data)
    {

        return $data;
    }

    //获取客户端IP
    public static function getClientIp($type=0)
    {
        global $_SERVER;
        $type = $type ? 1 : 0;
        $ip =  NULL;
        if ($ip !== NULL) return $ip[$type];
        if(isset($_SERVER['HTTP_X_REAL_IP']) && !empty($_SERVER['HTTP_X_REAL_IP']))
        {
            //nginx 代理模式下，获取客户端真实IP
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        }elseif (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']))
        {
            //客户端的ip
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //浏览当前页面的用户计算机的网关
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip = trim($arr[0]);
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];//浏览当前页面的用户计算机的ip地址
        }else{
            $ip = '';
        }
        // IP地址合法验证
        $long = sprintf("%u",ip2long($ip));
        $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }

    public static function makeSign($key, $params)
    {
        ksort($params);
        $string = self::toUrlParams($params);
        $string = $string . "&key=".$key;
        $string = md5($string);
        $result = strtoupper($string);
        return $result;
    }

    public static function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

    public static function getMillisecond()
    {
        //获取毫秒的时间戳
        $time = explode ( " ", microtime () );
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode( ".", $time );
        $time = $time2[0];
        return $time;
    }

    public static function toXml($values)
    {
        $xml = "<xml>";
        foreach ($values as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    public static function fromXml($xml)
    {
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }

    public static function toUrlParams($values)
    {
        $buff = "";
        foreach ($values as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }
}