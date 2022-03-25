<?php

namespace app\modules\gii\generators\model;

use app\models\ActiveRecord;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\behaviors\AttributeTypecastBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\BaseActiveRecord;
use yii\db\ColumnSchema;
use yii\db\Connection;
use yii\db\Exception;
use yii\db\Schema;
use yii\db\TableSchema;
use yii\gii\CodeFile;
use yii\helpers\Inflector;
use Yii;

use yii\helpers\StringHelper;

use function array_flip;
use function array_keys;
use function array_merge;
use function array_unique;
use function class_exists;
use function count;
use function get_class;
use function is_subclass_of;
use function key;
use function lcfirst;
use function str_replace;

use function strncasecmp;

use function strpos;

use const PHP_INT_MAX;
use const PHP_INT_MIN;
use const SORT_REGULAR;

/**
 * Extended model generator will generate `ActiveRecord` class(es) for the specified database table(s).
 *
 * @property-read \yii\db\Connection $dbConnection
 * @property-read string $name
 */
class Generator extends \yii\gii\generators\model\Generator
{
    /**
     * @inheritdoc
     */
    public $baseClass = ActiveRecord::class;
    /**
     * @inheritdoc
     */
    public $generateLabelsFromComments = true;
    /**
     * @inheritdoc
     */
    public $useTablePrefix = true;
    /**
     * @inheritdoc
     */
    public $standardizeCapitals = true;
    /**
     * @inheritdoc
     */
    public $singularize = true;
    /**
     * @inheritdoc
     */
    public $useSchemaName = true;
    /**
     * @inheritdoc
     */
    public $generateQuery = true;
    /**
     * @inheritdoc
     */
    public $queryNs = 'app\models\queries';

    /**
     * @var string[] the generic class names
     */
    protected $classNames = [];

    /**
     * @var array[] the model behaviors
     */
    public $modelBehaviors = [
        BlameableBehavior::class => [
            'createdByAttribute',
            'updatedByAttribute',
        ],
        TimestampBehavior::class => [
            'createdByAttribute',
            'updatedByAttribute'
        ],
        SluggableBehavior::class => [
            'slugAttribute',
            'attribute',
            'immutable',
            'ensureUnique',
            'skipOnEmpty',
        ],
        AttributeTypecastBehavior::class => [
            'typecastBeforeSave',
            'typecastAfterFind',
            'typecastAfterValidate',
        ],
    ];

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'Application Model Generator';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        $filter = static function (string $value): string {
            return ltrim($value, ' \\');
        };
        return array_merge(
            parent::rules(),
            [
                [['modelBehaviors'], 'safe'],
                [['baseClass', 'queryBaseClass'], 'filter', 'filter' =>  $filter]
            ]
        );
    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function generate(): array
    {
        $files = [];
        $relations = $this->generateRelations();
        $db = $this->getDbConnection();
        foreach ($this->getTableNames() as $tableName) {
            // model:
            $modelClassName = $this->generateClassName($tableName);
            $queryClassName = $this->generateQuery ? $this->generateQueryClassName($modelClassName) : false;
            $tableRelations = $relations[$tableName] ?? [];
            $tableSchema = $db->getTableSchema($tableName);
            $params = [
                'tableName' => $tableName,
                'className' => $modelClassName,
                'queryClassName' => $queryClassName,
                'tableSchema' => $tableSchema,
                'properties' => $this->generateProperties($tableSchema),
                'behaviors' => $this->generateBehaviors($tableSchema),
                'labels' => $this->generateLabels($tableSchema),
                'rules' => $this->generateRules($tableSchema),
                'relations' => $tableRelations,
                'relationsClassHints' => $this->generateRelationsClassHints($tableRelations, $this->generateQuery),
            ];
            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $modelClassName . '.php',
                $this->render('model.php', $params)
            );

            // query:
            if ($queryClassName) {
                $params['className'] = $queryClassName;
                $params['modelClassName'] = $modelClassName;
                $files[] = new CodeFile(
                    Yii::getAlias('@' . str_replace('\\', '/', $this->queryNs)) . '/' . $queryClassName . '.php',
                    $this->render('query.php', $params)
                );
            }
        }

        return $files;
    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function generateTableName($tableName): string
    {
        if ($this->useTablePrefix) {
            $db = $this->getDbConnection();
            $tableName = preg_replace('/(^' . $db->tablePrefix . '|' . $db->tablePrefix . '$)/', '%', $tableName);
        }
        return '{{' . $tableName . '}}';
    }

    /**
     * @inheritDoc
     */
    protected function generateClassName($tableName, $useSchemaName = null): string
    {
        return parent::generateClassName($tableName, $useSchemaName) . 'Model';
    }

    /**
     * @inheritDoc
     */
    public function autoCompleteData(): array
    {
        $data = parent::autoCompleteData();
        $data['tableColumns'] = function (): array {
            return $this->getDbConnection()->getTableSchema($this->tableName)->getColumnNames();
        };
        return $data;
    }

    /**
     * @inheritdoc
     */
    protected function generateProperties($table): array
    {
        $properties = [];
        $driverName = $this->getDbDriverName();
        foreach ($table->columns as $column) {
            switch ($column->type) {
                case Schema::TYPE_TINYINT:
                    if ($driverName === 'mysql' && $column->size === 1) {
                        $type = 'bool';
                        break;
                    }
                case Schema::TYPE_TIMESTAMP:
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                case 'middleint':
                    $type = 'int';
                    break;
                case Schema::TYPE_BOOLEAN:
                    $type = 'bool';
                    break;
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $type = 'float';
                    break;
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_JSON:
                    $type = 'string';
                    break;
                default:
                    $type = $column->phpType;
            }
            if ($column->allowNull) {
                $phpType = '?' . $type;
                $type .= '|null';
            } else {
                $phpType = $type;
            }
            $properties[$column->name] = [
                'type' => $type,
                'phpType' => $phpType,
                'name' => Inflector::variablize($column->name),
                'comment' => $column->comment,
            ];
        }

        return $properties;
    }

    /**
     * Returns the model behaviors.
     *
     * @param TableSchema $table the table schema
     * @return array
     * @todo rewrite
     */
    public function generateBehaviors(TableSchema $table): array
    {
        $behaviors = [];

        if (isset($table->columns['user_id']) || isset($table->columns['author_id'])) {
            $behaviors['blameable'] = [
                'class' => BlameableBehavior::class . '::class',
                'createdByAttribute' => isset($table->columns['user_id']) ? 'user_id' : 'author_id',
                'updatedByAttribute' => false
            ];
        }
        if (isset($table->columns['created_at']) || isset($table->columns['updated_at'])) {
            $behaviors['timestamp'] = [
                'class' => TimestampBehavior::class . '::class',
                'createdAtAttribute' => isset($table->columns['created_at']) ? 'created_at' : false,
                'updatedAtAttribute' => isset($table->columns['updated_at']) ? 'updated_at' : false,
            ];
        }
        if (isset($table->columns['slug']) && (isset($table->columns['name']) || $table->columns['title'])) {
            $behaviors['sluggable'] = [
                'class' => SluggableBehavior::class . '::class',
                'attribute' => isset($table->columns['name']) ? 'name' : 'title',
                'ensureUnique' => true,
                'skipOnEmpty' => true,
            ];
        }

        return $behaviors;
    }

    /**
     * Returns limits of numeric column.
     *
     * @param ColumnSchema $column The table column
     * @return array [min: int, max: int]
     */
    protected function getIntegerLimits(ColumnSchema $column): array
    {
        if ($column->unsigned) {
            $limits = [
                Schema::TYPE_TINYINT => ['min' => 0, 'max' => 255],
                Schema::TYPE_SMALLINT => ['min' => 0, 'max' => 65535],
                Schema::TYPE_INTEGER => ['min' => 0, 'max' => 4294967295],
            ];
        } else {
            $limits = [
                Schema::TYPE_TINYINT => ['min' => -128, 'max' => 127],
                Schema::TYPE_SMALLINT => ['min' => -32768, 'max' => 32767],
                Schema::TYPE_INTEGER => ['min' => -2147483648, 'max' => 2147483647],
            ];
        }
        return $limits[$column->type] ?? ['min' => $column->unsigned ? 0 : PHP_INT_MIN, 'max' => PHP_INT_MAX];
    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function generateRules($table): array
    {
        $types = [];
        $lengths = [];
        $defaults = [];
        $rules = [];
        $driverName = $this->getDbDriverName();
        foreach ($table->columns as $column) {
            if ($column->autoIncrement) {
                continue;
            }
            if (!$column->allowNull && $column->defaultValue === null) {
                $types['required'][] = $column->name;
            } elseif ($column->defaultValue !== null) {
                if (strncasecmp($column->defaultValue, 'current_timestamp', 17) !== 0) {
                    $defaults[$column->defaultValue][] = $column->name;
                }
            } elseif ($column->allowNull) {
                $defaults['NULL'][] = $column->name;
            }
            switch ($column->type) {
                 case Schema::TYPE_TINYINT:
                    if ($column->phpType === Schema::TYPE_TINYINT && $column->size === 1) {
                        $types['boolean'][] = $column->name;
                        break;
                    }
                 case Schema::TYPE_SMALLINT:
                 case Schema::TYPE_INTEGER:
                 case Schema::TYPE_BIGINT:
                 case 'middleint':
                    ['min' => $min, 'max' => $max] = $this->getIntegerLimits($column);
                    $rules[] = [$column->name, 'integer', 'min' => $min, 'max' => $max];
                    if ($driverName === 'pgsql') {
                        $defaults['NULL'][] = $column->name;
                    }
                    break;
                case Schema::TYPE_BOOLEAN:
                    $types['boolean'][] = $column->name;
                    break;
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $types['number'][] = $column->name;
                    break;
                case Schema::TYPE_TIMESTAMP:
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_JSON:
                    if ($column->type === Schema::TYPE_TIMESTAMP) {
                        $types['number'][] = $column->name;
                    } else {
                        $types['string'][] = $column->name;
                    }
                    $types['safe'][] = $column->name;
                    break;
                default: // strings
                    if ($column->size > 0) {
                        $lengths[$column->size][] = $column->name;
                    } else {
                        $types['string'][] = $column->name;
                    }
            }
        }
        foreach ($defaults as $default => $columns) {
            if ($default === 'NULL') {
                $default = null;
            }
            $rules[] = [$columns, 'default', 'value' => $default];
        }
        foreach ($types as $type => $columns) {
            $rules[] = [$columns, $type];
        }
        foreach ($lengths as $length => $columns) {
            $rules[] = [$columns, 'string', 'max' => $length];
        }

        $db = $this->getDbConnection();

        return array_merge(
            $rules,
            $this->generateUniqueRules($table, $db),
            $this->generateRetationRules($table, $db),
        );
    }

    /**
     * @param TableSchema $table the table schema
     * @param Connection $db the database connection
     * @return array[]
     */
    protected function generateUniqueRules(TableSchema $table, Connection $db): array
    {
        $rules = [];
        // Unique indexes rules
        try {
            $uniqueIndexes = array_merge($db->getSchema()->findUniqueIndexes($table), [$table->primaryKey]);
            $uniqueIndexes = array_unique($uniqueIndexes, SORT_REGULAR);
            foreach ($uniqueIndexes as $uniqueColumns) {
                // Avoid validating auto incremental columns
                if (!$this->isColumnAutoIncremental($table, $uniqueColumns)) {
                    $attributesCount = count($uniqueColumns);
                    if ($attributesCount === 1) {
                        $rules[] = [$uniqueColumns[0], 'unique'];
                    } elseif ($attributesCount > 1) {
                        $rules[] = [$uniqueColumns, 'unique', 'targetAttribute' => $uniqueColumns];
                    }
                }
            }
        } catch (NotSupportedException $e) {
            // doesn't support unique indexes information...do nothing
        }
        return $rules;
    }

    /**
     * @param TableSchema $table the table schema
     * @param Connection $db the database connection
     * @return array[]
     */
    protected function generateRetationRules(TableSchema $table, Connection $db): array
    {
        $rules = [];
        // Exist rules for foreign keys
        foreach ($table->foreignKeys as $refs) {
            $refTable = $refs[0];
            unset($refs[0]);
            $refTableSchema = $db->getTableSchema($refTable);
            if ($refTableSchema === null) {
                // Foreign key could point to non-existing table: https://github.com/yiisoft/yii2-gii/issues/34
                continue;
            }
            $rules[] = [
                array_keys($refs),
                'exist',
                'skipOnError' => true,
                'targetClass' => $this->generateClassName($refTable) . '::class',
                'targetAttribute' => $refs,
            ];
        }
        return $rules;
    }

    /**
     * @param array $relations
     * @param bool $generateQuery
     * @return array
     */
    public function generateRelationsClassHints($relations, $generateQuery): array
    {
        $result = [];
        foreach ($relations as $name => $relation) {
            // The queryNs options available if [[generateQuery]] is active
            if ($generateQuery) {
                $queryClassRealName = '\\' . $this->queryNs . '\\' . $relation[1];
                if (
                    class_exists($queryClassRealName, true)
                    && is_subclass_of($queryClassRealName, BaseActiveRecord::class)
                ) {
                    $activeQueryClass = get_class($queryClassRealName::find());
                    if (strpos($activeQueryClass, $this->ns) === 0){
                        $activeQueryClass = StringHelper::basename($activeQueryClass);
                    }
                    $result[$name] = $activeQueryClass;
                } else {
                   $result[$name] = $this->ns === $this->queryNs
                       ? $relation[1]
                       : '\\' . $this->queryNs . '\\' . $relation[1];
                   $result[$name] .= 'Query';
                }
            } else {
                $result[$name] = ActiveQuery::class;
            }
        }

        return $result;
    }

    /**
     * Generates relations using a junction table by adding an extra `via()` or `viaTable()` depending on
     * `$generateViaRelationMode`.
     *
     * @param TableSchema $table the table being checked
     * @param array $fks obtained from the checkJunctionTable() method
     * @param array $relations table relations
     * @return array modified `$relations`
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function generateManyManyRelations(TableSchema $table, array $fks, array $relations): array
    {
        $db = $this->getDbConnection();

        foreach ($fks as $pair) {
            [$firstKey, $secondKey] = $pair;
            $table0 = $firstKey[0][0];
            $table1 = $secondKey[0][0];
            unset($firstKey[0][0], $secondKey[0][0]);
            $className0 = $this->generateClassName($table0);
            $className1 = $this->generateClassName($table1);
            $table0Schema = $db->getTableSchema($table0);
            $table1Schema = $db->getTableSchema($table1);

            // @see https://github.com/yiisoft/yii2-gii/issues/166
            if (!isset($table0Schema, $table1Schema)) {
                continue;
            }

            $link = $this->generateRelationLink(array_flip($secondKey[0]));
            $relationName = $this->generateRelationName($relations, $table0Schema, key($secondKey[0]), true);
            if ($this->generateJunctionRelationMode === self::JUNCTION_RELATION_VIA_TABLE) {
                $relations[$table0Schema->fullName][$relationName] = [
                    "return \$this->hasMany($className1::class, $link)->viaTable('"
                    . $this->generateTableName($table->name) . "', " . $this->generateRelationLink($firstKey[0]) . ');',
                    $className1,
                    true,
                ];
            } elseif ($this->generateJunctionRelationMode === self::JUNCTION_RELATION_VIA_MODEL) {
                $foreignRelationName = null;
                foreach ($relations[$table0Schema->fullName] as $key => $foreignRelationConfig) {
                    if ($foreignRelationConfig[3] == $firstKey[1]) {
                        $foreignRelationName = $key;
                        break;
                    }
                }
                if (empty($foreignRelationName)) {
                    throw new Exception('Foreign key for junction table not found.');
                }
                $relations[$table0Schema->fullName][$relationName] = [
                    "return \$this->hasMany($className1::class, $link)->via('". lcfirst($foreignRelationName) . "');",
                    $className1,
                    true,
                ];
            }

            $link = $this->generateRelationLink(array_flip($firstKey[0]));
            $relationName = $this->generateRelationName($relations, $table1Schema, key($firstKey[0]), true);
            if ($this->generateJunctionRelationMode === self::JUNCTION_RELATION_VIA_TABLE) {
                $relations[$table1Schema->fullName][$relationName] = [
                    "return \$this->hasMany($className0::class, $link)->viaTable('"
                    . $this->generateTableName($table->name) . "', " . $this->generateRelationLink($secondKey[0])
                    . ');',
                    $className0,
                    true,
                ];
            } elseif ($this->generateJunctionRelationMode === self::JUNCTION_RELATION_VIA_MODEL) {
                $foreignRelationName = null;
                foreach ($relations[$table1Schema->fullName] as $key => $foreignRelationConfig) {
                    if ($foreignRelationConfig[3] == $secondKey[1]) {
                        $foreignRelationName = $key;
                        break;
                    }
                }
                if (empty($foreignRelationName)) {
                    throw new Exception('Foreign key for junction table not found.');
                }
                $relations[$table1Schema->fullName][$relationName] = [
                    "return \$this->hasMany($className0::class, $link)->via('". lcfirst($foreignRelationName) . "');",
                    $className0,
                    true,
                ];
            } else {
                throw new InvalidConfigException(
                    'Unknown generateViaRelationMode ' . $this->generateJunctionRelationMode
                );
            }
        }

        return $relations;
    }

    /**
     * Returns the database connection as specified by [[db]].
     *
     * @return Connection|null
     * @throws \yii\base\InvalidConfigException if `$this->db` is not registered
     */
    protected function getDbConnection(): ?Connection
    {
        return Yii::$app->get($this->db, false);
    }
}
