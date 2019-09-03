<?php
require 'bootstrap.php';

use Inesadt\Wechat\Fund;
$date  = '';
$appid = '';
$mch = '';
$key = 'otvcloud2015otvcloud2018hahahaha';
$timeout = 100;
$fund = new Fund($appid,$mch,$key,$timeout);
$result = $fund->bill($date,'all');


print_r($result);