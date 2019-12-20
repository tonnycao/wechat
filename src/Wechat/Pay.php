<?php


namespace Inesadt\Wechat;
use Inesadt\Wechat\Exceptions\TradeException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/***
 * @todo 支付主类
 * Class Pay
 * @package Inesadt\Wechat
 */
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


        $logger = $this->setLogger($log_path);
        $this->logger = $logger;
        $className = 'Inesadt\\Wechat\\Trade\\'.ucfirst(strtolower($trade_type));
        if(!class_exists($className))
        {
            throw new TradeException();
        }
        $this->tradeType = new $className($logger);

    }

    protected function setLogger($log_path)
    {
        $log = new Logger('wechat-pay');
        $log->pushHandler(new StreamHandler($log_path, Logger::DEBUG));
        return $log;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function order($amount, $desc, $out_trade_no, $product_id='', $device_info='', $openid='', $limit_pay='', $receipt='')
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
        if(!empty($product_id))
        {
            $params['product_id'] = $product_id;
        }
        if(!empty($device_info))
        {
            $params['device_info'] = $device_info;
        }
        if(!empty($openid))
        {
            $params['openid'] = $openid;
        }
        if(!empty($limit_pay) && $limit_pay=='no_credit')
        {
            $params['limit_pay'] = $limit_pay;
        }
        if(!empty($receipt) && $receipt=='Y')
        {
            $params['receipt'] = $receipt;
        }
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

    public function refund($out_trade_no, $refund_out_trade_no, $total_fee, $refund_fee, $refund_desc='', $refund_account='')
    {
        $config = [
            'appid'=>$this->appId,
            'mch_id'=>$this->machId,
            'key'=>$this->key,
            'notify_url'=>$this->notify_url
        ];
        $params = [
            'out_trade_no'=>$out_trade_no,
            'out_refund_no'=>$refund_out_trade_no,
            'total_fee'=>$total_fee,
            'refund_fee'=>$refund_fee
        ];
        if(!empty($refund_desc))
        {
            $params['refund_desc'] = $refund_desc;
        }
        if(!empty($refund_account))
        {
            $params['refund_account'] = $refund_account;
        }
        return $this->tradeType->refund($config, $params);
    }

    public function refundQuery($out_refund_no)
    {

    }
}