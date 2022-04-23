<?php

/**
 * Back-end Web application.
 */

defined('YII_DEBUG') || define('YII_DEBUG', getenv('YII_DEBUG') ?: true);
defined('YII_ENV') || define('YII_ENV', getenv('YII_ENV') ?: 'dev');

$rootDir = dirname(__DIR__, 2);

require $rootDir . '/vendor/yiisoft/yii2/Yii.php';
require $rootDir . '/vendor/autoload.php';

$app = new yii\web\Application(require($rootDir . '/config/backend.php'));
$app->run();
