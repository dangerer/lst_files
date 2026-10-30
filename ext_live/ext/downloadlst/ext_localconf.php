<?php
defined('TYPO3') || die('Access denied.');


\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Downloadlst',
	'Downloadlst',
	[
		\Hafs\Downloadlst\Controller\ExtendpagesController::class => 'list'
	],
	// non-cacheable actions
	[
		\Hafs\Downloadlst\Controller\ExtendpagesController::class => 'list'
	]
);

// wizards
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
	'mod {
		wizards.newContentElement.wizardItems.plugins {
			elements {
				downloadlst {
					iconIdentifier = downloadlst-plugin-downloadlst
					title = LLL:EXT:downloadlst/Resources/Private/Language/locallang_db.xlf:tx_downloadlst_downloadlst.name
					description = LLL:EXT:downloadlst/Resources/Private/Language/locallang_db.xlf:tx_downloadlst_downloadlst.description
					tt_content_defValues {
						CType = list
						list_type = downloadlst_downloadlst
					}
				}
			}
			show = *
		}
   }'
);
$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);

$iconRegistry->registerIcon(
	'downloadlst-plugin-downloadlst',
	\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
	['source' => 'EXT:downloadlst/Resources/Public/Icons/user_plugin_downloadlst.svg']
);


$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\Hafs\Downloadlst\Task\UpdateFlag::class] = array(
	'extension' => 'downloadlst',
	'title' => 'downloadlist update new flag',
	'description' => 'downloadlist update new flag'
//	'additionalFields' => 'tx_gtncachedevents_exportpidsettings'
);
