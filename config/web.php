<?php

$config = [
    'id' => 'web',
    'name' => 'Yii2 application',
    'language' => 'en-US',
    'basePath' => $appDir,
    'controllerNamespace' => 'app\controllers',
    'controllerPath' => $appDir . '/src/controllers',
    'layout' => 'frontend',
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => array_merge(
        require(__DIR__ . '/components.php'),
        [
            'cache' => [
                'class' => yii\caching\FileCache::class,
                'keyPrefix' => 'frontend',
                'gcProbability' => 1000
            ],
            'request' => [
                'cookieValidationKey' => 'k2aVT7nIv4QOrfhMY5yFu5c2h5NFWgxY',
            ],
            'user' => [
                'identityClass' => app\models\User::class,
                'enableAutoLogin' => true,
            ],
            'errorHandler' => [
                'errorAction' => 'site/error',
            ],
        ]
    ),
    'params' => require(__DIR__ . '/params.php'),
    'modules' => require(__DIR__ . '/modules.php'),
    'controllerMap' => [],
    'container' => [
        'definitions' => [
            yii\bootstrap5\ActiveForm::class => [
                'fieldClass' => app\widgets\ActiveField::class
            ],
        ]
    ]
];

// configuration adjustments for development environment
if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => yii\debug\Module::class,
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => yii\gii\Module::class,
        'allowedIPs' => ['127.0.0.1', '::1'],
        'generators' => [
            'module' => [
                'class' => app\modules\gii\generators\module\ModuleGenerator::class,
                'templates' => [
                    'default' => '@app/modules/gii/generators/module/default',
                ]
            ],
            'model' => [
                'class' => app\modules\gii\generators\model\Generator::class,
                'templates' => [
                    'default' => '@app/modules/gii/generators/model/default',
                ]
            ]
        ]
    ];
}

return $config;
