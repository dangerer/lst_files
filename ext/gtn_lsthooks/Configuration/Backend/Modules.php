<?php

declare(strict_types=1);

use Gtn\GtnLsthooks\Controller\CourseRegistrationsController;

return [
    'web_gtnlsthooks_courses' => [
        'parent' => 'web',
        'access' => 'admin',
        'workspaces' => 'live',
        'path' => '/module/web/schulungsanmeldungen',
        'icon' => 'EXT:gtn_lsthooks/Resources/Public/Icons/Extension.svg',
        'labels' => [
            'title' => 'Schulungsanmeldungen',
            'shortDescription' => 'Teilnehmer je Schulung und Termin',
            'description' => 'Gespeicherte Anmeldungen der Powermail-Formulare 20 und 21.',
        ],
        'inheritNavigationComponentFromMainModule' => false,
        'routes' => [
            '_default' => [
                'target' => CourseRegistrationsController::class . '::indexAction',
                'methods' => ['GET'],
            ],
        ],
    ],
];
