<?php

/**
 * Web application: back-end.
 */

defined('YII_DEBUG') || define('YII_DEBUG', getenv('YII_DEBUG') ?: true);
defined('YII_ENV') || define('YII_ENV', getenv('YII_ENV') ?: 'dev');

$appDir = dirname(__DIR__, 2);

require $appDir . '/vendor/yiisoft/yii2/Yii.php';
require $appDir . '/vendor/autoload.php';

$app = new yii\web\Application(require($appDir . 'config/web.php'));
$app->run();
