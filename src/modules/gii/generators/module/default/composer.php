<?php
/**
 * @var yii\web\View $this
 * @var app\modules\gii\generators\module\ModuleGenerator $generator
 */
?>
{
    "name": "<?= $generator->composerPackage ?>",
    "type": "yii2-extension",
    "description": "Application module",
    "keywords": ["yii2", "module"],
    "license": "BSD-3-Clause",
    "require": {
        "php": "^7.3 | ^8.0",
        "yiisoft/yii2": "^2.0.44"
    },
    "require-dev": {
        "roave/security-advisories": "dev-latest"
    },
    "autoload": {
        "psr-4": {
            "<?= $generator->moduleNamespace ?>\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "<?= $generator->moduleNamespace ?>\\": "tests/"
        }
    },
<?php if (is_subclass_of($generator->baseClass, yii\base\BootstrapInterface::class)) : ?>
    "extra": {
        "bootstrap": "<?= $generator->moduleClass ?>"
    },
<?php endif; ?>
    "repositories": [
        {
            "type": "composer",
            "url": "https://asset-packagist.org"
        }
    ]
}
