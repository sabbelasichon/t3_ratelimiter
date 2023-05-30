<?php

declare(strict_types=1);

/*
 * This file is part of the "t3_ratelimiter" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\T3Ratelimiter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Ssch\T3Ratelimiter\DependencyInjection\RatelimiterConfigurationResolver;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

final class RatelimiterConfigurationResolverTest extends TestCase
{
    private RatelimiterConfigurationResolver $subject;

    protected function setUp(): void
    {
        $this->subject = new RatelimiterConfigurationResolver();
    }

    public function testThatAnExceptionIsThrownWhenNoPolicyIsDefined(): void
    {
        // Arrange
        $configuration = [
            'a_limiter' => [],
        ];

        // Assert
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage(
            'The option "policy" with value null is invalid. Accepted values are: "fixed_window", "token_bucket", "sliding_window", "no_limit"'
        );

        // Act
        $this->subject->resolve($configuration);
    }

    public function testThatAnExceptionIsThrownWhenPolicyOtherThanNoLimitHasNotLimitDefined(): void
    {
        // Arrange
        $configuration = [
            'a_limiter' => [
                'policy' => 'sliding_window',
            ],
        ];

        // Assert
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage(
            'A limit must be provided when using a policy different than "no_limit". Policy "sliding_window" given.'
        );

        // Act
        $this->subject->resolve($configuration);
    }

    public function testThatDefaultConfigurationIsApplied(): void
    {
        // Arrange
        $configuration = [
            'a_limiter' => [
                'policy' => 'no_limit',
            ],
        ];

        // Act
        $resolvedConfiguration = $this->subject->resolve($configuration);

        // Assert
        self::assertSame([
            'limiters' => [
                'a_limiter' => [
                    'lock_factory' => null,
                    'cache_pool' => 'cache.rate_limiter',
                    'storage_service' => null,
                    'policy' => 'no_limit',
                    'rate' => [
                        'amount' => 1,
                    ],
                ],
            ],
        ], $resolvedConfiguration);
    }
}
