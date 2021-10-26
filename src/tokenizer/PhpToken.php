<?php

namespace app\tokenizer;

use function current;
use function in_array;
use function is_array;
use function is_int;
use function is_string;
use function max;
use function mb_strlen;
use function ord;
use function token_get_all;
use function token_name;

use const T_COMMENT;
use const T_DOC_COMMENT;
use const T_INLINE_HTML;
use const T_WHITESPACE;
use const TOKEN_PARSE;

/**
 * Extended class `PhpToken` (PHP8).
 */
class PhpToken
{
    /**
     * @var string Text of unknown token
     */
    public const T_UNKNOWN = 'UNKNOWN';

    /**
     * @var int Identifier
     */
    public $id;

    /**
     * @var string The textual content of the token
     */
    public $text;

    /**
     * @var int The starting line number (1-based) of the token
     */
    public $line;

    /**
     * @var int The starting position (0-based) in the tokenized string
     */
    public $pos;

    /**
     * @var string Symbolic name of PHP token
     */
    protected $name;

    /**
     * Creates a new instance.
     *
     * @param int|string $id `T_*` constant or an ASCII codepoint representing a single-char token
     * @param string $text The textual content of the token
     * @param int $line The starting line number of the token, first line - 0
     * @param int $position The starting position in the tokenized string
     * @return void
     */
    public function __construct($id, string $text, int $line = 0, int $position = 0)
    {
        $this->id = is_int($id) ? $id : ord($id);
        $this->text = $text;
        $this->line = max(1, $line);
        $this->pos = max(0, $position);
    }

    /**
     * Returns the name of the token.
     *
     * @return string|null
     */
    public function getTokenName(): ?string
    {
        if ($this->name === null) {
            $this->name = token_name($this->id);
        }
        return $this->name === self::T_UNKNOWN ? null : $this->name;
    }

    /**
     * Tells whether the token is of given kind.
     *
     * @param int|string|array $kind Either a single value to match the token's id or textual content, or an array
     *     thereof
     * @return bool
     */
    public function is($kind): bool
    {
        $token = (is_int($kind) || (is_array($kind) && is_int(current($kind))))
            ? $this->id
            : $this->getTokenName();
        return in_array($token, (array) $kind, true);
    }

    /**
     * Tells whether the token would be ignored by the PHP parser.
     *
     * @return bool
     */
    public function isIgnorable(): bool
    {
        return $this->is([T_WHITESPACE, T_COMMENT, T_DOC_COMMENT, T_INLINE_HTML]);
    }

    /**
     * Returns the textual content of the token.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->text;
    }

    /**
     * Splits given source into PHP tokens, represented by self objects.
     *
     * @param string $code The PHP source to parse
     * @param int $flags `TOKEN_PARSE` - recognises the ability to usereserved words in specific contexts
     * @return static[]
     */
    public static function tokenize(string $code, int $flags = TOKEN_PARSE): array
    {
        $tokens = [];
        $line = 1;
        $position = 0;
        foreach (token_get_all($code, $flags) as $token) {
            if (is_string($token)) {
                $text = $token;
                $id = $text;
            } else {
                [$id, $text, $line] = $token;
            }
            $tokens[] = new static($id, $text, $line - 1, $position);
            $position += mb_strlen($text);
        }

        return $tokens;
    }
}
