<?php

return [
    't3adminer' => [
        'standalone' => true,
        'parent' => 'tools',
        'access' => 'systemMaintainer',
        'path' => '/module/t3adminer',
        'iconIdentifier' => 'adminer-module',
        'labels' => 'LLL:EXT:t3adminer/Resources/Private/Language/locallang_mod.xlf',
        'routes' => [
            '_default' => [
                'target' => \Jigal\T3adminer\Controller\AdminerController::class . '::main',
            ],
        ],
    ],
];
