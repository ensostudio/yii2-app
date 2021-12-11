<?php

namespace app\modules\gii\generators\module;

use yii\base\Module;
use yii\i18n\DbMessageSource;
use yii\i18n\GettextMessageSource;
use yii\i18n\PhpMessageSource;

/**
 * @inheritDoc
 */
class Generator extends \yii\gii\generators\module\Generator
{
    /**
     * @var string The parent of `Module` class
     * @psalm-var class-string
     */
    public $baseClass = Module::class;
    /**
     * @var string The base namespace of modules
     */
    public $moduleNamespace;
    /**
     * @var string The name of Composer package of module
     */
    public $composerPackage;
    /**
     * @inheritDoc
     */
    public $messageCategory;
    /**
     * @var string The name of Composer package of module
     */
    public $messageSource = PhpMessageSource::class;

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                [['baseClass', 'moduleNamespace'], 'required'],
                [['messageCategory', 'composerPackage'], 'string'],
                [['baseClass'], 'validateBaseClass'],
                [
                    ['composerPackage'],
                    'match',
                    'pattern' => '/^\w[-\w]+\/\w[-\w]+$/',
                    'message' => 'The Composer package name not in format "vendor/package"',
                    'skipOnEmpty' => true,
                ],
                [['enableI18N'], 'boolean'],
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
     * Validates [[baseClass]] to make sure it is a fully qualified class, class must be `\yii\base\Module` or
     * inherite it.
     *
     * @return void
     */
    public function validateBaseClass()
    {
        if (!$this->baseClass instanceof Module) {
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
        return trim($this->moduleNamespace, '\\') . '\Module';
    }
}
