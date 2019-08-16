<?php

require 'bootstrap.php';

use Inesadt\Wechat\Pay;

$appid = 'appid098';
$mch = 123456;
$type = 'native';
$key = 'key';
$notify = 'https://cloud.tencent.com/document/product/583/19695';
$log_path = dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'pay.log';

$pay = new Pay($appid, $mch, $key, $type, $notify, $log_path);
$result = $pay->order(1, '123','789456');
var_dump($result);
