<?php

$config = [
    'id' => 'web',
    'name' => 'Yii2 application',
    'language' => 'en-US',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'app\controllers',
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
        '@app/controllers' => '@app/src/controllers',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'k2aVT7nIv4QOrfhMY5yFu5c2h5NFWgxY',
        ],
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'user' => [
            'identityClass' => \app\models\User::class,
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\swiftmailer\Mailer::class,
            'useFileTransport' => YII_ENV_DEV,
            'enableSwiftMailerLogging' => YII_DEBUG,
            'transport' => [
                'class' => \Swift_SmtpTransport::class,
                // @todo configure SMTP
                'host' => 'localhost',
                // 'username' => 'username',
                // 'password' => 'password',
                // 'port' => 25,
                // 'encryption' => 'tls',
                'plugins' => []
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => YII_DEBUG ? ['error', 'warning', 'info', 'trace'] : ['error', 'warning'],
                ],
            ],
        ],
        'db' => require __DIR__ . '/db.php',
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => require __DIR__ . '/routes.php',
        ],
        'assetManager' => [
            // override bundles to use local project files :
            'bundles' => [
                \yii\bootstrap5\BootstrapAsset::class => [
                    'sourcePath' => '@app/assets/node_modules/bootstrap/dist',
                    'css' => [
                        YII_DEBUG ? 'css/bootstrap.css' : 'css/bootstrap.min.css',
                    ],
                ],
                \yii\bootstrap5\BootstrapPluginAsset::class => [
                    'sourcePath' => '@app/assets/node_modules/bootstrap/dist',
                    'js' => [
                        YII_DEBUG ? 'js/bootstrap.js' : 'js/bootstrap.min.js',
                    ]
                ],
            ],
        ],
    ],
    'params' => require __DIR__ . '/params.php',
    'modules' => require __DIR__ . '/modules.php',
    'controllerMap' => [],
];

// configuration adjustments for development environment
if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => \yii\debug\Module::class,
        // uncomment the following to add your IP if you are not connecting from localhost.
        // 'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => \yii\gii\Module::class,
        // uncomment the following to add your IP if you are not connecting from localhost.
        // 'allowedIPs' => ['127.0.0.1', '::1'],
        // 'generators' => []
    ];
}

return $config;
