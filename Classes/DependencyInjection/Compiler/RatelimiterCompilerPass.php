<?php

declare(strict_types=1);

/*
 * This file is part of the "t3_ratelimiter" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\T3Ratelimiter\DependencyInjection\Compiler;

use Ssch\T3Ratelimiter\DependencyInjection\ConfigurationCollector;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\CacheStorage;

final class RatelimiterCompilerPass implements CompilerPassInterface
{
    private ConfigurationCollector $configurationCollector;

    public function __construct(ConfigurationCollector $configurationCollector)
    {
        $this->configurationCollector = $configurationCollector;
    }

    public function process(ContainerBuilder $container): void
    {
        $config = $this->configurationCollector->collect();

        foreach ($config['limiters'] as $name => $limiterConfig) {
            // default configuration (when used by other DI extensions)
            $limiterConfig += [
                'lock_factory' => 'lock.factory',
                'cache_pool' => 'cache.rate_limiter',
            ];

            $limiter = $container->setDefinition($limiterId = 'limiter.' . $name, new ChildDefinition('limiter'));

            if ($limiterConfig['lock_factory'] !== null) {
                if (! interface_exists(LockInterface::class)) {
                    throw new LogicException(sprintf(
                        'Rate limiter "%s" requires the Lock component to be installed. Try running "composer require symfony/lock".',
                        $name
                    ));
                }

                $limiter->replaceArgument(2, new Reference($limiterConfig['lock_factory']));
            }
            unset($limiterConfig['lock_factory']);

            $storageId = $limiterConfig['storage_service'] ?? null;

            if ($storageId === null) {
                $container->register($storageId = 'limiter.storage.' . $name, CacheStorage::class)->addArgument(
                    new Reference($limiterConfig['cache_pool'])
                );
            }

            $limiter->replaceArgument(1, new Reference($storageId));
            unset($limiterConfig['storage_service'], $limiterConfig['cache_pool']);

            $limiterConfig['id'] = $name;
            $limiter->replaceArgument(0, $limiterConfig);

            $container->registerAliasForArgument($limiterId, RateLimiterFactory::class, $name . '.limiter');
        }
    }
}
