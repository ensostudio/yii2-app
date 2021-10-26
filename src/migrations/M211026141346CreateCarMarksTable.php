<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Создает таблицу `{{%car_marks}}`.
 */
class M211026141346CreateCarMarksTable extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        $this->createTable(
            '{{%car_marks}}',
            [
                'id' => $this->primaryKey(),
            ]
        );
        $this->addCommentOnTable('{{%car_marks}}', 'Car marks');
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        $this->dropTable('{{%car_marks}}');
        return true;
    }
}
