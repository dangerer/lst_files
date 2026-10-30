<?php
defined('TYPO3') or die();

call_user_func(static function() {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['logoff_post_processing'][] =
        \Jigal\T3adminer\Hooks\T3AdminerHooks::class . '->logoffHook';
});
