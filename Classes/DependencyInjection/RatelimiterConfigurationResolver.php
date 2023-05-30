<?php

declare(strict_types=1);

/*
 * This file is part of the "t3_ratelimiter" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\T3Ratelimiter\DependencyInjection;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RatelimiterConfigurationResolver
{
    public function resolve(array $configuration): array
    {
        $resolver = new OptionsResolver();
        $this->configureDefaultOptions($resolver);

        $limitersConfiguration = [
            'limiters' => $configuration,
        ];
        $resolvedConfiguration = $resolver->resolve($limitersConfiguration);

        $this->validatePolicyConfiguration($resolvedConfiguration);

        return $resolvedConfiguration;
    }

    private function configureDefaultOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('limiters', function (OptionsResolver $limitersResolver) {
            $limitersResolver
                ->setPrototype(true);

            $limitersResolver
                ->setDefault('lock_factory', null)
                ->setInfo(
                    'lock_factory',
                    'The service ID of the lock factory used by this limiter (or null to disable locking)'
                );

            $limitersResolver
                ->setDefault('cache_pool', 'cache.rate_limiter')
                ->setInfo('cache_pool', 'The cache pool to use for storing the current limiter state');

            $limitersResolver
                ->setDefault('storage_service', null)
                ->setInfo(
                    'storage_service',
                    'The service ID of a custom storage implementation, this precedes any configured "cache_pool"'
                );

            $limitersResolver
                ->setDefault('policy', null)
                ->setInfo('policy', 'The algorithm to be used by this limiter');

            $limitersResolver->isRequired('policy');
            $limitersResolver->setAllowedValues(
                'policy',
                ['fixed_window', 'token_bucket', 'sliding_window', 'no_limit']
            );

            $limitersResolver->setDefined('limit')
                ->setAllowedTypes('limit', 'int')
                ->setInfo('limit', 'The maximum allowed hits in a fixed interval or burst');

            $limitersResolver->setDefined('interval')
                ->setInfo(
                    'interval',
                    'Configures the fixed interval if "policy" is set to "fixed_window" or "sliding_window". The value must be a number followed by "second", "minute", "hour", "day", "week" or "month" (or their plural equivalent).'
                );

            $limitersResolver->setDefault('rate', function (OptionsResolver $rateResolver) {
                $rateResolver
                    ->setDefined('interval')
                    ->setDefault('amount', 1);

                $rateResolver->setInfo(
                    'interval',
                    'Configures the rate interval. The value must be a number followed by "second", "minute", "hour", "day", "week" or "month" (or their plural equivalent).'
                );
                $rateResolver->setInfo('amount', 'Amount of tokens to add each interval');
            });
        });
    }

    private function validatePolicyConfiguration(array $resolvedConfiguration): void
    {
        foreach ($resolvedConfiguration['limiters'] as $limiter) {
            if ($limiter['policy'] === 'no_limit') {
                continue;
            }

            if (! isset($limiter['limit'])) {
                throw new InvalidOptionsException(
                    sprintf(
                        'A limit must be provided when using a policy different than "no_limit". Policy "%s" given.',
                        $limiter['policy']
                    )
                );
            }
        }
    }
}
