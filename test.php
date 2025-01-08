<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use GuzzleHttp\Psr7\HttpFactory;

$factory = new HttpFactory;
var_dump($factory);
