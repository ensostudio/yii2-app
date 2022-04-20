<?php

/**
 * Configuration of common application's components
 */

return [
    'cache' => [
        'class' => yii\caching\FileCache::class,
        'keyPrefix' => 'cli',
        'defaultDuration' => YII_DEBUG ? 0 : 86400,
        'gcProbability' => 1000
    ],
    'db' => require(__DIR__ . '/db.php'),
    'mailer' => [
        'class' => yii\symfonymailer\Mailer::class,
        'useFileTransport' => YII_DEBUG,
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
    /*
    'urlManager' => [
        'enablePrettyUrl' => true,
        'showScriptName' => false,
        'rules' => [],
    ],
    */
    'authManager' => [
        'class' => yii\rbac\DbManager::class,
    ],
    'i18n' => [
        'translations' => [
            'app' => [
                'class' => yii\i18n\PhpMessageSource::class,
                'basePath' => '@app/messages',
            ],
            'app/*' => [
                'class' => yii\i18n\PhpMessageSource::class,
                'basePath' => '@app/messages',
            ],
        ],
    ],
    'assetManager' => [
        // Override bundles to use minified files in production:
        'bundles' => [
            yii\bootstrap5\BootstrapAsset::class => [
                'sourcePath' => '@bower/bootstrap/dist',
                'css' => [
                    YII_ENV_PROD ? 'css/bootstrap.min.css' : 'css/bootstrap.css',
                ],
            ],
            yii\bootstrap5\BootstrapPluginAsset::class => [
                'sourcePath' => '@bower/bootstrap/dist',
                'js' => [
                    YII_ENV_PROD ? 'js/bootstrap.bundle.min.js' : 'js/bootstrap.bundle.js',
                ]
            ],
        ],
    ],
];
