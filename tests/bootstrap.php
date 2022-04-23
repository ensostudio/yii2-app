<?php

defined('YII_DEBUG') || define('YII_DEBUG', true);
defined('YII_ENV') || define('YII_ENV', 'test');

$rootDir = dirname(__DIR__);

require $rootDir . '/vendor/yiisoft/yii2/Yii.php';
require $rootDir . '/vendor/autoload.php';

new yii\console\Application(
    array_merge_recursive(require($rootDir . '/config/console.php'), require($rootDir . '/config/tests.php'))
);
