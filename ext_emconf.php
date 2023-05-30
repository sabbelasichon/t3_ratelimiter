<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Symfony RateLimiter Wrapper',
    'description' => 'Symfony RateLimiter Wrapper',
    'category' => 'misc',
    'author' => 'Sebastian Schreiber',
    'author_email' => 'breakpoint@schreibersebastian.de',
    'state' => 'beta',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-12.9.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'Ssch\\T3Ratelimiter\\' => 'Classes',
        ],
    ],
    'autoload-dev' => [
        'psr-4' => [
            'Ssch\\T3Ratelimiter\\Tests\\' => 'Tests',
        ],
    ],
];
