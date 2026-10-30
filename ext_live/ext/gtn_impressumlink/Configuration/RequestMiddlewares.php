<?php

use Gtn\GtnImpressumlink\Middleware\ContentReplaceMiddleware;

return [
    'frontend' => [
        'in2code/femanager/clean-user-group' => [
            'target' => ContentReplaceMiddleware::class,
            'before' => [
                'typo3/cms-frontend/output-compression',
            ],
            'after' => [
                'typo3/cms-frontend/content-length-headers',
            ],
        ],
    ],
];