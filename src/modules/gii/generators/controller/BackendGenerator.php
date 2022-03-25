<?php

namespace app\gii\generators\controller;

/**
 * @inheritDoc
 */
class BackendGenerator extends \yii\gii\generators\controller\Generator
{
    /**
     * @inheritDoc
     */
    public $baseClass = app\controllers\BackendController::class;

    /**
     * @inheritDoc
     */
    public $actions = 'index';

    /**
     * @inheritDoc
     */
    // public $controllerClass = 'app\\controllers\\backend\\';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Back-end Controller Generator';
    }
}
