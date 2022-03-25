<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this \yii\web\View */
/* @var $generator \yii\gii\generators\crud\Generator */

echo "<?php\n";
?>

use yii\bootstrap4\Html;

/* @var \yii\web\View $this */
/* @var \<?= ltrim($generator->modelClass, '\\') ?> $model */

$this->title = <?= $generator->generateString('Create ' . Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="create create-<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>">
    <h1 class="h2"><?= "<?= " ?>Html::encode($this->title) ?></h1>

    <?= "<?= " ?>$this->render('_form', ['model' => $model]) ?>
</div>
