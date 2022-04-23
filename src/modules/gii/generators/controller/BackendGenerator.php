<?php

namespace app\modules\gii\generators\controller;

/**
 * @inheritDoc
 */
class BackendGenerator extends Generator
{
    /**
     * @inheritDoc
     */
    public $baseClass = \app\web\BackendController::class;

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
        return 'Back-end controller generator';
    }
}
