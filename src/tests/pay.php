<?php

require 'bootstrap.php';

use Inesadt\Wechat\Pay;

$appid = '';
$mch = '';
$type = 'native';
$key = '';
$notify = 'https://cloud.tencent.com/document/product/583/19695';
$log_path = dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'pay.log';

$pay = new Pay($appid, $mch, $key, $type, $notify, $log_path);
$no = date('YmdHis');
$result = $pay->query(1, '测试',$no,'测试');
var_dump($result);
