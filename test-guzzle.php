<?php

use GuzzleHttp\Client;

require __DIR__.'/vendor/autoload.php';

$client = new Client;

$r = new ReflectionClass($client);

var_dump($r->getDefaultProperties());
