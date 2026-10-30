<?php
defined('TYPO3') or die();

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

call_user_func(
    function($extKey)
    {
    	$extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get($extKey);
			if (isset($extensionConfiguration['addressTable']) && $extensionConfiguration['addressTable'] === 'tt_address') {
				//Setting up country, country subdivision, preferred language in tt_address table
				//Adjusting some maximum lengths to the values as corresponding fields in fe_users as set by extension sr_feuser_registe
				if (isset($extensionConfiguration['imageFolder']) && $extensionConfiguration['imageFolder'] != '') {
					$GLOBALS['TCA']['tt_address']['columns']['image']['config']['uploadfolder'] = $extensionConfiguration['imageFolder'];
				}
			
				ExtensionManagementUtility::addTCAcolumns('tt_address', array(
					'static_info_country' => array(
						'exclude' => 0,
						'label' => 'LLL:EXT:sr_email_subscribe/Resources/Private/Language/locallang_db.xlf:tt_address.static_info_country',
						'config' => array(
							'type' => 'input',
							'size' => '5',
							'max' => '3',
							'eval' => '',
							'default' => ''
						)
					),
					'zone' => array(
						'exclude' => 0,
						'label' => 'LLL:EXT:sr_email_subscribe/Resources/Private/Language/locallang_db.xlf:tt_address.zone',
						'config' => array(
							'type' => 'input',
							'size' => '20',
							'max' => '40',
							'eval' => 'trim',
							'default' => ''
						)
					),
					'language' => array(
						'exclude' => 0,
						'label' => 'LLL:EXT:sr_email_subscribe/Resources/Private/Language/locallang_db.xlf:tt_address.language',
						'config' => array(
							'type' => 'input',
							'size' => '4',
							'max' => '2',
							'eval' => '',
							'default' => ''
						)
					),
					'date_of_birth' => array(
						'exclude' => 0,
						'label' => 'LLL:EXT:sr_email_subscribe/Resources/Private/Language/locallang_db.xlf:tt_address.date_of_birth',
						'config' => array(
							'type' => 'input',
							'renderType' => 'datetime',
							'eval' => 'date',
							'default' => 0
						)
					),
					'comments' => array(
						'exclude' => 0,
						'label' => 'LLL:EXT:sr_email_subscribe/Resources/Private/Language/locallang_db.xlf:tt_address.comments',
						'config' => array(
							'type' => 'text',
							'rows' => '5',
							'cols' => '48'
						)
					)
				));
			
				ExtensionManagementUtility::addToAllTCAtypes('tt_address', 'comments');
				$GLOBALS['TCA']['tt_address']['palettes']['address']['showitem'] = preg_replace('/(^|,)\s*country\s*(,|$)/', '$1zone,static_info_country,country,language$2', $GLOBALS['TCA']['tt_address']['palettes']['address']['showitem']);
			
				// tt_address modified
				if (!ExtensionManagementUtility::isLoaded('direct_mail')) {
					ExtensionManagementUtility::addTCAcolumns('tt_address', array(
						'module_sys_dmail_html' => array(
							'label'=>'LLL:EXT:sr_email_subscribe/Resources/Private/Language/locallang_db.xlf:tt_address.module_sys_dmail_html',
							'exclude' => '0',
							'config'=> array(
								'type'=>'check'
								)
							)
					));
					ExtensionManagementUtility::addToAllTCATypes('tt_address', '--div--;Direct mail,module_sys_dmail_html');
				}
			}
	},
	'sr_email_subscribe'
);