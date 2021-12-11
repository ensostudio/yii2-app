<?php

namespace app;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;
use Composer\InstalledVersions;

/**
 * Composer event handlers.
 *
 * @link https://getcomposer.org/doc/articles/scripts.md
 */
class ComposerEventHandler
{
    /**
     * The package type of Yii2 extensions
     */
    const EXTENSION_TYPE = 'yii2-extension';
    /**
     * The key of package extra array with configuration of application module (Yii2 extension)
     */
    const MODULE_EXTRA_KEY = 'yii2-module';

    /**
     * "post-package-install" event handler, occurs after a package has been installed.
     *
     * @param PackageEvent $event The current event
     * @return void
     */
    public static function packageInstall(PackageEvent $event)
    {
        /** @var \Composer\DependencyResolver\Operation\InstallOperation $operation */
        $operation = $event->getOperation();
        $package = $operation->getPackage();
        if ($package->getType() === self::EXTENSION_TYPE) {
            $extra = $package->getExtra();
        }
    }

    /**
     * "post-package-uninstall" event handler, occurs after a package has been uninstalled.
     *
     * @param PackageEvent $event The current event
     * @return void
     */
    public static function packageUninstall(PackageEvent $event)
    {
        /** @var \Composer\DependencyResolver\Operation\UninstallOperation $operation */
        $operation = $event->getOperation();
        $package = $operation->getPackage();
    }

    /**
     * "post-package-update" event handler, occurs after a package has been updated.
     *
     * @param PackageEvent $event The current event
     * @return void
     */
    public static function packageUpdate(PackageEvent $event)
    {
        /** @var \Composer\DependencyResolver\Operation\UpdateOperation $operation */
        $operation = $event->getOperation();
        $initialPackage = $operation->getInitialPackage();
        $targetPackage = $operation->getTargetPackage();
        echo $event->getName() . PHP_EOL;
    }

    /**
     * "post-create-project-cmd" event handler,  occurs after the `create-project` command has been executed.
     *
     * @param Event $event The current event
     * @return void
     */
    public static function createProject(Event $event)
    {
        $composer = $event->getComposer();
        $config = $composer->getConfig();
        $vendorDir = $config->get('vendor-dir') . '/';
        $repositoryManager = $composer->getRepositoryManager();
        $packages = InstalledVersions::getInstalledPackagesByType(self::EXTENSION_TYPE);
        foreach ($packages as $package) {
            $version = InstalledVersions::getPrettyVersion($package);
            $extra = $repositoryManager->findPackage($package, $version)->getExtra();
            if (isset($extra[self::MODULE_EXTRA_KEY])) {
                $config = $extra[self::MODULE_EXTRA_KEY];
                $config['version'] = $version;
                if (!isset($config['id'])) {
                    $config['id'] = $package;
                }
                if (!isset($config['cont'])) {
                    $config['id'] = $package;
                }
            }
        }
    }
}
