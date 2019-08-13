<?php


namespace Inesadt\WechatPay;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Api
{

    public static function unifiedorder($config, $param)
    {
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $data = $param;
        $data['appid'] = $config['appid'];
        $data['mch_id'] = $config['mch_id'];
        $data['time_start'] = date('YmdHis');
        $data['time_expire'] = date('YmdHis',time()+ 600);
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
        return Util::fromXml($response);
    }

    public static function orderquery($config, $out_trade_no)
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

    public static function shorturl($config, $long_url)
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

    public static function closeorder($config, $out_trade_no)
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

    public static function report($config, $params)
    {
        $url = 'https://api.mch.weixin.qq.com/payitil/report';
        $data = [];

        $xml = Util::toXml($data);
        $response = self::postXmlCurl($xml, $url);
        if(!$response)
        {
            return false;
        }
        return Util::fromXml($response);
    }

    private static function postXmlCurl($xml, $url, $useCert = false, $second = 20)
    {
        self::logger('debug', $xml);
        $ch = curl_init();
        $curlVersion = curl_version();
        $ua = "WXPay/".self::$VERSION." (".PHP_OS.") PHP/".PHP_VERSION." CURL/".$curlVersion['version'];

        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
        curl_setopt($ch,CURLOPT_USERAGENT, $ua);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if($useCert == true){
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            //证书文件请放入服务器的非web目录下
            $sslCertPath = "";
            $sslKeyPath = "";
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
            self::logger('debug', $data);
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            $msg = [
                'errorno'=>$error,
                'errormsg'=>Errors::curlCodeMsg($error)
            ];
           self::logger('error', $msg);
           return false;
        }
    }

    protected static function logger($level, $data)
    {
        $log = new Logger('wechat-pay');
        $root_path = dirname(__DIR__);
        $log_path = $root_path.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'wechat-pay.log';
        $log->pushHandler(new StreamHandler($log_path, Logger::DEBUG));
        $msg = $data;
        if(is_array($data))
        {
            $msg = json_encode($data);
        }
        switch ($level)
        {
            case 'warning':
                $log->warning($msg);
                break;
            case 'error':
                $log->error($msg);
                break;
            case 'debug':
                $log->debug($msg);
                break;
            case 'info':
                $log->info($msg);
                break;
        }

    }
}