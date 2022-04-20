<?php

/**
 * @var yii\web\View $this
 * @var yii\bootstrap5\ActiveForm $form
 * @var app\modules\gii\generators\module\ModuleGenerator $generator
 */
use app\modules\gii\GiiAsset;
use yii\helpers\Url;

GiiAsset::register($this);
?>
<div class="module-form">
    <?= $form->field($generator, 'baseClass')->textInput() ?>
    <?php $field = $form->field($generator, 'moduleClass')->textInput() ?>
    <?= $form->field($generator, 'moduleID')->textInput([
        'data-ajax-action' => Url::to(['default/action', 'id' => 'module', 'name' => 'generateClassName']),
        'data-ajax-target' => '#' . $field->inputId,
    ]) ?>
    <?= $field ?>
    <?= $form->field($generator, 'moduleNamespace')->textInput() ?>
    <?= $form->field($generator, 'composerPackage')->textInput() ?>
    <?= $form->field($generator, 'enableI18N')->checkbox([
        'data-bs-toggle' => 'collapse',
        'data-bs-target' => '#field-group-i18n',
        'aria-expanded' => var_export($generator->enableI18N, true)
    ]) ?>
    <div id="field-group-i18n" class="collapse<?= $generator->enableI18N ? ' show' : '' ?>">
        <?= $form->field($generator, 'messageCategory')->textInput() ?>
        <?= $form->field($generator, 'messageSource')->dropDownList($generator->getMessageSources()) ?>
    </div>
</div>

