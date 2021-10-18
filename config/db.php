<?php

return [
    'class' => yii\db\Connection::class,
    // @todo set database settings
    'dsn' => 'mysql:host=127.0.0.1:3306;dbname=yii',
    'username' => 'root',
    'password' => 'root',
    'charset' => 'utf8',
    // Schema cache options (for production environment)
    'enableSchemaCache' => YII_ENV_PROD,
    'schemaCacheDuration' => 3600,
    'schemaCache' => 'cache',
];
