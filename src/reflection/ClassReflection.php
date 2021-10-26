<?php

namespace app\reflection;

use app\tokenizer\Token;
use SplFileInfo;
use ReflectionClass;
use ReflectionException;
use function array_slice;
use function file;
use function file_get_contents;

/**
 * Extended reflection class.
 *
 * @property-read \ReflectionMethod[] $methods
 * @property-read \ReflectionProperty[] $properties
 * @property-read \ReflectionClassConstant[] $constants
 * @property-read string|false $fileName
 * @property-read SplFileInfo|false $fileInfo
 */
class ClassReflection extends ReflectionClass
{
    /**
     * @var SplFileInfo|false Information for class file
     */
    private $fileInfo;

    /**
     * @param string $name
     * @return mixed
     * @throws ReflectionException
     */
    public function __get(string $name)
    {
        $method = 'get' . ucfirst($name);
        if (!method_exists($this, $method)) {
            throw new ReflectionException("Property '$name' not defined");
        }
        return $this->{$method}();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return method_exists($this, 'get' . ucfirst($name));
    }

    /**
     * Returns class source into PHP tokens.
     *
     * @param bool $classOnly If true, method return "... class ... {}" tokens, else, return all tokens in file
     * @return Token[]|false
     */
    public function getTokens(bool $classOnly = true)
    {
        if ($this->getFileName() === false) {
            return false;
        }

        if ($classOnly) {
            $code = file($this->getFileName());
            $code = array_slice($code, $this->getStartLine(), $this->getEndLine() - $this->getStartLine());
            $tokens = Token::tokenize("<?php\n" . implode('', $code));
            // remove "<?php\n"
            $tokens = array_slice($tokens, 2);
        } else {
            $tokens = Token::tokenize(file_get_contents($this->getFileName()));
        }

        return $tokens;
    }

    /**
     * @return SplFileInfo|false
     */
    public function getFileInfo()
    {
        if ($this->fileInfo === null) {
            $file = $this->getFileName();
            $this->fileInfo = $file !== false ? new SplFileInfo($file) : false;
        }

        return $this->fileInfo;
    }
}
