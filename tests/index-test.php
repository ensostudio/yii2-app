<?php

// NOTE: Make sure this file is not accessible when deployed to production
if (!in_array(filter_input(INPUT_SERVER, 'REMOTE_ADDR'), ['127.0.0.1', '::1'], true)) {
    die('You are not allowed to access this file.');
}

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

$baseDir = dirname(__DIR__);
/** @var Composer\Autoload\ClassLoader $composerAutoload */
$composerAutoload = require $baseDir . '/vendor/autoload.php';
require $baseDir . '/vendor/yiisoft/yii2/Yii.php';

$app = new yii\web\Application(require __DIR__ . '/../config/test.php');
$app->run();
