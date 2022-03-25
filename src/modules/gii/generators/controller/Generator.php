<?php

namespace app\gii\generators\controller;

/**
 * @inheritDoc
 */
class Generator extends \yii\gii\generators\controller\Generator
{
    /**
     * @inheritDoc
     */
    public $baseClass = app\controllers\FrontendController::class;

    /**
     * @inheritDoc
     */
    public $actions = 'index sitemap';

    /**
     * @inheritDoc
     */
    // public $controllerClass = 'app\\controllers\\';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Front-end Controller Generator';
    }
}
