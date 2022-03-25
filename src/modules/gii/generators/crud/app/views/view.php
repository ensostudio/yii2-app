<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var \yii\web\View $this
 * @var \yii\gii\generators\crud\Generator $generator
 */
$urlParams = $generator->generateUrlParams();
$modelClass = StringHelper::basename($generator->modelClass);

echo "<?php\n";
?>

use yii\bootstrap5\Html;
use yii\widgets\DetailView;

/**
 * @var \yii\web\View $this
 * @var \<?= ltrim($generator->modelClass, '\\') ?> $model
 */

$this->title = Html::encode($model-><?= $generator->getNameAttribute() ?>);
$this->params['breadcrumbs'][] = [
    'label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words($modelClass))) ?>,
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
\yii\bootstrap5\BootstrapAsset::register($this);
?>
<div class="view" id="view-<?= Inflector::camel2id($modelClass) ?>">
    <h1 class="h2"><?= '<?= ' ?>$this->title ?></h1>

    <p class="btn-toolbar-top">
        <?= '<?= ' ?>Html::a(
            <?= $generator->generateString('Update') ?>,
            ['update', <?= $urlParams ?>],
            ['class' => 'btn btn-primary']
        ) ?>
         <?= '<?= ' ?>Html::a(
            <?= $generator->generateString('Delete') ?>,
            ['delete', <?= $urlParams ?>],
            ['class' => 'btn btn-danger',
            'data' => [
                'confirm' => <?= $generator->generateString('Are you sure you want to delete this item?') ?>,
                'method' => 'post',
            ],
         ]) ?>
    </p>

    <?= '<?= ' ?>DetailView::widget([
        'model' => $model,
        'attributes' => [
        <?php
        $tableSchema = $generator->getTableSchema();
        if ($tableSchema === false) {
            foreach ($generator->getColumnNames() as $name) {
                echo "            '" . $name . "',\n";
            }
        } else {
            foreach ($generator->getTableSchema()->columns as $column) {
                $format = $generator->generateColumnFormat($column);
                echo "            '" . $column->name . ($format === 'text' ? '' : ':' . $format) . "',\n";
            }
        }
        ?>
        ],
    ]) ?>
</div>
