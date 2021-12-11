<?php

namespace app\reflection;

use SplFileInfo;
use ReflectionClass;

/**
 * Extended class reflection.
 *
 * @property-read \ReflectionMethod[] $methods
 * @property-read \ReflectionProperty[] $properties
 * @property-read array $defaultProperties The default properties
 * @property-read \ReflectionClassConstant[] $reflectionConstants
 * @property-read array $constants An array of class constants as constant name/value pairs
 * @property-read \app\tokenizer\Token[]|false $tokens
 * @property-read ReflectionClass[] $traits
 * @property-read ReflectionClass[] $interfaces
 * @property-read int $modifiers The bitmask of modifier constants
 * @property-read string|false $docComment
 * @property-read string|false $fileName
 * @property-read SplFileInfo|false $fileInfo
 * @property-read string $namespaceName
 * @property-read string|false $parentClass
 */
class ClassReflection extends ReflectionClass
{
    use ReflectionTrait;

    /**
     * Returns methods declared in reflected class.
     *
     * @param int $flags The bitmask of attribute constants
     * @return array
     */
    public function getDeclaredMethods(int $flags = 0): array
    {
        $methods = [];
        foreach ($this->getMethods($flags) as $method) {
            if ($method->getDeclaringClass() === $this->name) {
                $methods[$method->name] = $method;
            }
        }
        return $methods;
    }

    /**
     * Returns properties declared in reflected class.
     *
     * @param int $flags The bitmask of attribute constants
     * @return array
     */
    public function getDeclaredProperties(int $flags = 0): array
    {
        $properties = [];
        foreach ($this->getProperties($flags) as $property) {
            if ($property->getDeclaringClass() === $this->name) {
                $properties[$property->name] = $property;
            }
        }
        return $properties;
    }
}
