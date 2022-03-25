<?php
/**
 * @var yii\web\View $this
 * @var app\modules\gii\generators\module\Generator $generator
 */
?>
{
    "name": "<?= $generator->composerPackage ?>",
    "type": "yii2-module",
    "description": "",
    "keywords": ["yii2", "module"],
    "license": "BSD-3-Clause",
    "require": {
        "yiisoft/yii2": "^2.0",
        "ensostudio/yii2-common": "^1.0"
    }
    "require-dev": {
        "phpunit/phpunit": ">=7.0",
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
    "extra": {
        "module-config": {
            "class": "<?= $generator->moduleClass ?>",
            "id": "<?= $generator->moduleId ?>",
            "basePath": "@vendor/<?= $generator->composerPackage ?>",
            "controllerNamespace": "<?= $generator->getControllerNamespace() ?>",
            "params": {},
            "aliases": {
                "@<?= $generator->composerPackage ?>": "@vendor/<?= $generator->composerPackage ?>",
                "@<?= $generator->composerPackage ?>/controllers": "@vendor/<?= $generator->composerPackage ?>/src/controllers"
            }
        }
    },
    "repositories": [
        {
            "type": "composer",
            "url": "https://asset-packagist.org"
        }
    ]
}