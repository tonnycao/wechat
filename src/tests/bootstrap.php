<?php

error_reporting(E_ALL | E_STRICT);


date_default_timezone_set('Asia/Shanghai');

$root_path = dirname(dirname(__DIR__));
// Ensure that composer has installed all dependencies
if (!file_exists($root_path . '/composer.lock')) {
    die("Dependencies must be installed using composer:\n\nphp composer.phar install --dev\n\n"
        . "See http://getcomposer.org for help with installing composer\n");
}

// Include the composer autoloader
require_once $root_path . '/vendor/autoload.php';