<?php

declare(strict_types=1);

/*
 * This file is part of the "t3_ratelimiter" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Psr\Cache\CacheItemPoolInterface;
use Ssch\Cache\Factory\Psr6Factory;
use Ssch\T3Ratelimiter\DependencyInjection\Compiler\RatelimiterCompilerPass;
use Ssch\T3Ratelimiter\DependencyInjection\ConfigurationCollector;
use Ssch\T3Ratelimiter\DependencyInjection\PackageManagerFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use function Symfony\Component\DependencyInjection\Loader\Configurator\abstract_arg;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use Symfony\Component\RateLimiter\RateLimiterFactory;

return static function (ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void {
    $services = $containerConfigurator->services();

    $services->set('cache.rate_limiter', CacheItemPoolInterface::class)
        ->factory([service(Psr6Factory::class), 'create'])
        ->args(['typo3_psr_cache_adapter_test']);

    $services->set('limiter', RateLimiterFactory::class)
        ->abstract()
        ->args([abstract_arg('config'), abstract_arg('storage'), null]);

    $containerBuilder->addCompilerPass(
        new RatelimiterCompilerPass(new ConfigurationCollector(PackageManagerFactory::createPackageManager()))
    );
};