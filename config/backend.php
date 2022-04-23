<?php

$config = [
    'id' => 'backend',
    'name' => 'Back-end application',
    'language' => 'en',
    'basePath' => $rootDir,
    'controllerNamespace' => 'app\controllers\backend',
    'controllerPath' => $rootDir . '/src/controllers/backend',
    //'viewPath' => '@app/views/backend',
    'layout' => 'backend',
    'defaultRoute' => 'backend/site/index',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => array_merge(
        require(__DIR__ . '/components.php'),
        [
            'cache' => [
                'class' => yii\caching\FileCache::class,
                'keyPrefix' => 'backend',
                'defaultDuration' => YII_DEBUG ? 0 : 86400,
                'gcProbability' => 10000
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
                'enableStrictParsing' => false,
                'normalizer' => [
                    'class' => yii\web\UrlNormalizer::class,
                    'collapseSlashes' => true,
                    'normalizeTrailingSlash' => true,
                ],
                'rules' => [
                    new yii\web\GroupUrlRule([
                        'routePrefix' => 'backend',
                        'prefix' => 'backend',
                        'rules' => [
                            '<controller:[-\w]+>' => '<controller>/index',
                            [
                                'pattern' => '<controller:[-\w]+>/<action:[-a-z\d]+>(/page-<page:\d+>)',
                                'route' => '<controller>/<action>',
                                'defaults' => ['page' => 1],
                            ],
                            [
                                'pattern' => '<controller:[-\w]+>/<action:[-\w]+>/<id:\d+>(/page-<page:\d+>)',
                                'route' => '<controller>/<action>',
                                'defaults' => ['page' => 1],
                            ],
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
        'definitions' => [
            yii\web\Controller::class => [
                'viewPath' => '@app/views/backend',
            ]
        ],
        'singletons' => []
    ]
];

return $config;
