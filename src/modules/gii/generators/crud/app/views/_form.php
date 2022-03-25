<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this \yii\web\View */
/* @var $generator \yii\gii\generators\crud\Generator */
/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes() ?: $model->attributes();

echo "<?php\n";
?>

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var yii\web\View $this */
/* @var <?= ltrim($generator->modelClass, '\\') ?> $model */
/* @var ActiveForm $form */
?>

<div class="form" id="form-<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>">
    <?= "<?php " ?>$form = ActiveForm::begin(); ?>

<?php foreach ($generator->getColumnNames() as $attribute) {
    if (in_array($attribute, $safeAttributes, true)) {
        echo "    <?= " . $generator->generateActiveField($attribute) . " ?>\n";
    }
} ?>
    <div class="form-group">
        <?= "<?= " ?>Html::submitButton(<?= $generator->generateString('Save') ?>, ['class' => 'btn btn-primary']) ?>
    </div>

    <?= "<?php " ?>ActiveForm::end(); ?>
</div>
