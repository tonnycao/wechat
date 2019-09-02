<?php


namespace Inesadt\Wechat;

class Api
{
    const VERSION = 0.2;
    protected static $logger = NULL;

    public static function setLogger($logger)
    {
        if(!isset(self::$logger))
        {
            self::$logger = $logger;
        }
    }

    public static function getLogger()
    {
        return self::$logger;
    }

    public static function unifiedOrder($config, $param)
    {
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $data = $param;
        $data['appid'] = $config['appid'];
        $data['mch_id'] = $config['mch_id'];
        $data['notify_url'] = $config['notify_url'];

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

        $xml = Util::toXml($data);
        $response = self::postXmlCurl($xml, $url);
        if(!$response)
        {
            return false;
        }
        return Util::fromXml($response);
    }

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

    private static function postXmlCurl($xml, $url, $useCert = false, $second = 20)
    {
        self::$logger->debug($xml);
        $ch = curl_init();
        $curlVersion = curl_version();
        $ua = "WXPay/".self::VERSION." (".PHP_OS.") PHP/".PHP_VERSION." CURL/".$curlVersion['version'];

        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);//严格校验
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
            self::$logger->debug(json_encode($data));
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            $msg = [
                'errorno'=>$error,
                'errormsg'=>Errors::curlCodeMsg($error)
            ];
            self::$logger->error(json_encode($msg));
           return false;
        }
    }

}