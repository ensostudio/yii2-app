<?php

namespace app\widgets;

use yii\bootstrap5;
use Yii;

/**
 * Alert widget renders a message from session flash. All flash messages are displayed
 * in the sequence they were assigned using setFlash. You can set message as following:
 *
 * ```php
 * Yii::$app->session->setFlash('error', 'This is the message');
 * Yii::$app->session->setFlash('success', 'This is the message');
 * Yii::$app->session->setFlash('info', 'This is the message');
 * ```
 *
 * Multiple messages could be set as follows:
 *
 * ```php
 * Yii::$app->session->setFlash('error', ['Error 1', 'Error 2']);
 * ```
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @author Alexander Makarov <sam@rmcreative.ru>
 * @author Enso <info@ensostudio.ru>
 */
class Alert extends bootstrap5\Widget
{
    /**
     * @var array the alert types configuration for the flash messages. This array is setup as `$key => $value`, where:
     * - key: the name of the session flash variable
     * - value: the bootstrap alert type (i.e. danger, success, info, warning)
     */
    public $alertTypes = [
        'error'   => 'alert-danger',
        'danger'  => 'alert-danger',
        'success' => 'alert-success',
        'info'    => 'alert-info',
        'warning' => 'alert-warning'
    ];
    /**
     * @var array the options for rendering the close button tag.
     */
    public $closeButton = [];

    /**
     * @inheritDoc
     * @throws \Exception Rendering error
     */
    public function run()
    {
        if (!Yii::$app->session->hasSessionId) {
            return '';
        }

        $appendClass = isset($this->options['class']) ? ' ' . $this->options['class'] : '';
        $result = '';
        foreach (Yii::$app->session->getAllFlashes() as $type => $messages) {
            if (!isset($this->alertTypes[$type])) {
                continue;
            }
            $idPrefix = $this->getId() . '-' . $type . '-';
            foreach ((array) $messages as $i => $message) {
                $result .= bootstrap5\Alert::widget([
                    'body' => $message,
                    'closeButton' => $this->closeButton,
                    'options' => array_merge(
                        $this->options,
                        ['id' => $idPrefix . $i, 'class' => $this->alertTypes[$type] . $appendClass]
                    ),
                ]) . PHP_EOL;
            }

            Yii::$app->session->removeFlash($type);
        }

        return $result;
    }
}
