<?php

/**
 * Application configuration shared by all test types
 */

return [
    'id' => 'tests',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'language' => 'en-US',
    'components' => [
        [
            'keyPrefix' => 'test',
        ],
        'db' => [
            'dsn' => 'mysql:host=127.0.0.1:3306;dbname=test',
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
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
            // but if you absolutely need it set cookie domain to localhost
            /*
            'csrfCookie' => [
                'domain' => 'localhost',
            ],
            */
        ],
    ],
    'params' => require(__DIR__ . '/params.php'),
];
