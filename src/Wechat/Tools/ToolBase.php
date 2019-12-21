<?php


namespace Inesadt\Wechat\Tools;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;


class ToolBase
{
    protected  $mch_id = '';
    protected $key = '';
    protected $appid = '';
    protected $log_path = '';
    protected $logger = NULL;

    public function __construct($mch_id,$appid,$key,$log_path=NULL)
    {
        $this->mch_id = $mch_id;
        $this->appid = $appid;
        $this->key = $key;
        $this->log_path = $log_path;
        $this->logger = $this->initLogger();
    }


    protected function initLogger()
    {
        $log = new Logger('tool-pay');
        $log->pushHandler(new StreamHandler($this->log_path, Logger::DEBUG));
        return $log;
    }


}