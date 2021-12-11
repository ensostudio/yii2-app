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
    'components' => require __DIR__ . '/components.php',
    'params' => require __DIR__ . '/params.php',
    'modules' => require __DIR__ . '/modules.php',
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
        // uncomment the following to add your IP if you are not connecting from localhost.
        // 'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => yii\gii\Module::class,
        // uncomment the following to add your IP if you are not connecting from localhost.
        // 'allowedIPs' => ['127.0.0.1', '::1'],
        'generators' => [
            'module' => [
                'class' => app\modules\gii\generators\module\Generator::class,
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
