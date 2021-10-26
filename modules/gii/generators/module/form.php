<?php
/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var yii\gii\generators\module\Generator $generator
 */
?>
<?= $generator->parentModuleClass; ?>
<div class="module-form">
    <?= $form->field($generator, 'parentModuleClass')->dropDownList(['foo' => 'bar', \yii\web\Application::class => 'fff', 'baz2' => 'fff2']) ?>
    <?= $form->field($generator, 'moduleClass') ?>
    <?= $form->field($generator, 'moduleID') ?>
</div>
