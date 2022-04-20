<?php

namespace app\models;

use DateTimeInterface;
use yii\base\InvalidArgumentException;
use yii\behaviors\AttributeTypecastBehavior;
use yii\helpers\Inflector;

use function array_diff;

/**
 * Расширенная модель ActiveRecord: при доступе к магическим свойствам вначале проверяется наличие get/set метода.
 * @inheritDoc
 * @property-write string $formName
 */
abstract class ActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * @var string Сценарий добавления новой записи (исключает генерируемые атрибуты: дата создания, id автора и т.д.)
     */
    public const SCENARIO_NEW = 'new';
    /**
     * @var string Сценарий публикации записи (исключает приватные атрибуты: токены, хэши и т.д.)
     */
    public const SCENARIO_PUBLIC = 'public';

    /**
     * @var string The name of HTML form
     */
    protected string $formName;

    /**
     * @inheritDoc
     */
    public function init(): void
    {
        parent::init();
        /*
        if (empty($this->getAttributes())) {
            $this->setAttributes(array_fill_keys($this->attributes(), null), false);
        }
        */
        // Загрузить значения по умолчанию для отображения в форме добавления новой записи
        if ($this->getScenario() === self::SCENARIO_NEW) {
            $this->loadDefaultValues();
        }
    }

    /**
     * Возвращает имена приватныx атрибутов (токен, хэш и т.д.).
     *
     * @return string[]
     */
    protected function privateAttributes(): array
    {
        return [];
    }

    /**
     * Возвращает имена генерируемыx атрибутов (дата создания, id автора и т.д.).
     *
     * @return string[]
     */
    protected function genericAttributes(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function scenarios(): array
    {
        /** @var array[] $scenarios */
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_NEW] = array_diff($scenarios[self::SCENARIO_DEFAULT], $this->genericAttributes());
        $scenarios[self::SCENARIO_PUBLIC] = array_diff($scenarios[self::SCENARIO_DEFAULT], $this->privateAttributes());

        return $scenarios;
    }

    /**
     * @inheritDoc
     */
    public function formName(): string
    {
        if (!$this->formName) {
            $this->formName = Inflector::underscore(parent::formName());
        }
        return $this->formName;
    }

    /**
     * Sets the name of HTML form.
     *
     * @param string $name The form name
     * @return void
     */
    public function setFormName(string $name)
    {
        $this->formName = $name;
    }

    /**
     * Returns attribute types as attribute name/type pairs.
     *
     * @return array
     */
    protected function attributeTypes(): array
    {
        return [];
    }

    /**
     * Attribute type cast: timestamp.
     *
     * @param string|int|float|DateTimeInterface|null $value
     * @return int
     */
    protected function attributeCastTimestamp($value): int
    {
        if ($value instanceof DateTimeInterface) {
            $value = $value->getTimestamp();
        } elseif (is_string($value)) {
            $value = strtotime($value);
        }
        if (!is_int($value) && !is_float($value)) {
            throw new InvalidArgumentException('Invalid timestamp: ' . $value);
        }
        return (int) $value;
    }

    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['attributeTypeCast'] = [
            'class' => AttributeTypecastBehavior::class,
            'typecastBeforeSave' => true,
            'typecastAfterFind' => true,
            'attributeTypes' => $this->attributeTypes(),
        ];

        return $behaviors;
    }

    /**
     * Normalizes and returns attribute name (optional converts camelCase to under_score).
     *
     * @param string $name The attribute name
     * @return string
     */
    protected function normalizeAttributeName(string $name): string
    {
        if (!$this->hasAttribute($name)) {
            $underscoreName = Inflector::underscore($name);
            if ($this->hasAttribute($underscoreName)) {
                return $underscoreName;
            }
        }
        return $name;
    }

    /**
     * @inheritDoc
     */
    public function __isset($name): bool
    {
        $normalizedName = $this->normalizeAttributeName($name);
        if ($normalizedName !== $name) {
            return true;
        }
        return parent::__isset($normalizedName);
    }

    /**
     * @inheritDoc
     */
    public function __unset($name)
    {
        parent::__unset($this->normalizeAttributeName($name));
    }

    /**
     * @inheritDoc
     */
    public function __get($name)
    {
        return parent::__get($this->normalizeAttributeName($name));
    }

    /**
     * @inheritDoc
     */
    public function __set($name, $value)
    {
        parent::__set($this->normalizeAttributeName($name), $value);
    }
}
