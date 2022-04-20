<?php

/**
 * @var yii\web\View $this
 * @var app\modules\gii\generators\module\ModuleGenerator $generator
 */
use yii\helpers\StringHelper;

echo "<?php\n";
?>

namespace <?= $generator->moduleNamespace ?>;

/**
 * Application module "<?= $generator->moduleID ?>".
 *
 * @inheritDoc
 */
class <?= StringHelper::basename($generator->moduleClass) ?> extends \<?= $generator->baseClass . "\n" ?>
{
    /**
     * @inheritDoc
     */
    public $id = '<?= $generator->moduleID ?>';
    /**
     * @inheritDoc
     */
    public $controllerNamespace = __NAMESPACE__;
    /**
     * @inheritDoc
     */
    public $params = [];
}
