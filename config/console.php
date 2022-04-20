<?php

$config = [
    'id' => 'console',
    'name' => 'CLI application',
    'basePath' => $appDir,
    'controllerNamespace' => 'app\commands',
    'controllerPath' => $appDir . '/src/commands',
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
        '@tests' => '@app/tests',
        '@migrations' => '@app/migrations',
    ],
    'params' => require(__DIR__ . '/params.php'),
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
    'controllerMap' => [
        // @see https://github.com/fzaninotto/Faker
        'fixture' => [
            'class' => yii\faker\FixtureController::class,
            'language' => 'en-US',
        ],
        'migrate' => [
            'class' => yii\console\controllers\MigrateController::class,
            'templateFile' => '@app/views/migrations/migration.php',
            'generatorTemplateFiles' => [
                'create_table' => '@app/views/migrations/createTable.php',
                'add_column' => '@app/views/migrations/addColumn.php',
            ],
            'migrationPath' => null,
            'migrationNamespaces' => ['migrations'],
        ],
        'migrate-yii' => [
            'class' => yii\console\controllers\MigrateController::class,
            'templateFile' => '@app/views/migrations/migration.php',
            'generatorTemplateFiles' => [
                'create_table' => '@app/views/migrations/createTable.php',
                'add_column' => '@app/views/migrations/addColumn.php',
            ],
            'migrationPath' =>  [
                '@yii/web/migrations',
                '@yii/rbac/migrations',
                // '@yii/caching/migrations',
                // '@yii/i18n/migrations',
                // '@yii/log/migrations',
            ]
        ]
    ],
    'modules' => require(__DIR__ . '/modules.php'),
];

// configuration adjustments for development environment
if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = ['class' => yii\gii\Module::class];
}

return $config;
