<?php
defined('TYPO3') or die();

use SJBR\SrFeuserRegister\Captcha\Freecap;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

call_user_func(
    function($extKey)
    {
    	$extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get($extKey);
		// Captcha hooks
		if (
			!isset($extensionConfiguration['captcha'])
			|| !is_array($extensionConfiguration['captcha'])
		) {
		    $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['captcha'] = [];
		}
		$captchaHooks = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['captcha'] ?? [];
		if (!in_array(Freecap::class, $captchaHooks)) {
		    $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extKey]['captcha'][] = Freecap::class;
		}
	},
	'sr_email_subscribe'
);