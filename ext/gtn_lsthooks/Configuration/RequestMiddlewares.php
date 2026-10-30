<?php

declare(strict_types=1);

return [
    'frontend' => [
        'gtn/lsthooks-registration-honeypot' => [
            'target' => \Gtn\GtnLsthooks\Middleware\RegistrationHoneypotMiddleware::class,
            'after' => [
                'typo3/cms-frontend/site',
            ],
            'before' => [
                'typo3/cms-frontend/page-resolver',
            ],
        ],
    ],
];