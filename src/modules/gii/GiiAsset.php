<?php

namespace app\modules\gii;

use yii\web\AssetBundle;

/**
 * This declares the asset files required by Gii.
 */
class GiiAsset extends AssetBundle
{
    /**
     * @inheritDoc
     */
    public $sourcePath = __DIR__ . DIRECTORY_SEPARATOR . 'assets';
    /**
     * @inheritDoc
     */
    public $css = ['form.css'];
    /**
     * @inheritDoc
     */
    public $js = ['form.js'];
    /**
     * @inheritDoc
     */
    public $depends = [
        \yii\gii\GiiAsset::class
    ];
}
