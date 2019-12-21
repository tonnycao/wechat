<?php

namespace Inesadt\Wechat;

/**
 * @todo 微信服务交互类
 * Class Api
 * @package Inesadt\Wechat
 */
class Api
{
    const VERSION = 1.0;
    protected static $logger = NULL;

    /***
     * @todo 设置日志引擎
     * @param $logger
     */
    public static function setLogger($logger)
    {
        if(!isset(self::$logger))
        {
            self::$logger = $logger;
        }
    }

    /***
     * @todo 获取日志引擎
     * @return null
     */
    public static function getLogger()
    {
        return self::$logger;
    }

    /**
     * @todo 统一下单
     * @param $config
     * @param $param
     * @return bool|mixed
     */
    public static function unifiedOrder($config, $param)
    {
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $data = $param;
        $data['appid'] = $config['appid'];
        $data['mch_id'] = $config['mch_id'];
        $data['notify_url'] = $config['notify_url'];

        $data['time_start'] = date('YmdHis');
        $data['time_expire'] = date('YmdHis',time()+ 1800);
        $data['spbill_create_ip'] = Util::getClientIp();
        $data['nonce_str'] = Util::getNonceStr();
        $data['sign_type'] = 'MD5';
        $data['sign'] = Util::makeSign($config['key'], $data);

        $xml = Util::toXml($data);
        $response = self::postXmlCurl($xml, $url);
        if(!$response)
        {
            return false;
        }
        $result =  Util::fromXml($response);
        if(!$result){
            return $result;
        }
        $result['client_ip'] = $data['spbill_create_ip'];
        $result['time_start'] = $data['time_start'];
        $result['time_expire'] = $data['time_expire'];
        return $result;
    }

    /**
     * @todo 查询订单状态
     * @param $config
     * @param $out_trade_no
     * @return bool|mixed
     */
    public static function orderQuery($config, $out_trade_no)
    {
        $url = 'https://api.mch.weixin.qq.com/pay/orderquery';
        $params = [
            'appid'=>$config['appid'],
            'mch_id'=>$config['mch_id'],
            'nonce_str'=>Util::getNonceStr(),
            'out_trade_no'=>$out_trade_no
        ];
        $params['sign_type'] = 'MD5';
        $params['sign'] = Util::makeSign($config['key'],$params);

        $xml = Util::toXml($params);
        $response = self::postXmlCurl($xml, $url);

        if(!$response)
        {
            return false;
        }
        return Util::fromXml($response);
    }

    /***
     * @todo 付款码支付
     * @param $config
     * @param $param
     * @return bool|mixed
     */
    public static function micropay($config,$param)
    {

        $data = $param;
        $data['appid'] = $config['appid'];
        $data['mch_id'] = $config['mch_id'];
        $data['time_start'] = date('YmdHis');
        $data['time_expire'] = date('YmdHis',time()+ 1800);
        $data['spbill_create_ip'] = Util::getClientIp();
        $data['nonce_str'] = Util::getNonceStr();
        $data['sign_type'] = 'MD5';
        $data['sign'] = Util::makeSign($config['key'], $data);

        $xml = Util::toXml($data);
        $response = self::postXmlCurl($xml);

        if(!$response){
            return false;
        }
        return Util::fromXml($response);
    }
    /**
     * @todo 支付二维码短链接
     * @param $config
     * @param $long_url
     * @return bool|mixed
     */
    public static function shortUrl($config, $long_url)
    {
        $url = 'https://api.mch.weixin.qq.com/tools/shorturl';
        $data = [
            'appid'=>$config['appid'],
            'mch_id'=>$config['mch_id'],
            'nonce_str'=>Util::getNonceStr(),
            'long_url'=>$long_url,
            'sign_type'=>'MD5'
        ];

        $xml = Util::toXml($data);
        $response = self::postXmlCurl($xml, $url);

        if(!$response)
        {
            return false;
        }
        return Util::fromXml($response);
    }

    /***
     * @todo 撤销订单
     * @param $config
     * @param $transaction_id
     * @param $out_trade_no
     * @return bool|mixed
     *
     */
    public static function reverse($config, $transaction_id, $out_trade_no)
    {
        $url = 'https://api.mch.weixin.qq.com/secapi/pay/reverse';
        $data = [
            'appid'=>$config['appid'],
            'mch_id'=>$config['mch_id'],
            'nonce_str'=>Util::getNonceStr(),
            'out_trade_no'=>$out_trade_no,
            'transaction_id'=>$transaction_id,
        ];

        $data['sign_type'] = 'MD5';
        $data['sign'] = Util::makeSign($config['key'],$data);
        $xml = Util::toXml($data);
        $sslCertPath = $config['cert_path'];
        $sslKeyPath = $config['key_path'];

        $response = self::postXmlCurl($xml, $url, 2,true, $sslCertPath, $sslKeyPath);
        if(!$response)
        {
            return false;
        }
        return Util::fromXml($response);
    }

    /***
     * @todo 关闭订单
     * @param $config
     * @param $out_trade_no
     * @return bool|mixed
     */
    public static function closeOrder($config, $out_trade_no)
    {
        $url = 'https://api.mch.weixin.qq.com/pay/closeorder';
        $data = [
            'appid'=>$config['appid'],
            'mch_id'=>$config['mch_id'],
            'out_trade_no'=>$out_trade_no,
            'nonce_str'=>Util::getNonceStr(),
            'sign_type'=>'MD5'
        ];
        $xml = Util::toXml($data);
        $response = self::postXmlCurl($xml, $url);
        if(!$response)
        {
            return false;
        }
        return Util::fromXml($response);
    }


    /***
     * @todo 发起退款
     * @param $config
     * @param $param
     * @return bool|mixed
     */
    public static function refund($config, $param)
    {
        $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
        $data = $param;
        $data['appid'] = $config['appid'];
        $data['mch_id'] = $config['mch_id'];
        if(!empty($config['notify_url']))
        {
            $data['notify_url'] = $config['notify_url'];
        }

        $data['nonce_str'] = Util::getNonceStr();
        $data['sign_type'] = 'MD5';
        $data['sign'] = Util::makeSign($config['key'], $data);
        $sslCertPath = $param['cert_path'];
        $sslKeyPath = $param['key_path'];
        $xml = Util::toXml($data);
        $response = self::postXmlCurl($xml, $url, 2,true, $sslCertPath, $sslKeyPath);
        if(!$response)
        {
            return false;
        }
        return Util::fromXml($response);
    }

    /***
     * @todo 查询退款状态
     * @param $config
     * @param $refund_order_no
     * @param int $offset
     * @return bool|mixed
     */
    public static function refundQuery($config, $refund_order_no,$offset=0)
    {
        $url = 'https://api.mch.weixin.qq.com/pay/refundquery';
        $data = [
            'appid'=>$config['appid'],
            'mch_id'=>$config['mch_id'],
            'out_refund_no'=>$refund_order_no,
            'nonce_str'=>Util::getNonceStr(),
            'sign_type'=>'MD5'
        ];
        if($offset>0)
        {
            $data['offset'] = $offset;
        }
        $xml = Util::toXml($data);
        $response = self::postXmlCurl($xml, $url);
        if(!$response)
        {
            return false;
        }
        return Util::fromXml($response);
    }

    /***
     * @todo 付款授权码查询openid
     * @param $config
     * @param $auth_code
     * @return bool|mixed
     */
    public static function authcode2openid($config, $auth_code)
    {
        $url = 'https://api.mch.weixin.qq.com/tools/authcodetoopenid';
        $param = [
            'appid'=>$config['appid'],
            'mch_id'=>$config['mch_id'],
            'auth_code'=>$auth_code,
            'nonce_str'=>Util::getNonceStr(),
        ];
        $param['sign'] = Util::makeSign($config['key'],$param);
        $xml = Util::toXml($param);
        $response = self::postXmlCurl($xml,$url);
        if(!$response){
            return false;
        }
        return Util::fromXml($response);
    }

    /**
     * @todo 下载账单
     * @param $config
     * @param $bill_date
     * @param $bill_type
     * @param string $tar_type
     * @return bool|mixed|string
     */
    public static function downloadBill($config,$bill_date,$bill_type, $tar_type='GZIP')
    {
        $url = 'https://api.mch.weixin.qq.com/pay/downloadbill';
        $data = [
            'appid'=>$config['appid'],
            'mch_id'=>$config['mch_id'],
            'bill_date'=>$bill_date,
            'bill_type'=>$bill_type,
            'nonce_str'=>Util::getNonceStr(),
            'sign_type'=>'MD5'
        ];
        if($tar_type=='GZIP')
        {
            $data['tar_type'] = $tar_type;
        }
        if(empty($config['timeout']))
        {
            $config['timeout'] = 100;
        }
        $data['sign'] = Util::makeSign($config['key'], $data);
        $xml = Util::toXml($data);
        $response = self::postXmlCurl($xml, $url,$config['timeout']);

        if(!$response)
        {
            return false;
        }
        if($tar_type=='GZIP')
        {
            $bills = Util::handleGzipData($response);
        }else{
            $bills = $response;
        }

        return $bills;
    }

    /***
     * @todo 下载账单流
     * @param $config
     * @param $bill_date
     * @param $account_type
     * @param string $tar_type
     * @return bool|mixed|string
     */
    public static function downloadFundFlow($config,$bill_date,$account_type,$tar_type='GZIP')
    {
        $url = 'https://api.mch.weixin.qq.com/pay/downloadfundflow';
        $data = [
            'appid'=>$config['appid'],
            'mch_id'=>$config['mch_id'],
            'bill_date'=>$bill_date,
            'account_type'=>$account_type,
            'tar_type'=>$tar_type,
            'nonce_str'=>Util::getNonceStr(),
            'sign_type'=>'HMAC-SHA256'
        ];
        if(empty($config['timeout']))
        {
            $config['timeout'] = 100;
        }
        $data['sign'] = Util::makeHmacSign($config['key'], $data);
        $xml = Util::toXml($data);
        $response = self::postXmlCurl($xml, $url,$config['timeout'],true,$config['ssl_cert_path'],$config['ssl_key_path']);
        if(!$response)
        {
            return false;
        }
        if($tar_type=='GZIP')
        {
            $bills = Util::handleGzipData($response);
        }else{
            $bills = $response;
        }

        return $bills;
    }

    /**
     * @todo 查询支付评价带分页
     * @param $config
     * @param $begin_time
     * @param $end_time
     * @param $offset
     * @param int $limit
     * @return bool|string
     */
    public static function batchQueryComment($config,$begin_time,$end_time,$offset,$limit=20)
    {
        $url = 'https://api.mch.weixin.qq.com/billcommentsp/batchquerycomment';
        $data = [
            'appid'=>$config['appid'],
            'mch_id'=>$config['mch_id'],
            'begin_time'=>$begin_time,
            'end_time'=>$end_time,
            'offset'=>$offset,
            'limit'=>$limit,
            'nonce_str'=>Util::getNonceStr(),
            'sign_type'=>'HMAC-SHA256'
        ];
        if(empty($config['timeout']))
        {
            $config['timeout'] = 100;
        }
        $data['sign'] = Util::makeHmacSign($config['key'], $data);
        $xml = Util::toXml($data);
        $response = self::postXmlCurl($xml, $url,$config['timeout'],true,$config['ssl_cert_path'],$config['ssl_key_path']);
        return $response;
    }

    /**
     * @todo curl post请求
     * @param $xml
     * @param $url
     * @param int $second
     * @param bool $useCert
     * @param string $sslCertPath
     * @param string $sslKeyPath
     * @return bool|string
     */
    public static function postXmlCurl($xml, $url, $second = 2, $useCert = false, $sslCertPath='', $sslKeyPath='')
    {
        if(isset(self::$logger))
        {
            self::$logger->debug($xml);
        }

        $ch = curl_init();
        $curlVersion = curl_version();
        $ua = "WXPay/".self::VERSION." (".PHP_OS.") PHP/".PHP_VERSION." CURL/".$curlVersion['version'];
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);//严格校验
        curl_setopt($ch,CURLOPT_USERAGENT, $ua);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if($useCert == true){
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            //证书文件请放入服务器的非web目录下
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, $sslCertPath);
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, $sslKeyPath);
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);

        //返回结果
        if($data){
            if(isset(self::$logger))
            {
                self::$logger->debug(json_encode($data));
            }
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            $msg = [
                'errorno'=>$error,
                'errormsg'=>Errors::curlCodeMsg($error)
            ];
            if(isset(self::$logger))
            {
                self::$logger->error(json_encode($msg));
            }
           return false;
        }
    }

}