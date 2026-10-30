<?php

use Psr\Log\LogLevel;
use SGalinski\Scriptmerger\Log\Writer\SysLogWriter;
use SGalinski\Scriptmerger\user_ScriptmergerCacheHook;
use TYPO3\CMS\Core\Log\Writer\FileWriter;

call_user_func(
	static function () {
		// post-processing hook to clear any existing cache files if the button in
		// the backend is clicked (contains an age check)
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] =
			user_ScriptmergerCacheHook::class . '->clearCachePostProc';

		// needs to be disabled for the frontend, otherwise the default exclude rule prevents any script files from merging
		$GLOBALS['TYPO3_CONF_VARS']['FE']['compressionLevel'] = '0';

		// Configure the logger for "scriptmerger" errors
		$GLOBALS['TYPO3_CONF_VARS']['LOG']['SGalinski']['Scriptmerger'] = [
			'writerConfiguration' => [
				LogLevel::WARNING => [
					SysLogWriter::class => [],
					FileWriter::class => []
				]
			]
		];
	}
);
