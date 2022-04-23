<?php

namespace app\modules\gii\generators\controller;

/**
 * @inheritDoc
 */
class Generator extends \yii\gii\generators\controller\Generator
{
    /**
     * @inheritDoc
     */
    public $baseClass = \app\web\FrontendController::class;

    /**
     * @inheritDoc
     */
    public $actions = 'index sitemap';

    /**
     * @inheritDoc
     */
    //public $controllerClass = 'app\\controllers\\';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Front-end controller generator';
    }
}
