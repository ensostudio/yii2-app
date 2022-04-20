<?php

namespace app\assets;

use yii\bootstrap5\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

/**
 * The assets bundle of Web application.
 */
class AppAsset extends AssetBundle
{
    /**
     * @inheritDoc
     */
    public $sourcePath = '@app/assets';
    /**
     * @inheritDoc
     */
    public $basePath = '@webroot';
    /**
     * @inheritDoc
     */
    public $baseUrl = '@web';
    /**
     * @inheritDoc
     */
    public $css = [
        'scss/app.scss',
    ];
    /**
     * @inheritDoc
     */
    public $js = [
        'js/app.js',
    ];
    /**
     * @inheritDoc
     */
    public $depends = [
        YiiAsset::class,
        BootstrapAsset::class,
    ];
}
