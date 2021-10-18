<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 */
class AppAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@app/assets';
    /**
     * @inheritdoc
     */
    public $basePath = '@webroot';
    /**
     * @inheritdoc
     */
    public $baseUrl = '@web';
    /**
     * @inheritdoc
     */
    public $css = [
        'css/site.css',
    ];
    /**
     * @inheritdoc
     */
    public $js = [
        'js/site.js',
    ];
    /**
     * @inheritdoc
     */
    public $depends = [
        \yii\web\YiiAsset::class,
        \yii\bootstrap5\BootstrapAsset::class,
    ];
}
