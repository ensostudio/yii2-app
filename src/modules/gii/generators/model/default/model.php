<?php

/**
 * This is the template for generating the model class of a specified table.
 *
 * @var \yii\web\View $this view instance
 * @var \app\gii\generators\model\Generator $generator generator instance
 * @var string $tableName full table name
 * @var string $className class name
 * @var string $queryClassName query class name
 * @var \yii\db\TableSchema $tableSchema database table
 * @var array $properties list of properties (property => [type:string,phpType:string,name:string,comment:string])
 * @var string[] $labels list of attribute labels (name => label)
 * @var string[] $rules list of validation rules
 * @var array $relations list of relations (name => relation declaration)
 * @var string[] $relationsClassHints relation hints
 * @var array $behaviors model behaviors
 */

use app\models\ActiveRecord;
use app\helpers\VarDumper;

$useAttrAliases = is_a($generator->baseClass, ActiveRecord::class, true);

echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

<?php if ($generator->db !== 'db'): ?>
use yii\db\Connection;
<?php endif; ?>
<?php if (!empty($queryClassName) || $generator->db !== 'db'): ?>
use Yii;
<?php endif; ?>

use function array_merge;

/**
 * Model for database table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($properties as $property => $data): ?>
 * @property <?= $data['type'] . ' $' . $property . ($data['comment'] ? ' ' . $data['comment'] : '') . "\n" ?>
<?php if ($useAttrAliases && $data['name'] !== $property): ?>
 * @property <?= $data['type'] . ' $' . $data['name'] . ' [[' . $property . "]] alias\n" ?>
<?php endif; ?>
<?php endforeach; ?>
<?php foreach ($relations as $name => $data): ?>
 * @property <?= $relationsClassHints[$name] . ($data[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php endforeach; ?>
 */
class <?= $className ?> extends <?= $generator->baseClass . "\n" ?>
{
<?php if ($generator->db !== 'db'): ?>
    /**
     * Returns the database connection used by this model.
     *
     * @return Connection
     */
    public static function getDb(): Connection
    {
        return Yii::$app->get('<?= $generator->db ?>');
    }

<?php endif; ?>
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }
<?php if (!empty($behaviors)): ?>

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return array_merge(
            parent::behaviors(),
            <?= VarDumper::export($behaviors) . "\n" ?>
        );
    }
<?php endif; ?>

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            <?= VarDumper::export($rules) . "\n" ?>
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return array_merge(
            parent::attributeLabels(),
            [
<?php foreach ($labels as $attribute => $label): ?>
                '<?= $attribute ?>' => <?= $generator->generateString($label) . ",\n" ?>
<?php endforeach; ?>
            ]
        );
    }
<?php foreach ($properties as $column => $data): ?>
    <?php $data['comment'] = lcfirst(trim($data['comment'])); ?>
    /**
     * Gets <?= $data['comment'] ?>.
     * Model attribute [[<?= $data['name'] ?>]], table column "<?= $column ?>".
     *
     * @return <?= $data['type'] . "\n" ?>
     */
    public function get<?= ucfirst($data['name']) ?>(): <?= $data['phpType'] . "\n" ?>
    {
        return $this->getAttribute('<?= $column ?>');
    }

<?php if (!$tableSchema->getColumn($column)->autoIncrement): ?>
    /**
     * Sets <?= $data['comment'] ?>.
     * Model attribute [[<?= $data['name'] ?>]], table column <?= $column ?>".
     *
     * @param <?= $data['type'] ?> $value the new value
     * @return $this
     */
    public function set<?= ucfirst($data['name']) ?>(<?= $data['phpType'] ?> $value): self
    {
        $this->setAttribute('<?= $column ?>', $value);

        return $this;
    }
<?php endif; ?>
<?php endforeach; ?>
<?php foreach ($relations as $name => $relation): ?>

    /**
     * Gets `ActiveQuery` instance for related model by property [[<?= lcfirst($name) ?>]].
     *
     * @return <?= $relationsClassHints[$name] . ($relation[2] ? '[]' : '') ?>
     */
    public function get<?= $name ?>(): <?= $relation[2] ? 'array' : $relation[1] ?>
    {
        <?= $relation[0] . "\n" ?>
    }
<?php endforeach; ?>
<?php if (!empty($queryClassName)): ?>
    <?php
    if ($generator->ns !== $generator->queryNs) {
        $queryClassName = '\\' . $generator->queryNs . '\\' . $queryClassName;
    }
    ?>

    /**
     * Returns `ActiveQuery` instance for this model.
     *
     * @return <?= $queryClassName . "\n" ?>
     */
    public static function find(): <?= $queryClassName . "\n" ?>
    {
        return Yii::createObject(<?= $queryClassName ?>::class, [static::class]);
    }
<?php endif; ?>
}
