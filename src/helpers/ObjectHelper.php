<?php

namespace app\helpers;

use PhpToken;
use Stringable;
use ReflectionClass;

/**
 * Yii2 developer's helper.
 */
abstract class ObjectHelper
{
    public static function isStringable(object $object): bool
    {
        return $object instanceof Stringable || method_exists($object, '__toString');
    }

    /**
     * @var string[] An array of class names in file => class pairs
     */
    protected static array $fileClasses = [];

    /**
     * Returns the namespace of given class/object.
     *
     * @param string|object $classOrObject the class name or object
     * @psalm-param class-string|object $classOrObject
     * @return string
     */
    public static function getClassNamespace($classOrObject): string
    {
        return (new ReflectionClass($classOrObject))->getNamespaceName();
    }

    /**
     * Returns the namespace of given class/object.
     *
     * @param string|object $classOrObject the class name or object
     * @psalm-param class-string|object $classOrObject
     * @param string|null $trimSuffix the suffix of class name to trim
     * @return string
     */
    public static function getClassName($classOrObject, string $trimSuffix = null): string
    {
        $className = (new ReflectionClass($classOrObject))->getShortName();
        if ($trimSuffix !== null && str_ends_with($className, $trimSuffix)) {
            $className = substr($className, 0, -strlen($trimSuffix));
        }

        return $className;
    }

    /**
     * Returns the class name in given file.
     *
     * @param string $fileOrAlias the file name or his alias
     * @param bool $shortName whether to return class name without namespace
     * @return string|null
     */
    public static function getClassByFile(string $fileOrAlias, bool $shortName = false): ?string
    {
        $path = \Yii::getAlias($fileOrAlias, false);
        if ($path === false || !\file_exists($path)) {
            return null;
        }
        $code = file_get_contents($path);
        $tokens = PhpToken::tokenize($code);
        unset($code, $path);

        $namespace = '';
        if ($shortName) {
            foreach ($tokens as $index => $token) {
                if ($token->is(T_CLASS)) {
                    return $tokens[$index + 2]->text;
                }
            }
        } else {
            foreach ($tokens as $index => $token) {
                if ($token->is([T_NAMESPACE, T_NS_SEPARATOR, T_CLASS])) {
                    $index += $token->id === T_NS_SEPARATOR ? 1 : 2;
                    if ($token->id === T_CLASS) {
                        return $namespace . $tokens[$index]->text;
                    }
                    $namespace .= $tokens[$index]->text . '\\';
                }
            }
        }

        return null;
    }

    /**
     * Returns the class names of files in given directory.
     *
     * @param string $pathOrAlias the path or his alias to directory
     * @param string $namespace the namespace mapped to given directory
     * @param string $fileNamePattern the pattern of file name without file extension, {@see Filesystem::findFiles()}
     * @param bool $short whether to trim base directory and namespace
     * @param bool $recursive whether to search recursive in sub-directories
     * @return array an array of classes as file => class pairs
     */
    public static function findClasses(
        string $pathOrAlias,
        string $namespace,
        string $fileNamePattern = '*',
        bool $short = false,
        bool $recursive = true
    ): array {
        $baseDir = rtrim(\Yii::getAlias($pathOrAlias), '\/') . DIRECTORY_SEPARATOR;
        $fileNamePattern = ($fileNamePattern ?: '*') . '.php';

        $classes = [];
        $files = Filesystem::findFiles($baseDir, ['only' => [$fileNamePattern], 'recursive' => $recursive]);
        if (!empty($files)) {
            $namespace = rtrim($namespace, '\\') . '\\';
            $length = strlen($baseDir);
            foreach ($files as $file) {
                $path = substr($file, $length);
                // trim `.php` extension and replace slashes
                $class = str_replace('/', '\\', substr($path, 0, -4));
                if ($short) {
                    $classes[$path] = $class;
                } else {
                    $classes[$baseDir . $path] = $namespace . $class;
                }
            }
        }

        return $classes;
    }
}
