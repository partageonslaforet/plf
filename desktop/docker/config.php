<?php

$env = getenv('') ?: 'development';

$config = [];

if ($env === 'production') {
    $config['db_host'] = 'production_db_host';
} else {
    $config['db_host'] = 'localhost';
}

return $config;



#if application use the following

$config = include "config.php";

$db_host = $config['db_host'];
