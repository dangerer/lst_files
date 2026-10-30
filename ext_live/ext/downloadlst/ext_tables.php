<?php
defined('TYPO3') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Downloadlst',
            'Downloadlst',
            'Download List'
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('downloadlst', 'Configuration/TypoScript', 'Download list');

    }
);
