<?php

namespace app\reflection;

use app\tokenizer\Token;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use SplFileInfo;

use function array_slice;
use function file;
use function file_get_contents;
use function implode;
use function method_exists;

/**
 * Extended PHP reflection.
 *
 * @property-read string|false $fileName The file in which the class has been defined
 * @property-read SplFileInfo|false $fileInfo The information about containing file
 * @property-read Token[]|false $tokens The source code in PHP tokens
 */
trait ReflectionTrait
{
    /**
     * @var SplFileInfo|false The information about containing file
     */
    protected $fileInfo;

    /**
     * Gets magic property.
     *
     * @param string $name the property name
     * @return mixed
     * @throws ReflectionException
     */
    public function __get(string $name)
    {
        if (!$this->__isset($name)) {
            throw new ReflectionException("Property '$name' not defined");
        }

        return $this->{'get' . ucfirst($name)}();
    }

    /**
     * Checks magic property.
     *
     * @param string $name the property name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return method_exists($this, 'get' . ucfirst($name));
    }

    /**
     * Returns the filename of the file in which the class has been defined.
     *
     * @return string|false
     */
    public function getFileName()
    {
        return ($this instanceof ReflectionClass || $this instanceof ReflectionMethod)
            ? parent::getFileName()
            : $this->getDeclaringClass()->getFileName();
    }

    /**
     * Returns class source into PHP tokens.
     *
     * @param bool $classOnly If true, method return `... class ... {...}` tokens, else, return all tokens in file
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
     * Returns the information about containing file.
     *
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
