<?php

namespace app\gii\generators\crud;

use app\core\helpers\FileHelper;
use Yii;
use yii\base\Model;
use yii\db\Schema;
use yii\helpers\Inflector;
use yii\helpers\VarDumper;

use function count;
use function in_array;
use function is_array;
use function strlen;

/**
 * {@inheritDoc}
 */
class Generator extends \yii\gii\generators\crud\Generator
{
    /**
     * @var \yii\base\Model Model instance
     */
    private $model;

    /**
     * Returns model instance.
     *
     * @return Model
     */
    private function getModel(): Model
    {
        if ($this->model === null) {
            $this->model = new $this->modelClass();
        }
        return $this->model;
    }

    /**
     * @inheritDoc
     */
    public function autoCompleteData(): array
    {
        $dir = Yii::getAlias('@app/models');
        $models = FileHelper::findFiles($dir, ['only' => ['*.php']]);
        $offset = strlen($dir) + 1;
        foreach ($models as &$model) {
            $model = str_replace('/', '\\', 'app/models/' . substr($model, $offset, -4));
        }
        return [
            'modelClass' => $models,
        ];
    }

    /**
     * @inheritDoc
     */
    public function generateActiveField($attribute): string
    {
        $tableSchema = $this->getTableSchema();
        if ($tableSchema === false || !isset($tableSchema->columns[$attribute])) {
            if (preg_match('/^(password|pass|passwd|passcode|psw)$/i', $attribute)) {
                return "\$form->field(\$model, '$attribute')->passwordInput()";
            }

            return "\$form->field(\$model, '$attribute')";
        }

        $column = $tableSchema->columns[$attribute];

        foreach ($this->getModel()->rules() as $rule) {
            if ($rule[1] === 'exist' && isset($rule['targetClass']) && in_array($attribute, (array) $rule[0], true)) {
                $model = new $rule['targetClass']();
                // $pk = $rule['targetAttribute'][$column->name];
                // $name = $tableSchema->columns[$this->getNameAttribute()];
                $method = "$model::getNames()";
                return "\$form->field(\$model, '$attribute')->dropDownList($method, ['prompt' => ''])";
            }
        }

        if (
            $column->phpType === 'boolean'
            || ($column->type === Schema::TYPE_TINYINT && $column->size === 1)
        ) {
            return "\$form->field(\$model, '$attribute')->checkbox()";
        }

        if ($column->type === Schema::TYPE_TEXT) {
            return "\$form->field(\$model, '$attribute')->textarea(['rows' => 6])";
        }

        if ($column->phpType === 'integer' || $column->phpType === 'float') {
            return "\$form->field(\$model, '$attribute')->input('number')";
        }

        if (preg_match('/^(password|pass|passwd|passcode|psw)$/i', $column->name)) {
            $input = 'passwordInput';
        } else {
            $input = 'textInput';
        }

        if (is_array($column->enumValues) && !empty($column->enumValues)) {
            $dropDownOptions = [];
            foreach ($column->enumValues as $enumValue) {
                $dropDownOptions[$enumValue] = Inflector::humanize($enumValue);
            }
            return "\$form->field(\$model, '$attribute')->dropDownList("
                . preg_replace("/\n\s*/", ' ', VarDumper::export($dropDownOptions)) . ", ['prompt' => ''])";
        }

        if ($column->phpType !== 'string' || $column->size === null) {
            return "\$form->field(\$model, '$attribute')->{$input}()";
        }

        return "\$form->field(\$model, '$attribute')->{$input}(['maxlength' => true])";
    }

    /**
     * @inheritDoc
     */
    public function generateColumnFormat($column): string
    {
        if ($column->phpType === 'boolean' || $column->type === 'boolean') {
            return 'boolean';
        }

        if ($column->type === 'text') {
            return 'ntext';
        }

        if (
            $column->phpType === 'integer' &&
            ($column->type === Schema::TYPE_TIMESTAMP || stripos($column->name, 'time') !== false)
        ) {
            return 'datetime';
        }

        if (stripos($column->name, 'email') !== false) {
            return 'email';
        }

        if (preg_match('/(\b|[_-])(image|picture|img|photo)(\b|[_-])/i', $column->name)) {
            return 'image';
        }

        if (preg_match('/(\b|[_-])(price|cost|amount)(\b|[_-])/i', $column->name)) {
            return 'currency';
        }

        if (preg_match('/(\b|[_-])url(\b|[_-])/i', $column->name)) {
            return 'url';
        }

        if (in_array($column->phpType, ['integer', 'double', 'float'], true)) {
            return 'number';
        }

        return 'text';
    }

    /**
     * @inheritDoc
     */
    public function generateSearchRules(): array
    {
        $table = $this->getTableSchema();
        if ($table === false) {
            return ["[['" . implode("', '", $this->getColumnNames()) . "'], 'safe']"];
        }

        $types = [];
        $driverName = $this->getClassDbDriverName();
        foreach ($table->columns as $column) {
            switch ($column->type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                    $types['integer'][] = $column->name;
                    break;
                case Schema::TYPE_TINYINT:
                    if (
                        $driverName !== 'pgsql'
                        && $column->size === 1
                        && in_array($column->defaultValue, [0, 1], true)
                    ) {
                        $types['boolean'][] = $column->name;
                    } else {
                        $types['integer'][] = $column->name;
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
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                case Schema::TYPE_JSON:
                default:
                    $types['safe'][] = $column->name;
                    break;
            }
        }

        $rules = [];
        foreach ($types as $type => $columns) {
            $rules[] = "[['" . implode("', '", $columns) . "'], '{$type}']";
        }

        return $rules;
    }

    /**
     * @inheritDoc
     */
    public function generateActionParams(): string
    {
        $pks = $this->modelClass::primaryKey();
        return 'int $' . implode(', int $', $pks);
    }

    /**
     * @inheritDoc
     */
    public function generateActionParamComments(): array
    {
        /* @var \yii\db\ActiveRecord $class */
        $pks = $this->modelClass::primaryKey();
        $table = $this->getTableSchema();
        if ($table === false) {
            $params = [];
            foreach ($pks as $pk) {
                $type = strtolower(substr($pk, -2)) === 'id' ? 'int' : 'string';
                $params[] = '@param ' . $type . ' $' . $pk . ' ID';
            }

            return $params;
        }

        $typeAliases = [
            'boolean' => 'bool',
            'integer' => 'int',
        ];

        if (count($pks) === 1) {
            $type = $table->columns[$pks[0]]->phpType;
            return ['@param ' . ($typeAliases[$type] ?? $type) . ' $id'];
        }

        $params = [];
        foreach ($pks as $pk) {
            $type = $table->columns[$pk]->phpType;
            $params[] = '@param ' . ($typeAliases[$type] ?? $type) . ' $' . $pk;
        }

        return $params;
    }
}
