<?php

/**
 * This view is used by `console/controllers/MigrateController`.
 * The following variables are available in this view:
 *
 * @var string $className the new migration class name without namespace
 * @var string $namespace the new migration class namespace
 * @var \yii\web\View $this
 */

use yii\helpers\Inflector;

echo "<?php\n";
if (!empty($namespace)) {
    echo "\nnamespace $namespace;\n";
}
?>

use yii\db\Migration;
use yii\console\Console;

/**
 * Миграция <?= Inflector::humanize($className) . ".\n" ?>
 */
class <?= $className ?> extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        Console::error("<?= $className ?> cannot be reverted.\n");
        return false;
    }
}
