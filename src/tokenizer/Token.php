<?php

namespace app\tokenizer;

use yii\helpers\Json;

const T_BRACE_OPEN = 40;
const T_BRACE_CLOSE = 41;
const T_COMMA = 44;
const T_SEMICOLON = 59;
const T_ASSIGN = 61;
const T_SQUARE_BRACE_OPEN = 91;
const T_SQUARE_BRACE_CLOSE = 93;
const T_CURLY_BRACE_OPEN = 123;
const T_CURLY_BRACE_CLOSE = 125;
const T_DOT = 260;

/**
 * Extended PHP token.
 */
class Token extends PhpToken
{
    /**
     * @var array[] The extra tokens, note that ID's can be changes in PHP 8!
     */
    public const EXTRAS = [
        '(' => [T_BRACE_OPEN, 'T_BRACE_OPEN'],
        ')' => [T_BRACE_CLOSE, 'T_BRACE_CLOSE'],
        ',' => [T_COMMA, 'T_COMMA'],
        ';' => [T_SEMICOLON, 'T_SEMICOLON'],
        '=' => [T_ASSIGN, 'T_ASSIGN'],
        '[' => [T_SQUARE_BRACE_OPEN, 'T_SQUARE_BRACE_OPEN'],
        ']' => [T_SQUARE_BRACE_CLOSE, 'T_SQUARE_BRACE_CLOSE'],
        '{' => [T_CURLY_BRACE_OPEN, 'T_CURLY_BRACE_OPEN'],
        '}' => [T_CURLY_BRACE_CLOSE, 'T_CURLY_BRACE_CLOSE'],
        '.' => [T_DOT, 'T_DOT'],
    ];

    /**
     * @var array[] The braces as tag open/close pairs
     */
    public const BRACES = [
        T_BRACE_OPEN => T_BRACE_CLOSE,
        T_SQUARE_BRACE_OPEN => T_SQUARE_BRACE_CLOSE,
        T_CURLY_BRACE_OPEN => T_CURLY_BRACE_CLOSE,
    ];

    /**
     * Returns the name of the token.
     *
     * @return string|null
     */
    public function getTokenName(): ?string
    {
        $name = parent::getTokenName();
        if ($name === null && isset(static::EXTRAS[$this->text])) {
            $this->name = static::EXTRAS[$this->text][1];
            $name = $this->name;
        }

        return $name;
    }

    /**
     * Dumping token object.
     *
     * @return array
     */
    public function __debugInfo(): array
    {
        $dump = \get_object_vars($this);
        if (!$this->isNumber() && !\in_array($this->id, [T_NS_SEPARATOR, \T_CONSTANT_ENCAPSED_STRING], true)) {
            $dump['text'] = Json::encode($this->id === \T_OPEN_TAG ? '&lt;?php' : $this->text);
        }
        if ($dump['name'] === null) {
            $dump['name'] = $this->getTokenName();
        }

        return $dump;
    }

    /**
     * Tells whether the token is interface, trait or class.
     *
     * @return bool
     */
    public function isClassType(): bool
    {
        return $this->is([\T_CLASS, \T_INTERFACE, \T_TRAIT]);
    }

    /**
     * Tells whether the token is function.
     *
     * @return bool
     */
    public function isFunction(): bool
    {
        $tokens = \defined('T_FN') ? [\T_FUNCTION, \T_FN] : [\T_FUNCTION];
        return $this->is($tokens);
    }

    /**
     * Tells whether the token is variable.
     *
     * @return bool
     */
    public function isVar(): bool
    {
        return $this->is(\T_VARIABLE);
    }

    /**
     * Tells whether the token is modifier.
     *
     * @return bool
     */
    public function isModifier(): bool
    {
        return $this->is([\T_PRIVATE, \T_PROTECTED, \T_PUBLIC, \T_STATIC, \T_ABSTRACT, \T_FINAL, \T_GLOBAL]);
    }

    /**
     * Tells whether the token is type cast (`(type)`).
     *
     * @return bool
     */
    public function isTypeCast(): bool
    {
        return $this->is(
            [\T_UNSET_CAST, \T_STRING_CAST, \T_OBJECT_CAST, \T_INT_CAST, \T_DOUBLE_CAST, \T_BOOL_CAST, \T_ARRAY_CAST]
        );
    }

    /**
     * Tells whether the token is number.
     *
     * @return bool
     */
    public function isNumber(): bool
    {
        return $this->is([\T_LNUMBER, \T_DNUMBER]);
    }

    /**
     * Tells whether the token is string.
     *
     * @return bool
     */
    public function isString(): bool
    {
        return $this->is([\T_STRING, \T_NUM_STRING, \T_ENCAPSED_AND_WHITESPACE, \T_CONSTANT_ENCAPSED_STRING]);
    }

    /**
     * Tells whether the token is PHP comment.
     *
     * @return bool
     */
    public function isComment(): bool
    {
        return $this->is([\T_DOC_COMMENT, \T_COMMENT]);
    }

    /**
     * Tells whether the token is DocBlock.
     *
     * @return bool
     */
    public function isDocBlock(): bool
    {
        return $this->is(\T_DOC_COMMENT) && \preg_match('~^\s*/\*\*\s*+\*/\s*$~', $this->text);
    }
}
