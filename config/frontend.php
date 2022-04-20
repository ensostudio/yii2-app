<?php

$config = [
    'id' => 'frontend',
    'name' => 'Front-end application',
    'language' => 'en',
    'basePath' => $rootDir,
    'controllerNamespace' => 'app\controllers',
    'controllerPath' => $rootDir . '/src/controllers',
    'layout' => 'frontend',
    'defaultRoute' => 'frontend/site/index',
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
                'defaultDuration' => YII_DEBUG ? 0 : 86400,
                'gcProbability' => 1000,
            ],
            'request' => [
                'cookieValidationKey' => 'k2aVT7nIv4QOrfhMY5yFu5c2h5NFWgxY',
            ],
            'user' => [
                'identityClass' => app\models\User::class,
                'enableAutoLogin' => true,
                'loginUrl' => ['account/login'],
                'identityCookie' => ['name' => '_identity', 'httpOnly' => true, 'secure' => true],
            ],
            'errorHandler' => [
                'errorAction' => 'site/error',
            ],
            'urlManager' => [
                'class' => yii\web\UrlManager::class,
                'enablePrettyUrl' => true,
                'showScriptName' => false,
                'normalizer' => [
                    'class' => yii\web\UrlNormalizer::class,
                    'collapseSlashes' => true,
                    'normalizeTrailingSlash' => true,
                ],
                'rules' => [
                    new yii\web\GroupUrlRule([
                        'rules' => [
                            'site' => 'site/index',
                        ]
                    ])
                ],
            ],
        ]
    ),
    'bootstrap' => ['log'],
    'modules' => require(__DIR__ . '/modules.php'),
    'params' => require(__DIR__ . '/params.php'),
    'controllerMap' => [],
    'container' => [
        'definitions' => [],
        'singletons' => []
    ]
];

// Configuration adjustments for development environment
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
