<?php
defined('TYPO3') or die();

use SJBR\SrFeuserRegister\Captcha\Freecap;
use SJBR\SrFeuserRegister\Configuration\Reports\StatusProvider;
use SJBR\SrFeuserRegister\Hooks\FileUploadHooks;
use SJBR\SrFeuserRegister\Hooks\RegistrationProcessHooks;
use SJBR\SrFeuserRegister\Hooks\UsergroupHooks;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

call_user_func(
    function($extKey)
    {
    	$extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get($extKey);
		// Example of configuration of hooks
		// $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['tx_srfeuserregister_pi1']['confirmRegistrationClass'][] = 'SJBR\\SrFeuserRegister\\Hooks\\Handler';
		if (
			!isset($extensionConfiguration['tx_srfeuserregister_pi1']['registrationProcess'])
			|| !is_array($extensionConfiguration['tx_srfeuserregister_pi1']['registrationProcess'])
		) {
			$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['tx_srfeuserregister_pi1']['registrationProcess'] = [];
		}
		if (!in_array(RegistrationProcessHooks::class, $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['tx_srfeuserregister_pi1']['registrationProcess'])) {
		    $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['tx_srfeuserregister_pi1']['registrationProcess'][] = RegistrationProcessHooks::class;
		}
		// Configure captcha hooks
		if (
			!isset($extensionConfiguration['captcha'])
			|| !is_array($extensionConfiguration['captcha'])
		) {
			$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['captcha'] = [];
		}
		if (!in_array(Freecap::class, $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['captcha'])) {
		    $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['captcha'][] = Freecap::class;
		}
		// Configure usergroup hooks
		if (
			!isset($extensionConfiguration['tx_srfeuserregister_pi1']['configuration'])
			|| !is_array($extensionConfiguration['tx_srfeuserregister_pi1']['configuration'])
		) {
			$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['tx_srfeuserregister_pi1']['configuration'] = [];
		}
		if (!in_array(UsergroupHooks::class, $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['tx_srfeuserregister_pi1']['configuration'])) {
		    $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['tx_srfeuserregister_pi1']['configuration'][] = UsergroupHooks::class;
		}
		if (
			!isset($extensionConfiguration['tx_srfeuserregister_pi1']['fe_users']['usergroup'])
			|| !is_array($extensionConfiguration['tx_srfeuserregister_pi1']['fe_users']['usergroup'])
		) {
			$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['tx_srfeuserregister_pi1']['fe_users']['usergroup'] = [];
		}
		if (!in_array(UsergroupHooks::class, $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['tx_srfeuserregister_pi1']['fe_users']['usergroup'])) {
		    $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['tx_srfeuserregister_pi1']['fe_users']['usergroup'][] = UsergroupHooks::class;
		}
		// Configure file upload hooks
		if (
			!isset($extensionConfiguration['tx_srfeuserregister_pi1']['model'])
			|| !is_array($extensionConfiguration['tx_srfeuserregister_pi1']['model'])
		) {
			$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['tx_srfeuserregister_pi1']['model'] = [];
		}
		if (!in_array(FileUploadHooks::class, $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['tx_srfeuserregister_pi1']['model'])) {
		    $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['tx_srfeuserregister_pi1']['model'][] = FileUploadHooks::class;
		}
	},
	'sr_feuser_register'
);