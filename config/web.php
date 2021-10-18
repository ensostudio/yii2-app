<?php

$baseDir = dirname(__DIR__);

$config = [
    'id' => 'web',
    'name' => 'Yii2 application',
    'language' => 'en-US',
    'basePath' => $baseDir,
    'controllerNamespace' => 'app\controllers',
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset'
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'k2aVT7nIv4QOrfhMY5yFu5c2h5NFWgxY',
        ],
        'cache' => [
            'class' => yii\caching\FileCache::class,
        ],
        'user' => [
            'identityClass' => app\models\User::class,
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => yii\swiftmailer\Mailer::class,
            'useFileTransport' => YII_ENV_DEV,
            'enableSwiftMailerLogging' => YII_DEBUG,
            'transport' => [
                'class' => Swift_SmtpTransport::class,
                // @todo configure SMTP
                'host' => 'localhost',
                // 'username' => 'username',
                // 'password' => 'password',
                // 'port' => 25,
                // 'encryption' => 'tls',
                'plugins' => [
                    // ['class' => Swift_Plugins_ThrottlerPlugin::class, 'constructArgs' => [20]],
                ]
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => yii\log\FileTarget::class,
                    'levels' => YII_DEBUG ? ['error', 'warning', 'info', 'trace'] : ['error', 'warning'],
                ],
            ],
        ],
        'db' => require __DIR__ . '/db.php',
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => require __DIR__ . '/rules.php',
        ],
    ],
    'params' => require __DIR__ . '/params.php',
    'modules' => [],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => yii\debug\Module::class,
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => yii\gii\Module::class,
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
