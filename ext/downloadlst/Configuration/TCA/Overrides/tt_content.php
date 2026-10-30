<?php
defined('TYPO3') || die();


$tmp_downloadlst_columns = [

    'new' => [
        'exclude' => true,
        'label' => 'LLL:EXT:downloadlst/Resources/Private/Language/locallang_db.xlf:tx_downloadlst_domain_model_ttcontent.new',
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
    'for_all_language' => [
        'exclude' => true,
        'label' => 'LLL:EXT:downloadlst/Resources/Private/Language/locallang_db.xlf:tx_downloadlst_domain_model_ttcontent.for_all_language',
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

// Add new fields to tt_content:
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content',$tmp_downloadlst_columns);

// Make fields visible in the TCEforms:
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
  'tt_content', // Table name
  'new,for_all_language', // Field list to add
  '', // List of specific types to add the field list to. (If empty, all type entries are affected)
  'after:subtitle' // Insert fields before (default) or after one, or replace a field
);
  
// Add the new palette:
$GLOBALS['TCA']['tt_content']['palettes']['new'] = array(
  'showitem' => 'new'
);

$_EXTKEY = 'downloadlst';
$pluginSignature = str_replace('_','',$_EXTKEY) . '_' . 'downloadlst';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/Flexforms/Flexform.xml');