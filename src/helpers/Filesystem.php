<?php

namespace app\helpers;

use yii\helpers\FileHelper;
use Yii;

use function is_array;
use function str_replace;
use function strlen;
use function substr;
use function strncmp;
use function uksort;

/**
 * Extended `FileHelper` with supporting path aliases.
 */
class Filesystem extends FileHelper
{
    /**
     * @var string[] An array of path aliases as path/alias pairs
     */
    protected static array $aliases;

    /**
     * @inheritDoc
     */
    public static function normalizePath($path, $ds = DIRECTORY_SEPARATOR): string
    {
        return parent::normalizePath(Yii::getAlias($path), $ds);
    }

    /**
     * @inheritDoc
     */
    public static function copyDirectory($src, $dst, $options = []): void
    {
        parent::copyDirectory(Yii::getAlias($src), Yii::getAlias($dst), $options);
    }

    /**
     * @inheritDoc
     */
    public static function removeDirectory($dir, $options = []): void
    {
        parent::removeDirectory(Yii::getAlias($dir), $options);
    }

    /**
     * @inheritDoc
     */
    public static function unlink($path): void
    {
        parent::unlink(Yii::getAlias($path));
    }

    /**
     * @inheritDoc
     */
    public static function createDirectory($path, $mode = 0775, $recursive = true): bool
    {
        return parent::createDirectory(Yii::getAlias($path), $mode, $recursive);
    }

    /**
     * @inheritDoc
     * @throws yii\base\Exception
     */
    public static function changeOwnership($path, $ownership, $mode = null): void
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
     * @param bool $reset the reset cache
     * @psalm-return array<string,string|array>
     */
    public static function getAliases(bool $reset = false): array
    {
        if (!isset(static::$aliases) || $reset) {
            static::$aliases = [];
            /** @var string|string[] $basePaths */
            foreach (Yii::$aliases as $alias => $basePaths) {
                if (!is_array($basePaths)) {
                    $basePaths = [$alias => $basePaths];
                }
                foreach ($basePaths as $alias2 => $basePath) {
                    $basePath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $basePath);
                    static::$aliases[$basePath] = $alias2;
                }
            }
            // sort by path length(long > short) for `self::findAlias()`
            uksort(static::$aliases, 'strcmp');
        }

        return static::$aliases;
    }

    /**
     * Returns path alias.
     *
     * @param string $path the real path
     * @return string|false
     */
    public static function findAlias(string $path)
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
