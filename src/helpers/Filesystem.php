<?php

namespace ensostudio\helpers;

use yii\helpers\BaseFileHelper;
use Yii;

/**
 * @inheritDoc
 */
class Filesystem extends BaseFileHelper
{
    /**
     * @var string[]|null An array of path aliases as path/alias pairs
     */
    protected static $aliases;

    /**
     * @inheritDoc
     */
    public static function normalizePath($path, $ds = DIRECTORY_SEPARATOR)
    {
        return parent::normalizePath(Yii::getAlias($path), $ds);
    }

    /**
     * @inheritDoc
     */
    public static function copyDirectory($src, $dst, $options = [])
    {
        parent::copyDirectory(Yii::getAlias($src), Yii::getAlias($dst), $options);
    }

    /**
     * @inheritDoc
     */
    public static function removeDirectory($dir, $options = [])
    {
        parent::removeDirectory(Yii::getAlias($dir), $options);
    }

    /**
     * @inheritDoc
     */
    public static function unlink($path)
    {
        parent::unlink(Yii::getAlias($path));
    }

    /**
     * @inheritDoc
     */
    public static function createDirectory($path, $mode = 0775, $recursive = true)
    {
        return parent::createDirectory(Yii::getAlias($path), $mode, $recursive);
    }

    /**
     * @inheritDoc
     * @throws \yii\base\Exception
     */
    public static function changeOwnership($path, $ownership, $mode = null)
    {
        parent::changeOwnership(Yii::getAlias($path), $ownership, $mode);
    }

    /**
     * @inheritDoc
     */
    public static function findDirectories($dir, $options = []): array
    {
        return parent::findDirectories(Yii::getAlias($dir), $options);
    }

    /**
     * @inheritDoc
     */
    public static function findFiles($dir, $options = []): array
    {
        return parent::findFiles(Yii::getAlias($dir), $options);
    }

    /**
     * Returns flatten list of alias/path pairs.
     *
     * @return array
     */
    public static function getAliases(): array
    {
        if (static::$aliases === null) {
            static::$aliases = [];
            /** @var string|string[] $basePaths */
            foreach (Yii::$aliases as $alias => $basePaths) {
                if (!is_array($basePaths)) {
                    $basePaths = [$alias => $basePaths];
                }
                foreach ($basePaths as $alias => $basePath) {
                    $basePath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $basePath);
                    static::$aliases[$basePath] = $alias;
                }
            }
            // sort by path length(long > short)
            uksort(
                static::$aliases,
                /**
                 * @param string $basePath
                 * @param string $basePath2
                 * @return int
                 */
                static function ($basePath, $basePath2) {
                    return strlen($basePath2) - strlen($basePath);
                }
            );
        }

        return static::$aliases;
    }

    /**
     * Returns path alias.
     *
     * @param string $path the real path
     * @return string|false
     */
    public static function findAlias($path)
    {
        $path = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
        foreach (static::getAliases() as $basePath => $alias) {
            $length = strlen($basePath);
            if (strncmp($path, $basePath, $length) === 0) {
                return $alias . substr($path, $length);
            }
        }

        return false;
    }
}
