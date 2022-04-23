<?php

namespace app\web\widgets;

use app\helpers\Html;
use Exception;
use yii\bootstrap5\Alert;
use yii\bootstrap5\Widget;
use Yii;

/**
 * Alert widget renders session's flash message.
 *
 * All flash messages are displayed in the sequence they were assigned using `Session::setFlash()`:
 * ```php
 * Yii::$app->session->setFlash('error', 'This is the error message');
 * Yii::$app->session->setFlash('success', 'This is the success message');
 * ```
 *
 * @see \yii\web\Session::setFlash()
 */
class FlashAlert extends Widget
{
    /**
     * @var array the alert types configuration for the flash messages:
     * - key: the name of the session flash variable
     * - value: the Bootstrap alert type i.e. 'danger', 'success', 'info', 'warning'
     */
    public array $alertTypes = [
        'error'   => 'alert-danger',
        'danger'  => 'alert-danger',
        'ok'      => 'alert-success',
        'success' => 'alert-success',
        'info'    => 'alert-info',
        'warning' => 'alert-warning'
    ];
    /**
     * @var array the options for rendering the close button tag
     */
    public array $closeButtonOptions = [];

    /**
     * @inheritDoc
     * @throws Exception The rendering error
     */
    public function run(): string
    {
        $session = Yii::$app->getSession();
        if (!$session->hasSessionId) {
            return '';
        }

        $result = '';
        foreach ($session->getAllFlashes() as $type => $messages) {
            if (!isset($this->alertTypes[$type])) {
                continue;
            }
            $session->removeFlash($type);
            foreach ((array) $messages as $message) {
                $options = $this->options;
                Html::addCssClass($options, $this->alertTypes[$type]);
                $result .= Alert::widget([
                    'body' => $message,
                    'closeButton' => $this->closeButtonOptions,
                    'options' => $options,
                ]);
            }
        }

        return $result;
    }
}
