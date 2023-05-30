<?php

declare(strict_types=1);

/*
 * This file is part of the "t3_ratelimiter" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\T3Ratelimiter\Tests\Functional\Fixtures\Extensions\t3_ratelimiter_test\Classes\Service;

use Symfony\Component\RateLimiter\Policy\Rate;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use TYPO3\CMS\Core\SingletonInterface;

final class RatelimiterService implements SingletonInterface
{
    // if you're using service autowiring, the variable name must be:
    // "rate limiter name" (in camelCase) + "Limiter" suffix
    private RateLimiterFactory $anonymousApiLimiter;

    private RateLimiterFactory $authenticatedApiLimiter;

    public function __construct(RateLimiterFactory $anonymousApiLimiter, RateLimiterFactory $authenticatedApiLimiter)
    {
        $this->anonymousApiLimiter = $anonymousApiLimiter;
        $this->authenticatedApiLimiter = $authenticatedApiLimiter;
    }

    public function checkAnonymous(string $key): void
    {
        $limiter = $this->anonymousApiLimiter->create($key);
        if ($limiter->consume()->isAccepted() === false) {
            throw new \UnexpectedValueException('Too many requests');
        }
    }

    public function checkAuthenticated(string $key): void
    {
        $limiter = $this->authenticatedApiLimiter->create($key);
        if ($limiter->consume()->isAccepted() === false) {
            throw new \UnexpectedValueException('Too many requests');
        }
    }
}
