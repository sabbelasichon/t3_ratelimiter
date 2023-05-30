<?php

declare(strict_types=1);

/*
 * This file is part of the "t3_ratelimiter" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

return [
    'anonymous_api' => [
        'policy' => 'fixed_window',
        'limit' => 1,
        'interval' => '2 seconds',
    ],
    'authenticated_api' => [
        'policy' => 'fixed_window',
        'limit' => 4,
        'interval' => '2 seconds',
    ],
];
