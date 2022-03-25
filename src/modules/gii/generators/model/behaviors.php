<?php
/**
 * @var \yii\web\View $this
 * @var \app\gii\generators\model\Generator $generator
 */
use yii\bootstrap5\BootstrapAsset;
use yii\bootstrap5\Accordion;
use yii\helpers\StringHelper;

BootstrapAsset::register($this);

$accordionItems = [];
foreach ($generator->modelBehaviors as $class => $options) {
    $accordionItems[$class] = [
        'label' => StringHelper::basename($class),
        'content' => ''
    ];
}
?>
<fieldset id="behaviors" class="mb-1 p-0">
    <legend>Behaviors</legend>
    <?= Accordion::widget(['items' => $accordionItems]); ?>
</fieldset>
