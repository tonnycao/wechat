<?php


namespace Inesadt\WechatPay;
use Inesadt\WechatPay\Exceptions\PayTypeException;

class Pay
{
    const VERSION = 0.1;
    protected $appId = NULL;
    protected $machId = NULL;
    protected $tradeType = NULL;
    protected $key = NULL;

    public function __construct($appId, $machId, $key, $trade_type)
    {
        $this->appId = $appId;
        $this->machId = $machId;
        $this->key = $key;

        try{
            $className = 'Inesadt\\WechatPay\\TradeType\\'.ucfirst($trade_type);
            $this->tradeType = new $className;
            throw new PayTypeException();
        }catch (PayTypeException $e)
        {
            print($e->customFunction());
        }

    }


    public function order($params)
    {
        $config = [
            'appid'=>$this->appId,
            'mch_id'=>$this->machId,
            'key'=>$this->key
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

    public function notify()
    {
        $xml = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        $data = [];
        $result = Notify::handle($xml, $data);
        if($result['code']==1)
        {
            Notify::replyOk();
        }else{
            Notify::replyFail($result['msg']);
        }
        return $data;
    }

    public function refund($out_trade_no, $refund_out_trade_no, $total_fee, $refund_fee)
    {

    }

    public function refundQuery($out_refund_no)
    {

    }
}