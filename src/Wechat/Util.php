<?php

namespace Inesadt\Wechat;

/**
 * @todo 通用工具类
 * Class Util
 * @package Inesadt\Wechat
 */
class Util
{
    /***
     * @todo 获取服务器IP
     * @return mixed
     */
    public static function getServerIp()
    {
        global $_SERVER;
        return $_SERVER['SERVER_ADDR'];
    }
    /***
     * @todo HMAC签名
     * @param $key
     * @param $params
     * @return string
     */
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

    /**
     * @todo 获取客户端IP
     * @param int $type
     * @return mixed
     */
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

    /**
     * @tood MD5签名
     * @param $key
     * @param $params
     * @return string
     */
    public static function makeSign($key, $params)
    {
        ksort($params);
        $string = self::toUrlParams($params);
        $string = $string . "&key=".$key;
        $string = md5($string);
        $result = strtoupper($string);
        return $result;
    }

    /***
     * @todo 字符随机数
     * @param int $length
     * @return string
     */
    public static function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

    /***
     * @todo 获取毫秒的时间戳
     * @return array|string
     */
    public static function getMillisecond()
    {
        $time = explode ( " ", microtime () );
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode( ".", $time );
        $time = $time2[0];
        return $time;
    }

    /***
     * @todo 数组转XML字符串
     * @param $values
     * @return string
     */
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

    /**
     * @todo XML字符串转数组
     * @param $xml
     * @return mixed
     */
    public static function fromXml($xml)
    {
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }

    /***
     * @todo 数组转URL
     * @param $values
     * @return string
     */
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

    /***
     * @todo 用Openssl RSA解密
     * @param $data
     * @param $rsa
     * @return int
     */
    public static function rsaOpensslDecode($data, $rsa){
        $private_key = openssl_pkey_get_private($rsa);
        if (!$private_key) {
            return false;
        }
        $return_de = openssl_private_decrypt(base64_decode($data), $decrypted, $private_key);
        if (!$return_de) {
            return false;
        }
        return $decrypted;
    }

    /***
     * @todo 用Openssl RSA加密
     * @param $data
     * @param $rsa
     * @return int|string
     */
    public static function rsaOpensslEncode($data,$rsa){
        //公钥加密
        $key = openssl_pkey_get_public($rsa);
        if (!$key) {
            return false;
        }
        $return_en = openssl_public_encrypt($data, $crypted, $key, OPENSSL_PKCS1_OAEP_PADDING);
        if (!$return_en) {
            return false;
        }
        return base64_encode($crypted);
    }

    public static function sodiumAes256gcmDecrypt($key,$associated_data,$ciphertext,$nonce){
        $check_sodium_mod = extension_loaded('sodium');
        if($check_sodium_mod === false){
            return false;
        }
        $check_aes256gcm = sodium_crypto_aead_aes256gcm_is_available();
        if($check_aes256gcm === false){
            return false;
        }

        $pem = sodium_crypto_aead_aes256gcm_decrypt(base64_decode($ciphertext),$associated_data,$nonce,$key);
        return $pem;
    }

}