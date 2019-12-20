<?php


namespace Inesadt\Wechat\Notify;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * @todo 通知基类
 * Class Base
 * @package Inesadt\Wechat\Notify
 */
class Base
{
    protected $raw_xml = '';
    protected $result = [];
    protected $log_path = '';
    protected $key = '';
    protected $logger = NULL;

    public function __construct($key,$log_path)
    {
        $this->key = $key;
        $this->log_path = $log_path;
        $this->logger = $this->initLogger();
    }

    protected function initLogger()
    {
        $log = new Logger('notify-pay');
        $log->pushHandler(new StreamHandler($this->log_path, Logger::DEBUG));
        return $log;
    }

    public function getRawXml()
    {
        return $this->raw_xml;
    }

    public function getResult()
    {
        return $this->result;
    }

}