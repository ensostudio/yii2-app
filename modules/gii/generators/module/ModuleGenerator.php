<?php

namespace app\gii\generators\module;

use yii\gii\CodeFile;
use yii\helpers\Html;
use Yii;
use yii\helpers\StringHelper;

/**
 * This generator will generate the skeleton code needed by a module.
 *
 * @property-read string $controllerNamespace The controller namespace of the module.
 * @property-read bool $modulePath The directory that contains the module class.
 * @property-read string $name The module name
 */
class ModuleGenerator extends \yii\gii\generators\module\Generator
{
    /**
     * @var string The class name of parent module/application.
     * @psalm-var class-string
     */
    public $parentModuleClass = \yii\web\Application::class;

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'Nested Module Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription(): string
    {
        return 'Generates nested module.';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                [['moduleID'], 'match', 'pattern' => '/^[\w\\-]+$/', 'message' => 'Only word characters and dashes are allowed.'],
                [['moduleClass'], 'validateModuleClass'],
                [['parentModuleClass'], 'validateParentModuleClass'],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'moduleID' => 'Module ID',
            'moduleClass' => 'Module Class',
            'parentModuleClass' => 'Parent module',
        ];
    }

    /**
     * @inheritdoc
     */
    public function hints(): array
    {
        return [
            'moduleID' => 'This refers to the ID of the module, e.g., <code>admin</code>.',
            'moduleClass' => 'This is the fully qualified class name of the module, e.g., <code>app\modules\admin\Module</code>.',
        ];
    }

    /**
     * @inheritdoc
     */
    public function successMessage(): string
    {
        if (Yii::$app->hasModule($this->moduleID)) {
            $link = Html::a('try it now', Yii::$app->getUrlManager()->createUrl($this->moduleID), ['target' => '_blank']);

            return "The module has been generated successfully. You may $link.";
        }

        $output = <<<EOD
<p>The module has been generated successfully.</p>
<p>To access the module, you need to add this to your application configuration:</p>
EOD;
        $code = <<<EOD
<?php
    ......
    'modules' => [
        '{$this->moduleID}' => [
            'class' => '{$this->moduleClass}',
        ],
    ],
    ......
EOD;

        return $output . '<pre>' . highlight_string($code, true) . '</pre>';
    }

    /**
     * @inheritdoc
     */
    public function generate(): array
    {
        $files = [];
        $modulePath = $this->getModulePath();
        $files[] = new CodeFile(
            $modulePath . '/' . StringHelper::basename($this->moduleClass) . '.php',
            $this->render('module.php')
        );
        $files[] = new CodeFile(
            $modulePath . '/controllers/DefaultController.php',
            $this->render('controller.php')
        );
        $files[] = new CodeFile(
            $modulePath . '/views/default/index.php',
            $this->render('view.php')
        );

        return $files;
    }

    /**
     * Validates [[parentModuleClass]] to make sure it's a class inherit `\yii\base\Module`.
     */
    public function validateParentModuleClass(): void
    {
        if (isset($this->parentModuleClass) && !is_a($this->parentModuleClass, \yii\base\Module::class, true)) {
            $this->addError('parentModuleClass', 'Module parent class must be instance of `\yii\base\Module`.');
        }
    }

    /**
     * Validates [[moduleClass]] to make sure it is a fully qualified class name.
     */
    public function validateModuleClass(): void
    {
        if (strpos($this->moduleClass, '\\') === false || Yii::getAlias('@' . str_replace('\\', '/', $this->moduleClass), false) === false) {
            $this->addError('moduleClass', 'Module class must be properly namespaced.');
        }
        if (empty($this->moduleClass) || substr_compare($this->moduleClass, '\\', -1, 1) === 0) {
            $this->addError('moduleClass', 'Module class name must not be empty. Please enter a fully qualified class name. e.g. "app\modules\admin\Module".');
        }
        if (preg_match('/^[\w\\\\]*$/', $this->moduleClass) === 0) {
            $this->addError('moduleClass', 'Only word characters and backslashes are allowed.');
        }
    }

    /**
     * @return string the directory that contains the module class
     */
    public function getModulePath(): string
    {
        return Yii::getAlias('@' . str_replace('\\', '/', substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\'))));
    }

    /**
     * @return string the controller namespace of the module.
     */
    public function getControllerNamespace(): string
    {
        return substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\')) . '\controllers';
    }
}
