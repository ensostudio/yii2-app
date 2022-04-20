<?php

/**
 * Web application: front-end.
 */

defined('YII_DEBUG') || define('YII_DEBUG', getenv('YII_DEBUG') ?: true);
defined('YII_ENV') || define('YII_ENV', getenv('YII_ENV') ?: 'dev');

$rootDir = dirname(__DIR__);

require $rootDir . '/vendor/yiisoft/yii2/Yii.php';
require $rootDir . '/vendor/autoload.php';

$app = new yii\web\Application(require($rootDir . '/config/frontend.php'));
$app->run();
