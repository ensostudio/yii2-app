<?php

namespace EnsoStudio\Yii2App;

use ReflectionClass;
use Yii;

/**
 * Application module.
 *
 * @inheritDoc
 * @property-read string $sourcePath The base directory of sources
 * @property-read string $i18nPath The base directory of translations
 * @property string $controllerPath The base directory of controllers
 */
abstract class Module extends \yii\base\Module
{
    /**
     * Module sub-directory with PHP sources(classes and interfaces).
     */
    const SOURCE_DIR = 'src';
    /**
     * Module sub-directory with message translations.
     */
    const I18N_DIR = 'messages';

    /**
     * @event Event an event raised at module initializion.
     */
    const EVENT_INIT = 'init';

    /**
     * @var string the base namespace of module classes
     */
    private $baseNamespace;
    /**
     * @var string the unique identifier of this module
     */
    private $uniqueId;
    /**
     * @var string the root directory of module controllers
     */
    private $controllerPath;

    /**
     * @inheritDoc
     */
    public function __construct($id, $parent = null, array $config = [])
    {
        if (empty($config['baseNamespace']) || empty($config['basePath'])) {
            $class = new ReflectionClass($this);
        }
        if (empty($config['baseNamespace']) && $class->inNamespace()) {
            $config['baseNamespace'] = $class->getNamespaceName();
        } else {
            $config['baseNamespace'] = trim($config['baseNamespace'], '\\');
        }
        if (empty($config['basePath']) && $class->getFileName() !== false) {
            // `/module-package/src/Module.php` > `/module-package`
            $config['basePath'] = dirname($class->getFileName(), 2);
        } else {
            $config['basePath'] = rtrim($config['basePath'], '\/');
        }
        if (empty($config['controllerPath'])) {
            $config['controllerPath'] = $config['basePath'] . DIRECTORY_SEPARATOR . static::SOURCE_DIR
                . (Yii::$app instanceof \yii\web\Application ? '' :  DIRECTORY_SEPARATOR . 'console')
                . DIRECTORY_SEPARATOR . 'controllers';
        }
        if (empty($config['controllerNamespace'])) {
            $isWebApp = Yii::$app instanceof \yii\web\Application;
            $config['controllerNamespace'] = $config['baseNamespace'] . ($isWebApp ? '' : '\console') . '\controllers';
        }

        parent::__construct($id, $parent, $config);
    }

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        // Add the alias for base directory of this module
        $this->setAliases([
            '@' . $this->getUniqueId() => $this->getBasePath()
        ]);
        // Add message translation
        $i18nPath = $this->getBasePath() . DIRECTORY_SEPARATOR . static::I18N_DIR;
        if (is_dir($i18nPath)) {
            Yii::$app->getI18n()->translations[$this->getUniqueId()] = [
                'class' => \yii\i18n\PhpMessageSource::class,
                'sourceLanguage' => Yii::$app->sourceLanguage,
                'basePath' => $i18nPath,
            ];
            Yii::$app->getI18n()->translations[$this->getUniqueId() . '/*'] = [
                'class' => \yii\i18n\PhpMessageSource::class,
                'sourceLanguage' => Yii::$app->sourceLanguage,
                'basePath' => $i18nPath,
            ];
        }

        // Trigger 'init' event
        $this->trigger(static::EVENT_INIT);
    }

    /**
     * @inheritDoc
     */
    public function getUniqueId()
    {
        if ($this->uniqueId === null) {
            $this->uniqueId = parent::getUniqueId();
        }

        return $this->uniqueId;
    }

    /**
     * @inheritDoc
     */
    public function getControllerPath()
    {
        if ($this->controllerPath === null) {
            $this->controllerPath = parent::getControllerPath();
        }

        return $this->controllerPath;
    }

    /**
     * Sets the root directory of module controllers.
     *
     * @param string $path The root directory of module controllers
     * @return void
     */
    public function setControllerPath($path)
    {
        $path = Yii::getAlias(rtrim($path, '\/'));
        $this->controllerPath = $path;
    }

    /**
     * Returns the base directory of sources.
     *
     * @return string
     */
    public function getSourcePath()
    {
        return $this->getBasePath() . DIRECTORY_SEPARATOR . static::SOURCE_DIR;
    }

    /**
     * Returns the base directory of translations.
     *
     * @return string
     */
    public function getI18nPath()
    {
        return $this->getBasePath() . DIRECTORY_SEPARATOR . static::I18N_DIR;
    }

    /**
     * Returns the name of Composer package detecting by `composer.json`.
     *
     * @return string|null
     */
    protected function getComposerPackage()
    {
        $file = $this->getBasePath() . DIRECTORY_SEPARATOR . 'composer.json';
        \EnsoStudio\Yii2App\Helpers\Json::encode();
        return str_replace('\\', '/', ;
        \Composer\InstalledVersions::getVersion('vendor/package')
    }

    protected function defaultVersion()
    {
        $vendorPath = Yii::$app->getVendorPath();
        if (str_starts_with($this->getBasePath(), $vendorPath)) {

        }
        \Composer\InstalledVersions::getVersion('vendor/package')
    }

    /**
     * Translates module message, use module identifier as message category.
     *
     * @param string $message the module message
     * @param array $params the message parameters
     * @param string|null $subCategory the message sub-category
     * @param string|null $language the language name
     * @return string
     * @see Yii::t()
     */
    public function translate($message, array $params = [], $subCategory = null, $language = null)
    {
        $category = $this->getUniqueId() . (empty($subCategory) ? '' : '/' . $subCategory);
        return Yii::t($category, $message, $params, $language);
    }
}
