<?php


namespace Inesadt\WechatPay;
use Inesadt\WechatPay\Exceptions\PayTypeException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Pay
{
    const VERSION = 0.2;
    protected $appId = NULL;
    protected $machId = NULL;
    protected $tradeType = NULL;
    protected $key = NULL;
    protected $notify_url = NULL;
    protected $log_path = NULL;
    protected $logger = NULL;

    public function __construct($appId, $machId, $key, $trade_type, $notify_url, $log_path)
    {
        $this->appId = $appId;
        $this->machId = $machId;
        $this->key = $key;
        $this->notify_url = $notify_url;
        $this->log_path = $log_path;


        $logger = $this->initLog($log_path);
        $this->logger = $logger;
        $className = 'Inesadt\\WechatPay\\TradeType\\'.ucfirst(strtolower($trade_type));
        if(!class_exists($className))
        {
            throw new PayTypeException();
        }
        $this->tradeType = new $className($logger);

    }

    protected function initLog($log_path)
    {
        $log = new Logger('wechat-pay');
        $log->pushHandler(new StreamHandler($log_path, Logger::DEBUG));
        return $log;
    }

    public function order($amount, $desc, $out_trade_no)
    {
        $config = [
            'appid'=>$this->appId,
            'mch_id'=>$this->machId,
            'key'=>$this->key,
            'notify_url'=>$this->notify_url
        ];
        $params = [
            'total_fee'=>$amount,
            'body'=>$desc,
            'out_trade_no'=>$out_trade_no
        ];
        $response = $this->tradeType->order($config,$params);
        return $response;
    }

    public function query($out_trade_no)
    {
        $config = [
            'appid'=>$this->appId,
            'mch_id'=>$this->machId,
            'key'=>$this->key,
            'notify_url'=>$this->notify_url
        ];
        return $this->tradeType->query($config, $out_trade_no);
    }

    public function close($out_trade_no)
    {
        $config = [
            'appid'=>$this->appId,
            'mch_id'=>$this->machId,
            'key'=>$this->key,
            'notify_url'=>$this->notify_url
        ];
        return $this->tradeType->close($config, $out_trade_no);
    }

    public function notify(&$data)
    {
        $xml = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        $this->logger->debug($xml);
        $result = Notify::handle($this->key, $xml, $data);
        if($result['code']==1)
        {
            Notify::replyOk();
        }else{
            Notify::replyFail($result['msg']);
        }
        return $result;
    }

    public function refund($out_trade_no, $refund_out_trade_no, $total_fee, $refund_fee)
    {

    }

    public function refundQuery($out_refund_no)
    {

    }
}