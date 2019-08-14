<?php
echo phpinfo();
$client = new Yar_client("http://order.bestbox3inter.com/rpc.php");



//$client->SetOpt(YAR_OPT_CONNECT_TIMEOUT, 10);
////Set packager to JSON
//$client->SetOpt(YAR_OPT_PACKAGER, "json");

/* call directly */
var_dump($client->add(1, 2));

/* call via call */
var_dump($client->call("add", array(3, 2)));

echo $client->hehe2(array('name' => 'Test', 'age' => 27))." \n";
/* __add can not be called */
//var_dump($client->_add(1, 2));