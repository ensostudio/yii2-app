<?php

namespace app\widgets;

use app\helpers\Html;
use yii\bootstrap5\Widget;
use yii\bootstrap5\Alert;
use Yii;

/**
 * Alert widget renders a message from session flash.
 *
 * All flash messages are displayed in the sequence they were assigned using `Session::setFlash()`.
 * You can set message as following:
 * ```php
 * Yii::$app->session->setFlash('error', 'This is the error message');
 * Yii::$app->session->setFlash('success', 'This is the success message');
 * ```
 * Multiple messages could be set as follows:
 * ```php
 * Yii::$app->session->setFlash('info', ['Error 1', 'Error 2']);
 * ```
 */
class FlashAlertWidget extends Widget
{
    /**
     * @var array the alert types configuration for the flash messages:
     * - key: the name of the session flash variable
     * - value: the Bootstrap alert type i.e. 'danger', 'success', 'info', 'warning'
     */
    public array $alertTypes = [
        'error'   => 'alert-danger',
        'danger'  => 'alert-danger',
        'success' => 'alert-success',
        'info'    => 'alert-info',
        'warning' => 'alert-warning'
    ];
    /**
     * @var array the options for rendering the close button tag
     */
    public array $closeButton = [];

    /**
     * @inheritDoc
     * @throws \Exception The rendering error
     */
    public function run(): string
    {
        if (!Yii::$app->session->hasSessionId) {
            return '';
        }

        $result = '';
        foreach (Yii::$app->session->getAllFlashes() as $type => $messages) {
            if (!isset($this->alertTypes[$type])) {
                continue;
            }
            $idPrefix = $this->getId() . '-' . $type . '-';
            foreach ((array) $messages as $i => $message) {
                $options = $this->options;
                Html::addCssClass($options, $this->alertTypes[$type]);
                $options['id'] = $idPrefix . $i;
                $result .= Alert::widget([
                    'body' => $message,
                    'closeButton' => $this->closeButton,
                    'options' => $options,
                ]);
            }

            Yii::$app->session->removeFlash($type);
        }

        return $result;
    }
}
