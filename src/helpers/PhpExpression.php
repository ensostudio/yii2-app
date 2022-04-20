<?php

namespace app\helpers;

use InvalidArgumentException;
use JsonSerializable;

/**
 * Class marks a string as a PHP expression.
 *
 * Using to generate PHP code for configuration files and views/classes by Gii.
 *
 * @see VarDumper
 */
class PhpExpression implements JsonSerializable
{
    /**
     * Behavior flag "wrap expression in PHP tags": `<?php expression ?>`.
     */
    public const WRAP_IN_TAGS = 1;
    /**
     * Behavior flag "print result of expression": `print expression` or `<?= expression ?>`.
     */
    public const WRAP_IN_TAGS_AND_ECHO = 2;

    /**
     * @var string the PHP expression represented by this object
     */
    protected string $expression;
    /**
     * @var int the expression modifierr: the class constant or zero
     */
    protected int $wrapMode;

    /**
     * Creates new instance.
     *
     * @param string $expression the PHP expression
     * @param int $wrapMode the expression modifierr: the class constant or zero
     */
    public function __construct(string $expression, int $wrapMode = 0)
    {
        $this->expression = $expression;
        $this->setWrapMode($wrapMode);
    }

    /**
     * Sets the expression modifier.
     *
     * @param int $value the expression modifier: the class constant or zero
     * @return void
     * @throws InvalidArgumentException if invalid expression modifier
     */
    public function setWrapMode(int $value)
    {
        if ($value < 0 || $value > 2) {
            throw new InvalidArgumentException("Invalid expression modifier: $value");
        }
        $this->wrapMode = $value;
    }

    /**
     * Returns a PHP expression with optional modification.
     */
    public function getExpression(): string
    {
        if ($this->wrapMode > 0) {
            return ($this->wrapMode === static::WRAP_IN_TAGS ? '<?php ' : '<?= ') . $this->expression . ' ?>';
        }

        return $this->expression;
    }

    /**
     * @return string the PHP expression
     */
    public function jsonSerialize(): string
    {
        return $this->getExpression();
    }

    /**
     * @return string the PHP expression
     */
    public function __toString(): string
    {
        return $this->getExpression();
    }
}
