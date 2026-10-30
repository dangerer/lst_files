<?php
declare(strict_types = 1);

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Registers the Static Info Tables Manager backend module, if enabled
 */
if (GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('static_info_tables')['enableManager'] ?? false) {
	return [
		'staticinfomanager' => [
			'parent' => 'tools',
			'position' => [],
			'access' => 'admin',
			'workspaces' => '*',
			'identifier' => 'staticinfomanager',
			'isStandalone' => false,
			'path' => '/module/tools/staticinfomanager',
			'iconIdentifier' => 'static-info-tables-icon',
			'labels' => 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_mod.xlf',
			'extensionName' => 'StaticInfoTables',
			'controllerActions' => [
				\SJBR\StaticInfoTables\Controller\ManagerController::class => [
					'information',
					'newLanguagePack',
					'createLanguagePack',
					'testForm',
					'testFormResult',
					'sqlDumpNonLocalizedData'
				]
			]
		]
	];
} else {
	return [];
}