<?php
/**
 * This is the template for generating the `ActiveQuery` class.
 *
 * @var \yii\web\View $this view instance
 * @var \app\gii\generators\model\Generator $generator generator instance
 * @var string $tableName full table name
 * @var string $className class name
 * @var \yii\db\TableSchema $tableSchema database table
 * @var string $labels[] list of attribute labels (name => label)
 * @var string $rules[] list of validation rules
 * @var array $relations list of relations (name => relation declaration)
 * @var string $className class name
 * @var string $modelClassName related model class name
 */

$modelFullClassName = $modelClassName;
if ($generator->ns !== $generator->queryNs) {
    $modelFullClassName = '\\' . $generator->ns . '\\' . $modelFullClassName;
}

echo "<?php\n";
?>

namespace <?= $generator->queryNs ?>;

/**
 * The `ActiveQuery` class for `<?= $modelFullClassName ?>`.
 *
 * @method array|<?= $modelFullClassName ?>[] all($db = null)
 * @method <?= $modelFullClassName ?>|null one($db = null)
 * @see <?= $modelFullClassName . "\n" ?>
 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->queryBaseClass, '\\') . "\n" ?>
{
<?php if (!empty($tableSchema->columns['visible'])): ?>
    /**
     * Only visibles, filter by column `visible`(attribute `visible`).
     *
     * @return $this
     */
    public function visible(): self
    {
        return $this->andWhere('[[visible]]=1');
    }
<?php endif; ?>
<?php if (!empty($tableSchema->columns['sort_order'])): ?>

    /**
     * Sorts records by column `sort_order`(attribute `sortOrder`).
     *
     * @param bool $descendingly if `TRUE`, then sort descendingly, else sort ascendingly (by default)
     * @return $this
     */
    public function sortByOrder(bool $descendingly = false): self
    {
        return $this->orderBy(['sort_order' => $descendingly ? \SORT_DESC : \SORT_ASC]);
    }
<?php endif; ?>
}
