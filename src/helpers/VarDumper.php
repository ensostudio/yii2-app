<?php

namespace app\helpers;

use yii\base\Arrayable;
use yii\helpers\BaseVarDumper;
use Closure;
use ReflectionFunction;
use Exception;
use Traversable;

/**
 * @inheritDoc
 */
class VarDumper extends BaseVarDumper
{
    /**
     * @var string The left indent for internal code
     */
    public static string $indent = '    ';
    /**
     * @var int The max. size of array to display in line
     */
    public static int $inlineArrayMaxSize = 3;

    /**
     * @inheritDoc
     * @param mixed $var the variable to export
     * @param string|null $leftOffset the line offset at left as string, NULL - auto detect offset
     */
    public static function export($var, string $leftOffset = null): string
    {
        if ($leftOffset === null) {
            $leftOffset = static::detectLeftOffset();
        }
        return static::exportInternal($var, 0, $leftOffset);
    }

    /**
     * Detects and returns left offset.
     *
     * @return string
     */
    protected static function detectLeftOffset(): string
    {
        // calculate left offset
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
        $line = file($trace['file'])[$trace['line'] - 1];

        return (string) substr($line, 0, -strlen(ltrim($line)));
    }

    /**
     * Recursive export.
     *
     * @param mixed $var the variable to be exported
     * @param int $level the depth level
     * @param string $leftOffset line offset at left as string
     * @return string
     */
    protected static function exportInternal($var, int $level = 0, string $leftOffset = ''): string
    {
        switch (gettype($var)) {
            case 'NULL':
                $result = 'null';
                break;
            case 'array':
                $result = static::exportArray($var, $leftOffset, $level);
                break;
            case 'object':
                if ($var instanceof PhpExpression) {
                    $result = $var->__toString();
                } elseif ($var instanceof Closure) {
                    $result = static::exportClosure($var);
                } else {
                    try {
                        $result = 'unserialize(' . var_export(serialize($var), true) . ')';
                    } catch (Exception $e) {
                        // serialize may fail, for example: if object contains a Closure instance so we use a fallback
                        if ($var instanceof Arrayable) {
                            $result = static::exportArray($var->toArray(), $leftOffset, $level);
                        } elseif ($var instanceof Traversable) {
                            $result = static::exportArray(iterator_to_array($var), $leftOffset, $level);
                        } elseif (method_exists($var, '__toString')) {
                            $result = static::exportInternal($var->__toString(), $level, $leftOffset);
                        } else {
                            $result = static::exportInternal(static::dumpAsString($var), $level, $leftOffset);
                        }
                    }
                }
                break;
            default:
                $result = var_export($var, true);
        }

        return $result;
    }


    /**
     * Recursive export array.
     *
     * @param array $var the variable to export
     * @param string|null $leftOffset line offset at left as string, NULL - auto detect offset
     * @param int $level the depth level
     * @return string
     */
    public static function exportArray(array $var, string $leftOffset = null, int $level = 0): string
    {
        if (empty($var)) {
            return '[]';
        }

        if ($leftOffset === null) {
            $leftOffset = static::detectLeftOffset();
        }
        $keys = array_keys($var);
        $outputKeys = $keys !== array_keys($keys);
        $spaces = str_repeat(static::$indent, $level);
        $multiLine = $level < 3 && count($var) > static::$inlineArrayMaxSize;
        $result = '[';
        foreach ($var as $key => $value) {
            if ($multiLine) {
                $result .= PHP_EOL . $leftOffset . $spaces . static::$indent;
            }
            if ($outputKeys && !isset($keys[$key])) {
                $result .= var_export($key, true) . ' => ';
            }
            $result .= static::exportInternal($value, $level + 1, $leftOffset) . ',';
            if (!$multiLine) {
                $result .= ' ';
            }
        }
        $result = rtrim($result, ' ,');
        if ($multiLine) {
            $result .= PHP_EOL . $leftOffset . $spaces;
        }
        $result .= ']';

        return $result;
    }

    /**
     * Exports a [[Closure]] instance.
     *
     * @param Closure $closure the closure instance.
     * @return string
     */
    protected static function exportClosure(Closure $closure): string
    {
        $reflection = new ReflectionFunction($closure);
        $fileName = $reflection->getFileName();
        $start = $reflection->getStartLine();
        $end = $reflection->getEndLine();

        if ($fileName === false || $start === false || $end === false) {
            return 'function () {/** Error: unable to determine Closure source */}';
        }

        --$start;
        $source = implode('', array_slice(file($fileName), $start, $end - $start));
        $tokens = array_slice(token_get_all('<?php ' . $source), 2);

        $closureTokens = [];
        $pendingParenthesisCount = 0;
        foreach ($tokens as $token) {
            if (isset($token[0]) && ($token[0] === T_FUNCTION || (defined('T_FN') && $token[0] === T_FN))) {
                $closureTokens[] = $token[1];
                continue;
            }
            if ($closureTokens !== []) {
                $closureTokens[] = $token[1] ?? $token;
                if ($token === '}') {
                    $pendingParenthesisCount--;
                    if ($pendingParenthesisCount === 0) {
                        break;
                    }
                } elseif ($token === '{') {
                    $pendingParenthesisCount++;
                }
            }
        }

        return implode('', $closureTokens);
    }
}
