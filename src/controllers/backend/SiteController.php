<?php

namespace app\controllers\backend;

// use yii\filters\AccessControl;
// use yii\base\ErrorAction;
use yii\web\Controller;

/**
 * Default controller
 */
class SiteController extends Controller
{
    /**
     * @inheritDoc
     */
    public function init()
    {
        // @todo Change the autogenerated stub
        parent::init();
        $this->viewPath = '@app/views/backend/' . $this->id;
    }

    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        return [
            /*
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            */
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions(): array
    {
        return [
            /*
            'error' => [
                'class' => ErrorAction::class,
            ]
            */
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        return $this->render('index');
    }
}
