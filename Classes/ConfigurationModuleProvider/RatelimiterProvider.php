<?php

declare(strict_types=1);

/*
 * This file is part of the "t3_ratelimiter" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\T3Ratelimiter\ConfigurationModuleProvider;

use Ssch\T3Ratelimiter\DependencyInjection\ConfigurationCollector;
use TYPO3\CMS\Lowlevel\ConfigurationModuleProvider\AbstractProvider;

final class RatelimiterProvider extends AbstractProvider
{
    private ConfigurationCollector $configurationCollector;

    public function __construct(ConfigurationCollector $configurationCollector)
    {
        $this->configurationCollector = $configurationCollector;
    }

    public function getConfiguration(): array
    {
        return $this->configurationCollector->collect();
    }
}
