<?php
defined('TYPO3') or die();

// Configure extension static templates
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('sr_feuser_register', 'Configuration/TypoScript/PluginSetup', 'FE User Registration Setup');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('sr_feuser_register', 'Configuration/TypoScript/DefaultStyles', 'FE User Registration CSS Styles');