<?php

/**
 * Application configuration shared by all test types
 */

return [
    'id' => 'tests',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@tests' => '@app/tests',
    ],
    'language' => 'en-US',
    'components' => [
        'cache' => [
            'keyPrefix' => 'test',
        ],
        'db' => [
            'dsn' => 'mysql:host=127.0.0.1:3306;dbname=yii2test',
        ],
        'mailer' => [
            'useFileTransport' => true,
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'user' => [
            'identityClass' => app\models\User::class,
        ],
    ],
    'params' => [],
];
