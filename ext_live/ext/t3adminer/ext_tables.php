<?php
defined('TYPO3') or die();

call_user_func(static function() {
    // registration for v11
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModule(
        'tools',
        'txt3adminerM1',
        '',
        null,
        [
            'routeTarget' => Jigal\T3adminer\Controller\AdminerController::class . '::main',
            'access' => 'systemMaintainer',
            'name' => 'tools_txt3adminerM1',
            'labels' => 'LLL:EXT:t3adminer/Resources/Private/Language/locallang_mod.xlf',
            'iconIdentifier' => 'adminer-module',
        ]
    );
});
