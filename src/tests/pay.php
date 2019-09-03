<?php

require 'bootstrap.php';

use Inesadt\Wechat\Pay;

$appid = 'wx9f54fdb0c89382ba';
$mch = 1515530431;
$type = 'native';
$key = 'otvcloud2015otvcloud2018hahahaha';
$notify = 'https://cloud.tencent.com/document/product/583/19695';
$log_path = dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'pay.log';

$pay = new Pay($appid, $mch, $key, $type, $notify, $log_path);
$result = $pay->order(1, '123','789456');
var_dump($result);
