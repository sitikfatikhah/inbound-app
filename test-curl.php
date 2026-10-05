<?php

$ch = curl_init('https://api.github.com');

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'PHP');

$result = curl_exec($ch);

var_dump($result !== false);
var_dump(curl_error($ch));
var_dump(curl_getinfo($ch));

curl_close($ch);
