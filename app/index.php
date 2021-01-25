<?php

use GuzzleHttp\Client;
use Test\TestApi\Manager\TestApiManager;

require '../vendor/autoload.php';
$config = require('../config/config.php');

try {
    if (!array_key_exists('test', $config) || !is_array($config['test'])) {
        throw new RuntimeException('Configuration is invalid!');
    }
    $client = new Client();
    $testApi = new TestApiManager($config['test'], $client);
} catch (Throwable $e) {
    error_log('Index error:' . $e->getMessage());
}
