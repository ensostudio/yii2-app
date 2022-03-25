<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this \yii\web\View */
/* @var $generator \yii\gii\generators\crud\Generator */

$id = 'search-' . Inflector::camel2id(StringHelper::basename($generator->modelClass));

echo "<?php\n";
?>

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var \yii\web\View $this */
/* @var \<?= ltrim($generator->searchModelClass, '\\') ?>  $model */
/* @var \yii\bootstrap4\ActiveForm $form */
?>

<div class="search" id="<?= $id ?>">
    <a class="btn btn-secondary btn-sm" 
       href="#<?= $id ?>-collapse"
       role="button"
       data-toggle="collapse"
       aria-expanded="false"
       aria-controls="<?= $id ?>-collapse"
    >Toggle search</a>

    <?= "<?php " ?>$form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'class' => 'collapse',
            'id' => '<?= $id ?>-collapse',
<?php if ($generator->enablePjax): ?>
            'data-pjax' => 1,
<?php endif; ?>
        ],
    ]); ?>

<?php
$count = 0;
foreach ($generator->getColumnNames() as $attribute) {
    if (++$count < 6) {
        echo "    <?= " . $generator->generateActiveSearchField($attribute) . " ?>\n\n";
    } else {
        echo "    <?php // echo " . $generator->generateActiveSearchField($attribute) . " ?>\n\n";
    }
}
?>
    <div class="form-group">
        <?= "<?= " ?>Html::submitButton(<?= $generator->generateString('Search') ?>, ['class' => 'btn btn-primary']) ?>
        <?= "<?= " ?>Html::resetButton(<?= $generator->generateString('Reset') ?>, ['class' => 'btn btn-secondary']) ?>
    </div>

    <?= "<?php " ?>ActiveForm::end(); ?>
</div>
