<?php

declare(strict_types=1);

/*
 * This file is part of the "t3_ratelimiter" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\T3Ratelimiter\DependencyInjection;

use TYPO3\CMS\Core\Package\PackageManager;

final class ConfigurationCollector
{
    private PackageManager $packageManager;

    private RatelimiterConfigurationResolver $configurationResolver;

    public function __construct(PackageManager $packageManager, RatelimiterConfigurationResolver $configurationResolver)
    {
        $this->packageManager = $packageManager;
        $this->configurationResolver = $configurationResolver;
    }

    public function collect(): array
    {
        $config = new \ArrayObject();
        foreach ($this->packageManager->getAvailablePackages() as $package) {
            $configurationFile = $package->getPackagePath() . 'Configuration/Ratelimiter.php';
            if (file_exists($configurationFile)) {
                $ratelimiterInPackage = require $configurationFile;
                if (is_array($ratelimiterInPackage)) {
                    $config->exchangeArray(array_replace_recursive($config->getArrayCopy(), $ratelimiterInPackage));
                }
            }
        }

        return $this->configurationResolver->resolve($config->getArrayCopy());
    }
}
