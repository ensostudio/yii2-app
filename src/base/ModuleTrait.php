<?php

namespace app\base;

use ReflectionClass;
use yii\base\Module;
use yii\base\Application;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use Yii;

/**
 * Module/application trait.
 *
 * The common structure:
 * - `src/commands/` CLI controllers
 * - `src/controllers/` Web controllers
 * - `views/` templates
 * - `messages/` I18N translations
 *
 * @property-read string $baseNamespace The module namespace
 * @property-read string $sourcePath the path to base directory of sources of the module/application
 * @property-read string $i18nPath The path to the base directory of translations of the module/application
 * @property-read string $i18nCategory The category of I18N messages
 */
trait ModuleTrait
{
    /**
     * @var string The unique identifier of this module
     */
    protected string $uniqueId;
    /**
     * @var string The module namespace
     */
    protected string $baseNamespace;
    /**
     * @var string The category of I18N messages
     */
    protected string $i18nCategory;

    /**
     * @var bool Whether to auto fill controller's map
     * @see self::loadControllerMap()
     */
    public bool $autoloadControllerMap = false;

    /**
     * @inheritDoc
     */
    public function __construct(string $id, Module $parent = null, array $config = [])
    {
        if (!isset($config['basePath'])) {
            // Module class in sub-directory `src`
            $this->setBasePath(dirname((new ReflectionClass($this))->getFileName(), 2));
        }
        if (!isset($config['controllerPath'], $this->controllerNamespace)) {
            $this->setControllerPath(
                $config['basePath'] . DIRECTORY_SEPARATOR
                . 'src' . DIRECTORY_SEPARATOR
                . (Yii::$app instanceof \yii\console\Application ? 'commands' : 'controllers')
            );
        }
        if (!isset($config['controllerNamespace'])) {
            $this->controllerNamespace = $this->getBaseNamespace()
                . (Yii::$app instanceof \yii\console\Application ? 'commands' : 'controllers');
        }

        parent::__construct($id, $parent, $config);

        if ($this->autoloadControllerMap && empty($this->controllerMap) && is_dir($this->getControllerPath())) {
            $this->loadControllerMap();
        }
    }

    /**
     * Fills `controllerMap` property by files in controller's directory.
     *
     * @return void
     */
    protected function loadControllerMap()
    {
        $prefixLength = strlen($this->getControllerPath()) + 1;
        $files = FileHelper::findFiles($this->getControllerPath(), ['only' => ['*Controller.php']]);
        foreach ($files as $file) {
            // converts '.../src/controllers/BarBazController.php' to 'BarBazController'
            $class = str_replace('/', '\\', substr($file, $prefixLength, -4));
            // converts 'foo\BarBazController' to 'foo/bar-baz'
            $id = str_replace('\-', '/', Inflector::camel2id(substr($class, 0, -10)));
            $this->controllerMap[$id] = $this->controllerNamespace . '\\' . $class;
        }
    }

    /**
     * @inheritDoc
     */
    public function getUniqueId(): string
    {
        if (!isset($this->uniqueId)) {
            $this->uniqueId = parent::getUniqueId();
        }

        return $this->uniqueId;
    }

    /**
     * Returns the base namespace of the module/application.
     */
    public function getBaseNamespace(): string
    {
        if (!isset($this->baseNamespace)) {
            $this->baseNamespace = (new ReflectionClass($this))->getNamespaceName();
        }

        return $this->baseNamespace;
    }

    /**
     * Returns the path to the base directory of sources of the module/application.
     */
    public function getSourcePath(): string
    {
        return $this->getBasePath() . DIRECTORY_SEPARATOR . 'src';
    }

    /**
     * Returns the path to the base directory of translations of the module/application.
     */
    public function getI18nPath(): string
    {
        return $this->getBasePath() . DIRECTORY_SEPARATOR . 'messages';
    }

    /**
     * Returns the category of translations of the module/application.
     */
    public function getI18nCategory(): string
    {
        if (!isset($this->i18nCategory)) {
            $this->i18nCategory = $this instanceof Application
                ? 'app/' . $this->id
                : 'modules/' . $this->getUniqueId();
        }

        return $this->i18nCategory;
    }

    /**
     * Translates the module/application message.
     *
     * @param string $message the source message
     * @param array $params the message parameters
     * @param string|null $language the language name
     * @see Yii::t()
     */
    public function translate(string $message, array $params = [], string $language = null): string
    {
        return Yii::t($this->getI18nCategory(), $message, $params, $language);
    }
}
