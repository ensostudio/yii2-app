<?php
/**
 * This is the template for generating a module class file.
 *
 * @var \yii\web\View $this
 * @var \app\gii\generators\module\Generator $generator
 */

$className = $generator->moduleClass;
$pos = strrpos($className, '\\');
$ns = ltrim(substr($className, 0, $pos), '\\');
$className = substr($className, $pos + 1);

echo "<?php\n";
?>

namespace <?= $ns ?>;

/**
 * Application module "<?= $generator->moduleID ?>".
 */
class <?= $className ?> extends \app\Module
{
    /**
     * @inheritDoc
     */
    public $controllerNamespace = '<?= $generator->getControllerNamespace() ?>';

    /**
     * @inheritDoc
     */
    public function init(): void
    {
        parent::init();
        // custom initialization code goes here
    }
}
