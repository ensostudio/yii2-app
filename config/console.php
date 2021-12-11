<?php

$config = [
    'id' => 'console',
    'name' => 'Yii2 CLI application',
    'language' => 'en-US',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'app\commands',
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@app/commands' => '@app/src/commands',
        '@app/migrations' => '@app/src/migrations',
    ],
    'params' => require __DIR__ . '/params.php',
    'components' => [
        'db' => require __DIR__ . '/db.php',
        'cache' => [
            'class' => yii\caching\FileCache::class,
        ],
        'mailer' => [
            'class' => yii\swiftmailer\Mailer::class,
            'useFileTransport' => YII_ENV_DEV,
            'enableSwiftMailerLogging' => YII_DEBUG,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => yii\log\FileTarget::class,
                    'levels' => YII_DEBUG ? ['error', 'warning', 'info'] : ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => require __DIR__ . '/routes.php',
        ],
        'authManager' => [
            'class' => yii\rbac\DbManager::class,
        ],
        'i18n' => [
            'translations' => [
                'app/*' => [
                    'class' => yii\i18n\PhpMessageSource::class,
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'en-US',
                ],
            ],
        ],
    ],
    'controllerMap' => [
        // @see https://github.com/fzaninotto/Faker
        'fixture' => [
            'class' => yii\faker\FixtureController::class,
            'language' => 'ru_RU',
        ],
        'migrate-ns' => [
            'class' => yii\console\controllers\MigrateController::class,
            'templateFile' => '@app/views/migrations/migration.php',
            'generatorTemplateFiles' => [
                'create_table' => '@app/views/migrations/createTable.php',
                'add_column' => '@app/views/migrations/addColumn.php',
            ],
            'migrationPath' => null,
            'migrationNamespaces' => ['app\migrations'],
        ],
        'migrate' => [
            'class' => yii\console\controllers\MigrateController::class,
            'templateFile' => '@app/views/migrations/migration.php',
            'generatorTemplateFiles' => [
                'create_table' => '@app/views/migrations/createTable.php',
                'add_column' => '@app/views/migrations/addColumn.php',
            ],
            'migrationPath' =>  [
                '@yii/rbac/migrations',
                '@yii/web/migrations',
                '@yii/caching/migrations',
                '@yii/i18n/migrations',
                '@yii/log/migrations',
            ]
        ]
    ],
    'modules' => require __DIR__ . '/modules.php',
];

// configuration adjustments for development environment
if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => yii\gii\Module::class,
    ];
}

return $config;
