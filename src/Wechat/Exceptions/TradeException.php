<?php


namespace Inesadt\Wechat\Exceptions;



class TradeException extends \Exception
{

    public function __construct()
    {

        $message = '支付方式不支持';
        $code = -1;
        parent::__construct($message, $code);
    }

    public function __toString()
    {
        return __CLASS__ . ":[" . $this->code . "]:" . $this->message;
    }

    public function customFunction()
    {
       return [
         'code'=>$this->code,
           'msg'=>$this->getMessage()
       ];
    }
}