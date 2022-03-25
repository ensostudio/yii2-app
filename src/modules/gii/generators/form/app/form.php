<?php
/**
 * This is the template for generating an action view file.
 *
 * @var \yii\web\View $this
 * @var \yii\gii\generators\form\Generator $generator
 */

$class = str_replace('/', '-', trim($generator->viewName, '_'));

echo "<?php\n";
?>

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

/**
 * @var \yii\web\View $this
 * @var \<?= ltrim($generator->modelClass, '\\') ?> $model
 */
<?= '?>' ?>
<section class="<?= $class ?>">
    <?= '<?php ' ?>$form = ActiveForm::begin(); ?>

    <?php foreach ($generator->getModelAttributes() as $attribute): ?>
        <?= '<?= ' ?>$form->field($model, '<?= $attribute ?>') ?>
    <?php endforeach; ?>

        <div class="form-group">
            <?= '<?= ' ?>Html::submitButton(<?= $generator->generateString('Submit') ?>) ?>
        </div>
    <?= '<?php ' ?>ActiveForm::end(); ?>
</section>
