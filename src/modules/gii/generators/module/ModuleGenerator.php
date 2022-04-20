<?php

namespace app\modules\gii\generators\module;

use app\AppModule;
use yii\base\Module;
use yii\gii\CodeFile;
use yii\gii\generators\module\Generator;
use yii\helpers\StringHelper;
use yii\i18n\DbMessageSource;
use yii\i18n\GettextMessageSource;
use yii\i18n\PhpMessageSource;

use function array_keys;
use function array_merge;
use function is_subclass_of;
use function ucfirst;

/**
 * @inheritDoc
 * @property-read string[] $messageSources
 */
class ModuleGenerator extends Generator
{
    /**
     * @inheritDoc
     */
    public $messageCategory = 'app/modules';
    /**
     * @var string The parent of `Module` class
     * @psalm-var class-string
     */
    public string $baseClass = AppModule::class;
    /**
     * @var string The base namespace of modules
     */
    public string $moduleNamespace = 'app\modules';
    /**
     * @var string The name of Composer package of module in format `vendor/package`
     */
    public string $composerPackage;
    /**
     * @var string The translation source
     */
    public string $messageSource = PhpMessageSource::class;

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                [['baseClass', 'moduleNamespace'], 'required'],
                [['baseClass', 'moduleNamespace'], 'trim', 'chars' => '\ '],
                [['baseClass', 'moduleNamespace', 'messageCategory', 'composerPackage'], 'string'],
                [['enableI18N'], 'boolean'],
                [['baseClass'], 'validateBaseClass'],
                [
                    ['composerPackage'],
                    'match',
                    'pattern' => '/^\w[-\w]+\/\w[-\w]+$/',
                    'message' => 'The Composer package name not in format "vendor/package"',
                    'skipOnEmpty' => true,
                ],
                [
                    ['messageCategory'],
                    'match',
                    'pattern' => '/^[-\w\/]+$/',
                    'message' => 'The message category must contain only dash, slash, alphabet and digit chars',
                    'when' => static function (self $generator) {
                        return $generator->enableI18N;
                    },
                ],
                [['messageSource'], 'in', 'range' => array_keys($this->getMessageSources())]
            ]
        );
    }

    /**
     * Validates [[baseClass]] to make sure it is a fully qualified class, class must be [[yii\base\Module]] or
     * inherite it.
     *
     * @return void
     */
    public function validateBaseClass()
    {
        if (!is_subclass_of($this->baseClass, Module::class)) {
            $this->addError('baseClass', 'Base class not instance of \yii\base\Module');
        }
    }

    /**
     * @inheritDoc
     */
    public function stickyAttributes(): array
    {
        return array_merge(
            parent::stickyAttributes(),
            ['baseClass', 'enableI18N', 'messageSource']
        );
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels(): array
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'baseClass' => 'Base class',
                'moduleNamespace' => 'Module namespace',
                'composerPackage' => 'Composer package',
                'messageCategory' => 'Message category',
                'messageSource' => 'Message source',
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function hints(): array
    {
        return array_merge(
            parent::hints(),
            [
                'baseClass' => 'The parent class of the new Module class, a fully qualified namespaced class name.',
                'moduleNamespace' => 'The base namespace of module classes. Example: <code>app\modules\id</code>.',
                'composerPackage' => 'The name of Composer package in format <code>vendor/package</code>, using to'
                    . ' generate <code>composer.json</code>.',
                'enableI18N' => 'Indicates whether the generator should generate strings using <code>Yii::t()</code>.',
                'messageCategory' => 'The category used by <code>Yii::t()</code> in case you enable I18N.',
                'messageSource' => 'The message source stores translations.',
            ]
        );
    }

    /**
     * Returns classes of I18N message sources.
     *
     * @return string[]
     */
    public function getMessageSources(): array
    {
        return [
            PhpMessageSource::class => 'PHP files',
            GettextMessageSource::class => 'Gettext files',
            DbMessageSource::class => 'Database',
        ];
    }

    /**
     * Action to generate class name of module.
     *
     * @return string
     */
    public function actionGenerateClassName(): string
    {
        return $this->moduleNamespace . '\\' . ucfirst($this->moduleID) . 'Module';
    }

    /**
     * @inheritDoc
     */
    public function generate(): array
    {
        $modulePath = $this->getModulePath();
        $files = [];
        $files[] = new CodeFile(
            $modulePath . '/src/' . StringHelper::basename($this->moduleClass) . '.php',
            $this->render('module.php')
        );
        $files[] = new CodeFile(
            $modulePath . '/src/controllers/DefaultController.php',
            $this->render('controller.php')
        );
        $files[] = new CodeFile(
            $modulePath . '/views/default/index.php',
            $this->render('view.php')
        );
        if ($this->composerPackage) {
            $files[] = new CodeFile(
                $modulePath . '/composer.json',
                $this->render('composer.php')
            );
        }

        return $files;
    }
}
