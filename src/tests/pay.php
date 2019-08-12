<?php

require 'bootstrap.php';

use Inesadt\WechatPay\Pay;

$appid = 'appid098';
$mch = 123456;
$type = 'native';
$pay = new Pay($appid, $mch, $type);
$pay->order([]);
