<?php
require 'bootstrap.php';

use Inesadt\Wechat\Fund;
$date  = '20190901';
$appid = 'wx9f54fdb0c89382ba';
$mch = 1515530431;
$key = 'otvcloud2015otvcloud2018hahahaha';
$timeout = 100;
$fund = new Fund($appid,$mch,$key,$timeout);
$result = $fund->bill($date,'all');


print_r($result);