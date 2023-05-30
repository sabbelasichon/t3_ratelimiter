<?php

declare(strict_types=1);

/*
 * This file is part of the "t3_ratelimiter" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\T3Ratelimiter\Tests\Functional;

use Ssch\T3Ratelimiter\Tests\Functional\Fixtures\Extensions\t3_ratelimiter_test\Classes\Service\RatelimiterService;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class RatelimiterTest extends FunctionalTestCase
{
    protected bool $initializeDatabase = false;

    protected array $testExtensionsToLoad = [
        'typo3conf/ext/typo3_psr_cache_adapter',
        'typo3conf/ext/t3_ratelimiter',
        'typo3conf/ext/t3_ratelimiter/Tests/Functional/Fixtures/Extensions/t3_ratelimiter_test',
    ];

    public function testThatAnExceptionIsThrownWhenLimitIsExceeded(): void
    {
        // Arrange
        $ratelimiterService = $this->get(RatelimiterService::class);

        // Assert
        $this->expectException(\UnexpectedValueException::class);

        // Act
        $ratelimiterService->checkAnonymous('test');
        sleep(1);
        $ratelimiterService->checkAnonymous('test');
    }

    public function testThatAnExceptionIsThrownWhenLimitIsExceededForAuthenticatedApiLimiter(): void
    {
        // Arrange
        $ratelimiterService = $this->get(RatelimiterService::class);

        // Assert
        $this->expectException(\UnexpectedValueException::class);

        // Act
        $ratelimiterService->checkAuthenticated('test');
        $ratelimiterService->checkAuthenticated('test');
        $ratelimiterService->checkAuthenticated('test');
        $ratelimiterService->checkAuthenticated('test');
        $ratelimiterService->checkAuthenticated('test');
    }
}
