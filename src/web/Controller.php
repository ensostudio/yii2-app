<?php

namespace app\web;

use yii\helpers\Url;
use yii\web\Application;
use yii\web\Response;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use yii\web\User;
use Yii;

/**
 * Base web controller.
 *
 * @inheritdoc
 * @property-read Application $app The current application
 * @property-read User $user The current user
 */
abstract class Controller extends \yii\web\Controller
{
    /**
     * @inheritDoc
     */
    public $layout = 'frontend';
    /**
     * @var string The human-readable name of controller
     */
    public string $name;

    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'text/html' => Response::FORMAT_HTML,
                    'application/html' => Response::FORMAT_HTML,
                    'application/json' => Response::FORMAT_JSON,
                    'application/xml' => Response::FORMAT_XML,
                ],
                'languages' => Yii::$app->params['languages'] ?? null,
            ],
            // AJAX actions (`actionAjax*`)
            'ajaxFilter' => [
                'class' => AjaxFilter::class,
                'only' => ['ajax*']
            ],
        ];
    }

    /**
     * Returns the current application.
     *
     * @return Application
     */
    public function getApp(): Application
    {
        return Yii::$app;
    }

    /**
     * Returns the current user.
     *
     * @return User
     */
    public function getUser(): User
    {
        return Yii::$app->getUser();
    }

    /**
     * @inheritdoc
     */
    public function render($view, $params = []): string
    {
        $this->getView()->params += [
            'application' => $this->getApp(),
            'user' => $this->getUser(),
            'module' => $this->module,
            'breadcrumbs' => [
                Url::home() => Yii::t('app', 'Home'),
            ],
        ];
        $this->getView()->title = $this->name;

        return parent::render($view, $params);
    }
}
