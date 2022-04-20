<?php

namespace app\helpers;

use ReflectionClass;
use JsonSerializable;
use yii\base\InvalidArgumentException;
use yii\helpers\BaseJson;
use Yii;

/**
 * @inheritDoc
 */
class Json extends BaseJson
{
    /**
     * Encodes the given object into a JSON string.
     *
     * @param object $object the object to encode
     * @param int $options the encoding options
     * @return string
     * @throws InvalidArgumentException [[encode()]] error
     */
    public static function encodeObject(object $object, int $options = 320): string
    {
        $data = static::recursiveConvertToArray($object);

        return static::encode($data, $options);
    }

    /**
     * Decodes the given JSON object into a PHP object by [[Yii::createObject()]].
     *
     * @param string $json the JSON object to decode, must be compatible with [[Yii::createObject()]]
     * @return object
     * @throws InvalidArgumentException [[decode()]] error
     * @throws InvalidArgumentException if decoded data is not array
     * @throws yii\base\InvalidConfigException if the configuration is invalid
     */
    public static function decodeObject(string $json): object
    {
        $data = static::decode($json);
        if (!is_array($data)) {
            throw new InvalidArgumentException('Not compatible with Yii::createObject()');
        }

        return static::recursiveConvertToObject($data);
    }

    /**
     * Recursive converts array values to objects.
     *
     * @param array $array the object configuration
     * @return object
     * @throws yii\base\InvalidConfigException if the configuration is invalid
     */
    protected static function recursiveConvertToObject(array $array): object
    {
        foreach ($array as $key => $value) {
            if (is_array($value) && isset($value['class'])) {
                $array[$key] = static::recursiveConvertToObject($value);
            }
        }

        return Yii::createObject($array);
    }

    /**
     * Recursive converts object properties to array.
     *
     * @param object $object the object to convert
     * @return array|null
     */
    protected static function recursiveConvertToArray(object $object): ?array
    {
        if ($object instanceof JsonSerializable) {
            $data = $object->jsonSerialize();
            if (!is_array($data)) {
                return $data;
            }
        } else {
            $data = get_object_vars($object);
        }

        foreach ($data as $key => $value) {
            if (is_object($value)) {
                $data[$key] = static::recursiveConvertToArray($value);
            }
        }

        $class = new ReflectionClass($object);
        if ($class->isInstantiable()) {
            $data['class'] = get_class($object);
        } else {
            // empty uninstantiable objects was nulled
            if (empty($data)) {
                return null;
            }

            $data['class'] = 'stdClass';
        }

        return $data;
    }

    /**
     * Encodes PHP data into JSON string and writes in file.
     *
     * @param string $path the path to JSON file
     * @param mixed $data the data to encode
     * @param int $options the decoding options
     * @return void
     * @throws InvalidArgumentException [[encode()]] error
     * @throws yii\base\Exception
     */
    public static function encodeFile(string $path, $data, int $options = 320)
    {
        $path = Filesystem::normalizePath($path);
        if (!file_exists($path)) {
            Filesystem::createDirectory(dirname($path));
        }
        $json = static::encode($data, $options);
        file_put_contents($path, $json);
    }

    /**
     * Decodes given JSON file into PHP data structure.
     *
     * @param string $path the path to JSON file
     * @param bool $asArray whether to return objects in terms of associative arrays
     * @return mixed the PHP data
     * @throws InvalidArgumentException if there is any decoding error
     */
    public static function decodeFile(string $path, bool $asArray = true)
    {
        $path = Filesystem::normalizePath($path);
        $json = file_get_contents($path);

        return static::decode($json, $asArray);
    }
}
