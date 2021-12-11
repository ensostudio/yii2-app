<?php

namespace app\helpers;

use yii\bootstrap5\BaseHtml;

/**
 * @inheritDoc
 */
class Html extends BaseHtml
{
    /**
     * @inheritDoc
     * @param bool $prepend Whether to prepend given class(es)
     */
    public static function addCssClass(&$options, $class, $prepend = false)
    {
        $addClasses = static::parseCssClasses($class);
        if (isset($options['class'])) {
            $classes = static::parseCssClasses($options['class']);
            foreach ($addClasses as $key => $addClass) {
                if (is_int($key)) {
                    if (!in_array($addClass, $classes, true)) {
                        if ($prepend) {
                            array_unshift($classes, $addClass);
                        } else {
                            $classes[] = $addClass;
                        }
                    }
                } elseif (!isset($classes[$key])) {
                    $removeKey = array_search($addClass, $classes, true);
                    if ($removeKey !== false) {
                        unset($classes[$removeKey]);
                    }
                    if ($prepend) {
                        $classes = [$key => $addClass] + $classes;
                    } else {
                        $classes[$key] = $addClass;
                    }
                }
            }
            $options['class'] = $classes;
        } else {
            $options['class'] = $addClasses;
        }
    }

    /**
     * Returns list of CSS classes.
     *
     * @param string|array $classes the inline classes
     * @return array
     */
    protected static function parseCssClasses($classes): array
    {
        if (is_array($classes)) {
            return array_unique(static::parseCssClasses(implode(' ', $classes)));
        }
        return preg_split('/\s+/', $classes, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    }
}
