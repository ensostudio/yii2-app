<?php

namespace app\base;

use yii\base\BootstrapInterface;
use yii\base\Module;
use yii\i18n\PhpMessageSource;

/**
 * Base application module.
 *
 * @inheritDoc
 */
abstract class BaseModule extends Module implements BootstrapInterface
{
    use ModuleTrait;

    /**
     * @inheritDoc
     */
    public function bootstrap($app)
    {
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
