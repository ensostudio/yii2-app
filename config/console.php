<?php

$baseDir = dirname(__DIR__);
$config = [
    'id' => 'console',
    'name' => 'Yii2 CLI application',
    'language' => 'en-US',
    'basePath' => $baseDir,
    'controllerNamespace' => 'app\commands',
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
        '@app/controllers' => '@app/src/controllers',
    ],
    'components' => [
        'cache' => [
            'class' => yii\caching\FileCache::class,
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
    'controllerMap' => [
        'fixture' => [
            'class' => yii\faker\FixtureController::class,
        ],
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => yii\gii\Module::class,
    ];
}

return $config;
