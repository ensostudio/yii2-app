<?php

namespace app\helpers;

/**
 * Application helper.
 */
class AppHelper
{
    /**
     * Returns the merged configuration.
     *
     * - merges configuration arrays recursively
     * - if objects belong to different classes, then replace object, else, merges public properties
     * - if arrays haves different values in `class` key, then replaces array, else, merges arrays
     * - if only second array have `class` key, then replaces first array
     * - if only first array have `class` key, then merge arrays
     *
     *
     *
     *
     * @param array ...$configs the configurations to merge
     * @return array[]
     */
    public static function mergeConfigs(array ...$configs): array
    {
        $result = [];
        foreach ($configs as $config) {

        }

        return $result;
    }
}
