<?php

declare(strict_types=1);

/*
 * This file is part of the "t3_ratelimiter" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\T3Ratelimiter\DependencyInjection;

use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class PackageManagerFactory
{
    private static ?PackageManager $packageManager = null;

    public static function createPackageManager(): PackageManager
    {
        if (self::$packageManager !== null) {
            return self::$packageManager;
        }

        $versionInformation = GeneralUtility::makeInstance(Typo3Version::class);
        if ($versionInformation->getMajorVersion() >= 11) {
            $coreCache = Bootstrap::createCache('core');
            $packageCache = Bootstrap::createPackageCache($coreCache);
            $packageManager = Bootstrap::createPackageManager(PackageManager::class, $packageCache);
        } else {
            $coreCache = Bootstrap::createCache('core');
            $packageManager = Bootstrap::createPackageManager(PackageManager::class, $coreCache);
        }

        self::$packageManager = $packageManager;

        return self::$packageManager;
    }
}
