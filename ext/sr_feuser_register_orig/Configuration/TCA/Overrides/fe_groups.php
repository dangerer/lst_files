<?php
defined('TYPO3') or die();

call_user_func(
    function($extKey)
    {
		/**
		 * Enabling localization of frontend groups
		 */
		$GLOBALS['TCA']['fe_groups']['ctrl']['origUid'] = 't3_origuid';
		$GLOBALS['TCA']['fe_groups']['ctrl']['languageField'] = 'sys_language_uid';
		$GLOBALS['TCA']['fe_groups']['ctrl']['transOrigPointerField'] = 'l10n_parent';
		$GLOBALS['TCA']['fe_groups']['ctrl']['transOrigDiffSourceField'] = 'l10n_diffsource';
		
		$GLOBALS['TCA']['fe_groups']['columns']['subgroup']['l10n_mode'] = 'exclude';
		$GLOBALS['TCA']['fe_groups']['columns']['TSconfig']['l10n_mode'] = 'exclude';
		$GLOBALS['TCA']['fe_groups']['columns']['lockToDomain']['l10n_mode'] = 'exclude';
		$GLOBALS['TCA']['fe_groups']['columns']['lockToDomain']['config']['type'] = 'none';
		
		$GLOBALS['TCA']['fe_groups']['columns']['subgroup']['config']['foreign_table_where'] = ' AND fe_groups.sys_language_uid IN (-1,0) AND NOT(fe_groups.uid = ###THIS_UID###) AND fe_groups.hidden=0 ORDER BY fe_groups.title';
		
		$addColumnArray = [
			'sys_language_uid' => [
				'exclude' => true,
				'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
				'config' => [
					'type' => 'language',
					'renderType' => 'selectSingle',
					'foreign_table' => 'sys_language',
					'foreign_table_where' => 'ORDER BY sys_language.title',
					'items' => [
						['LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages', -1],
						['LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.default_value', 0]
					],
					'default' => 0,
					'fieldWizard' => [
						'selectIcons' => [
							'disabled' => false,
						],
					],
				]
			],
			'l10n_parent' => [
				'displayCond' => 'FIELD:sys_language_uid:>:0',
				'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
				'config' => [
					'type' => 'select',
					'renderType' => 'selectSingle',
					'items' => [
						[
							'label' => '',
							'value' => 0
						]
					],
					'foreign_table' => 'sys_category',
					'foreign_table_where' => 'AND sys_category.uid=###REC_FIELD_l10n_parent### AND sys_category.sys_language_uid IN (-1,0)',
					'default' => 0
				]
			],
			'l10n_diffsource' => [
				'config' => [
					'type' => 'passthrough',
					'default' => ''
				]
			]
		];
		$typo3Version = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class);
		$typo3Branch = $typo3Version->getBranch();
		if (version_compare($typo3Branch, '11.2', '<')) {
            $addColumnArray['sys_language_uid']['config']['type'] = 'select';
        }
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_groups', $addColumnArray);
	},
	'sr_feuser_register'
);