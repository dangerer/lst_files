<?php
defined('TYPO3') || die();


$tmp_downloadlst_columns = [

    'feuserpage' => [
        'exclude' => true,
        'label' => 'LLL:EXT:downloadlst/Resources/Private/Language/locallang_db.xlf:tx_downloadlst_domain_model_syscategory.feuserpage',
        'config' => [
            'type' => 'group',
            'internal_type' => 'db',
            'allowed' => 'pages',
            'size' => '1',
            'minitems' => '0',
            'maxitems' => '1',
            'wizards' => [
                '_PADDING' => '1',
                '_VERTICAL' => '1',
                'suggest' => [
                    'type' => 'suggest',
                    'default' => [
                        'searchWholePhrase' => '1'
                    ]
                ]
            ],
        ]
    ],

];

// Add new fields to sys_category:
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_category',$tmp_downloadlst_columns);

// Make fields visible in the TCEforms:
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
  'sys_category', // Table name
  'feuserpage', // Field list to add
  '', // List of specific types to add the field list to. (If empty, all type entries are affected)
  '' // Insert fields before (default) or after one, or replace a field
);
  
// Add the new palette:
$GLOBALS['TCA']['sys_category']['palettes']['feuserpage'] = array(
  'showitem' => 'feuserpage'
);