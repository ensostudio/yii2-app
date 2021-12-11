<?php

namespace app\console\controllers;

use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Controller of application modules:
 * - Generates configuration file
 * - Creates directory with symbolic links to nested modules
 *
 * @link https://www.yiiframework.com/doc/guide/2.0/en/structure-modules
 */
class ModuleController extends Controller
{
    /**
     * @var string the path to file containing configuration of application modules
     */
    public $config = '@app/config/modules.php';
    /**
     * @var string the directory of modules or links to modules in `vendor` directory
     */
    public $dir = 'modules';


    /**
     * @inheritDoc
     */
    public function options($actionID): array
    {
        $options = parent::options($actionID);
        $options[] = 'configFile';

        return $options;
    }

    public function actionIndex(): int
    {
        return ExitCode::OK;
    }
}
