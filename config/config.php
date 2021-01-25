<?php

declare(strict_types=1);

namespace Test\TestApi\Config;

use Test\TestApi\Manager\TestApiManager;

return [
    'test' => [
        'api' => [
            'url' => getenv('API_URL') ?: TestApiManager::DEFAULT_API_URL,
            'login' => getenv('LOGIN') ?: 'test',
            'pass' =>getenv('PASS') ?: '12345',
        ],
    ],
];
