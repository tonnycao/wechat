<?php


namespace Inesadt\Wechat;


class Comment
{
    const VERSION = 0.1;
    protected $appId = NULL;
    protected $mchId = NULL;
    protected $key = NULL;
    protected $timeout = 10;

    public function __construct($appId,$mchId, $key,$timeout=20)
    {
        $this->appId = $appId;
        $this->mchId = $mchId;
        $this->key = $key;
        $this->timeout = $timeout;
    }

    public function batchQueryComment($ssl_cert_path,$ssl_key_path,$begin_time,$end_time,$offset=1, $limit=100)
    {
        $config = [
            'appid'=>$this->appId,
            'mch_id'=>$this->mchId,
            'key'=>$this->key,
            'timeout'=>$this->timeout,
            'ssl_key_path'=>$ssl_key_path,
            'ssl_cert_path'=>$ssl_cert_path,
        ];
        $response = Api::batchQueryComment($config,$begin_time,$end_time,$offset,$limit);
        return $response;
    }
}