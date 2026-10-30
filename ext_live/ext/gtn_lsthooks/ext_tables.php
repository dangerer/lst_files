<?php
if (!defined('TYPO3')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'GtnLsthooks',
	'Ajax',
	'Ajax gtnLsthooks'
);

//$pluginSignature = str_replace('_','','gtn_lsthooks') . '_ajax';
//$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
//\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:gtn_lsthooks/Configuration/FlexForms/flexform_ajax.xml');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('gtn_lsthooks', 'Configuration/TypoScript', 'gtn Lst hooks');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('international_ran_requests');



