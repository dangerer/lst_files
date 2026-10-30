<?php

declare(strict_types=1);

namespace Jigal\T3adminer\Hooks;

use TYPO3\CMS\Core\Authentication\AbstractUserAuthentication;

class T3AdminerHooks
{

    /**
     * Hook to remove t3adminer session on logoff
     *
     * @param $parameters
     * @param AbstractUserAuthentication $parentObject
     * @return void
     */
    public function logoffHook(&$parameters, AbstractUserAuthentication $parentObject): void
    {
        if (isset($_SESSION)) {
            session_write_close();
        }
        if ($sessionId = $_COOKIE['tx_t3adminer'] ?? null) {
            session_id($sessionId);
            session_start();
            unset(
                $_SESSION['pwds'],
                $_SESSION['ADM_driver'],
                $_SESSION['ADM_user'],
                $_SESSION['ADM_password'],
                $_SESSION['ADM_server'],
                $_SESSION['ADM_db'],
                $_SESSION['ADM_tca'],
                $_SESSION['ADM_extConf'],
                $_SESSION['ADM_hideOtherDBs'],
                $_SESSION['ADM_SignonURL'],
                $_SESSION['ADM_uploadDir']
            );
            session_write_close();
            $parentObject->removeCookie('tx_t3adminer');
        }
    }
}
