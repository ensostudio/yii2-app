<?php

namespace app\base;

use Yii;
use yii\base\BootstrapInterface;
use yii\i18n\PhpMessageSource;
use yii\web\GroupUrlRule;

use function array_filter;
use function array_key_exists;
use function is_array;
use function is_dir;
use function is_subclass_of;

/**
 * Base application module.
 *
 * @inheritDoc
 * @property GroupUrlRule|array $urlRules The URL rules of module and sub-modules to web controllers
 */
abstract class Module extends \yii\base\Module implements BootstrapInterface
{
    use ModuleTrait;

    /**
     * @var GroupUrlRule The URL rules of module and sub-modules to web controllers
     */
    private GroupUrlRule $urlRules;

    /**
     * Sets the URL rules of module and sub-modules to web controllers.
     *
     * @param GroupUrlRule|array $group the group of URL rules
     * @return void
     * @see \yii\web\UrlManager::$rules
     */
    public function setUrlRules($group)
    {
        if (!$group instanceof GroupUrlRule) {
            if (isset($group['class'])) {
                $group = Yii::createObject($group);
            } else {
                $group = new GroupUrlRule(['prefix' => $this->getUniqueId(), 'rules' => $group]);
            }
        }
        if (!isset($group->routePrefix)) {
            $group->routePrefix = $this->getUniqueId();
        }

        // Add sub-module's rules
        foreach ($this->getModules() as $id => $module) {
            // Skip configured module
            if (array_key_exists($id, $group->rules)) {
                continue;
            }
            if (is_array($module) && is_subclass_of($module['class'], __CLASS__)) {
                $module = $this->getModule($id);
            }
            if ($module instanceof self) {
                $group->rules[$id] = $module->getUrlRules();
            }
        }
        $group->rules = array_filter($group->rules);

        $this->urlRules = $group;
    }

    /**
     * Returns the URL rules for actions of module and sub-modules.
     *
     * @return GroupUrlRule
     */
    public function getUrlRules(): GroupUrlRule
    {
        if (!isset($this->urlRules)) {
            $this->setUrlRules([]);
        }

        return $this->urlRules;
    }

    /**
     * @inheritDoc
     *
     * This method adds:
     * - the path alias
     * - I18N translation source
     * - URL rules
     */
    public function bootstrap($app)
    {
        foreach ($this->getModules() as $id => $module) {
            if (is_array($module) && is_subclass_of($module['class'], BootstrapInterface::class)) {
                $module = $this->getModule($id);
            }
            if ($module instanceof BootstrapInterface) {
                $module->bootstrap($app);
            }
        }

        // Adds alias for module directory
        $this->setAliases(['@modules/' . $this->getUniqueId() => $this->getBasePath()]);
        // Adds module translations
        if (is_dir($this->getI18nPath())) {
            $app->getI18n()->translations[$this->getI18nCategory()] = [
                'class' => PhpMessageSource::class,
                'basePath' => $this->getI18nPath(),
            ];
        }
        // Adds URL rules
        $app->urlManager->addRules([$this->getUrlRules()]);
    }
}
