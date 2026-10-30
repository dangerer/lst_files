<?php

defined('TYPO3') or die();

$GLOBALS['TCA']['fe_users']['palettes']['2']['showitem'] = str_replace('gender,--linebreak--,', '', $GLOBALS['TCA']['fe_users']['palettes']['2']['showitem']);

$tempColumns = array(
    'lst_newsletter' => array(
        'label' => 'LLL:EXT:gtn_lsthooks/Resources/Private/Language/locallang.xlf:fe_users.lst_newsletter',
        'config' => array(
            'type' => 'check',
            'default' => 1,
        ),
    ),
    'technical_newsletter' => array(
        'label' => 'LLL:EXT:gtn_lsthooks/Resources/Private/Language/locallang.xlf:fe_users.technical_newsletter',
        'config' => array(
            'type' => 'check',
            'default' => 1,
        ),
    ),
    'marketing_newsletter' => array(
        'label' => 'LLL:EXT:gtn_lsthooks/Resources/Private/Language/locallang.xlf:fe_users.marketing_newsletter',
        'config' => array(
            'type' => 'check',
            'default' => 1,
        ),
    ),
    'fe_language' => [
        'exclude' => true,
        'label' => 'FE language',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                ['LLL:EXT:gtn_lsthooks/Resources/Private/Language/fileadmin/locallang.xlf:fe_user.fe_language.item0', 0],
                ['LLL:EXT:gtn_lsthooks/Resources/Private/Language/fileadmin/locallang.xlf:fe_user.fe_language.item1', 1]
            ],
            'default' => 0
        ]
    ],
    'lst_region' => [
        'exclude' => true,
        'label' => 'Region',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                ['— bitte wählen —', 0],
            ],
            'foreign_table' => 'sys_category',
            'foreign_table_where' => '
                AND sys_category.parent = 8
                AND sys_category.sys_language_uid IN (-1,0)
                AND sys_category.deleted = 0
                AND sys_category.hidden = 0
                ORDER BY sys_category.title
            ',
            'default' => 0,
            'size' => 1,
            'maxitems' => 1,
        ],
    ],
    'customer_number' => array(
        'label' => 'Kundennummer',
        'config' => array(
            'type' => 'input',
            'size' => '30',
            'eval' => 'trim',
        ),
    ),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'fe_users',
    $tempColumns,
    true
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'fe_users',
    'lst_newsletter, technical_newsletter, marketing_newsletter, fe_language, customer_number, lst_region'
);
