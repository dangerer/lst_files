<?php
defined('TYPO3') || die();

$tmp_downloadlst_columns = [

    'new' => [
        'exclude' => true,
        'label' => 'LLL:EXT:downloadlst/Resources/Private/Language/locallang_db.xlf:tx_downloadlst_domain_model_extendpages.new',
        'config' => [
            'type' => 'check',
/*            'items' => [
                '1' => [
                    '0' => 'LLL:EXT:lang/locallang_core.xlf:labels.enabled'
                ]
            ],*/
            'default' => 0,
        ]
    ],

];

// Add new fields to pages:
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages',$tmp_downloadlst_columns);
  
// Make fields visible in the TCEforms:
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
  'pages', // Table name
  'new', // Field list to add
  '', // List of specific types to add the field list to. (If empty, all type entries are affected)
  'after:subtitle' // Insert fields before (default) or after one, or replace a field
);
  
// Add the new palette:
$GLOBALS['TCA']['pages']['palettes']['new'] = array(
  'showitem' => 'new'
);