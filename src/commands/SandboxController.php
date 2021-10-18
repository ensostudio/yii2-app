<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;

/**
 * "sandbox" controller.
 */
class SandboxController extends Controller
{
    /**
     * @inheritdoc
     */
    public $color = true;

    /**
     * This command echoes what you have entered as the message.
     *
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionIndex(string $message = 'default value'): int
    {
        echo $message . "\n";

        return ExitCode::OK;
    }
}
