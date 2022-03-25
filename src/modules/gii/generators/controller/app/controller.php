<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * This is the template for generating a controller class file.
 * @var yii\web\View $this
 * @var yii\gii\generators\controller\Generator $generator
 */
$controllerID = $generator->getControllerID();
$appEnv = StringHelper::basename(get_class($generator)) === 'BackendGenerator' ? 'Back-end' : 'Front-end';
$controllerClass = StringHelper::basename($generator->controllerClass);
echo "<?php\n";
?>

namespace <?= $generator->getControllerNamespace() ?>;

/**
 * <?= $appEnv ?> controller <?= Inflector::humanize(lcfirst($controllerID)) ?>.
 */
class <?= $controllerClass ?> extends <?= '\\' . ltrim($generator->baseClass, '\\') . "\n" ?>
{
<?php foreach ($generator->getActionIDs() as $action): ?>
    /**
     * Action "<?= Inflector::humanize(lcfirst($action)) ?>".
     *
     * @return string
     */
    public function action<?= Inflector::id2camel($action) ?>(): string
    {
        return $this->render('<?= $action ?>');
    }

<?php endforeach; ?>
}
