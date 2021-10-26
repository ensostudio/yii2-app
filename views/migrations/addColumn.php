<?php

/**
 * This view is used by `yii\console\controllers\MigrateController`.
 * The following variables are available in this view:
 *
 * @var string $className the new migration class name without namespace
 * @var string $namespace the new migration class namespace
 * @var string $table the name table
 * @var array $fields the fields
 * @var array $foreignKeys the foreign keys
 * @var \yii\web\View $this
 */

echo "<?php\n";
if (!empty($namespace)) {
    echo "\nnamespace $namespace;\n";
}
?>

use yii\db\Migration;

/**
 * Добавляет колонки <?= implode(', ', $fields) ?> в таблице `<?= $table ?>`.
<?= $this->render('@yii/views/_foreignTables', ['foreignKeys' => $foreignKeys]) ?>
 */
class <?= $className ?> extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
<?= $this->render('@yii/views/_addColumns', compact('table', 'fields', 'foreignKeys')) ?>
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
<?= $this->render('@yii/views/_dropColumns', compact('table', 'fields', 'foreignKeys')) ?>
        return true;
    }
}
