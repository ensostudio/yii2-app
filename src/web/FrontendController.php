<?php

namespace app\web;

use app\base\Module;
use yii\helpers\Url;
use yii\web\Application;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use yii\web\User;
use Yii;

use function str_replace;
use function ucfirst;

/**
 * Front-end Web controller.
 *
 * @inheritDoc
 * @property-read Application $app The current application
 * @property-read User $user The current user
 * @property-read array[] $breadcrumbs The breadcrumb links for widget
 */
abstract class FrontendController extends Controller
{
    /**
     * @inheritDoc
     */
    public $layout = 'frontend';

    /**
     * @var string The human-readable name of controller
     */
    protected string $name;

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
                    'application/js' => Response::FORMAT_JSON, // JSONP
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
     * Returns the human-readable name of controller.
     */
    public function getName(): string
    {
        if (!isset($this->name)) {
            $this->name = ucfirst(str_replace(['-', '_'], ' ', $this->id));
            if ($this->module instanceof Module) {
                $this->name = $this->module->translate($this->name);
            } else {
                $this->name = Yii::t($this->module->getUniqueId() ?: $this->module->id, $this->name);
            }
        }

        return $this->name;
    }

    /**
     * Returns the human-readable name of controller's action.
     *
     * @param string|null $actionId the action identifier
     */
    protected function getActionName(string $actionId = null): string
    {
        $actionName = ucfirst(str_replace(['-', '_'], ' ', $actionId !== null ? $actionId : $this->action->id));
        if ($this->module instanceof Module) {
            $actionName = $this->module->translate($actionName);
        } else {
            $actionName = Yii::t($this->module->getUniqueId() ?: $this->module->id, $actionName);
        }

        return $actionName;
    }

    /**
     * Returns the current user.
     *
     * @return array
     */
    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [];
        // @todo add modules breadcrumbs
        if ($this->action !== null && $this->action->id !== $this->defaultAction) {
            $breadcrumbs[] = ['url' => Url::current(['action' => $this->defaultAction]), 'label' => $this->getName()];
            $breadcrumbs[] = ['label' => $this->getActionName()];
        } else {
            $breadcrumbs[] = ['label' => $this->getName()];
        }

        return $breadcrumbs;
    }

    /**
     * @inheritdoc
     */
    public function render($view, $params = []): string
    {
        $breadcrumbs = $this->getBreadcrumbs();
        $this->getView()->params += [
            'application' => $this->getApp(),
            'userManager' => $this->getUser(),
            'module' => $this->module,
            'breadcrumbs' => $breadcrumbs,
        ];
        $this->getView()->title = implode(' - ', array_column($breadcrumbs, 'label'));

        return parent::render($view, $params);
    }
}
