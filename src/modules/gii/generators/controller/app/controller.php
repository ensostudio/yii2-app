<?php
/**
 * This is the template for generating a controller class file.
 * @var yii\web\View $this
 * @var app\modules\gii\generators\controller\Generator $generator
 */
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$controllerID = $generator->getControllerID();
$isbackendGenerator = $generator instanceof app\modules\gii\generators\controller\BackendGenerator;
$controllerClass = StringHelper::basename($generator->controllerClass);
echo "<?php\n";
?>

namespace <?= $generator->getControllerNamespace() ?>;

/**
 * <?= $isbackendGenerator ? 'Back' : 'Front' ?>-end controller "<?= str_replace(['_', '-'], ' ', $controllerID) ?>".
 *
 * @inheritDoc
 */
class <?= $controllerClass ?> extends <?= '\\' . ltrim($generator->baseClass, '\\') . "\n" ?>
{
<?php foreach ($generator->getActionIDs() as $action): ?>
    /**
     * Action "<?= str_replace(['_', '-'], ' ', $action) ?>".
     *
     * @return string
     */
    public function action<?= Inflector::id2camel($action) ?>(): string
    {
        return $this->render('<?= $action ?>');
    }
<?php endforeach; ?>
}
