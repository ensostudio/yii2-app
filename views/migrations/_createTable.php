<?php

/**
 * Creates a call for the method `yii\db\Migration::createTable()`.
 *
 * @var \yii\web\View $this
 * @var string $table the name table
 * @var array $fields the fields
 * @var array $foreignKeys the foreign keys
 */
?>
        $this->createTable(
            '<?= $table ?>',
            [
<?php foreach ($fields as $field): ?>
                '<?= $field['property'] ?>'<?= $field['decorators'] ? ' => $this->' . $field['decorators'] : '' ?>,
<?php endforeach; ?>
            ]
        );
<?= $this->render('_addForeignKeys', compact('table', 'foreignKeys')) ?>
