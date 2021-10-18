<?php

defined('YII_DEBUG') or define('YII_DEBUG', getenv('YII_DEBUG') ?: false);
defined('YII_ENV') or define('YII_ENV', getenv('YII_ENV') ?: 'prod');

$baseDir = dirname(__DIR__) . DIRECTORY_SEPARATOR;
/** @var Composer\Autoload\ClassLoader $composerAutoload */
$composerAutoload = require $baseDir . 'vendor/autoload.php';
require $baseDir . 'vendor/yiisoft/yii2/Yii.php';
$app = new yii\web\Application(require $baseDir . 'config/web.php');
$app->run();
