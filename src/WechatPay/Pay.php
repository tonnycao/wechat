<?php


namespace Inesadt\WechatPay;
use Inesadt\WechatPay\Exceptions\PayTypeException;

class Pay
{
    const VERSION = 0.2;
    protected $appId = NULL;
    protected $machId = NULL;
    protected $tradeType = NULL;
    protected $key = NULL;
    protected $notify_url = NULL;

    public function __construct($appId, $machId, $key, $trade_type, $notify_url)
    {
        $this->appId = $appId;
        $this->machId = $machId;
        $this->key = $key;
        $this->notify_url = $notify_url;

        try{
            $className = 'Inesadt\\WechatPay\\TradeType\\'.ucfirst($trade_type);
            $this->tradeType = new $className;
            throw new PayTypeException();
        }catch (PayTypeException $e)
        {
            print($e->customFunction());
        }

    }


    public function order($amount, $desc, $out_trade_no)
    {
        $config = [
            'appid'=>$this->appId,
            'mch_id'=>$this->machId,
            'key'=>$this->key
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

    }

    public function close($out_trade_no)
    {

    }

    public function notify(&$data)
    {
        $xml = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        $result = Notify::handle($xml, $data);
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