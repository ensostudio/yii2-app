<?php

return [
    'class' => yii\db\Connection::class,
    'dsn' => 'mysql:host=127.0.0.1:3306;dbname=yii2app',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
    // Schema cache options for production
    'enableSchemaCache' => YII_ENV_PROD,
    'schemaCacheDuration' => 86400,
    'schemaCache' => 'cache',
];
