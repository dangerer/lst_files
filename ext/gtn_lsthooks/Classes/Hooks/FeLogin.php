<?php

namespace Gtn\GtnLsthooks\Hooks;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Hook
 *
 * @package TYPO3
 * @subpackage gtn_lsthooks
 */
class FeLogin {

	/**
	 * Hook after login
	 * @param array $params
	 * @param FrontendLoginController $pObj
	 */
	public function regionAfterLogin($params = null, $pObj) {
		//DebuggerUtility::var_dump($params['pObj']);
		// This hook is invoked by EXT:felogin in FE context.
		if (!is_object($pObj)) {
			return;
		}

		// Only for new sessions (i.e. on successful login): force cookie to user's stored lst_region
		if (!empty($pObj->newSessionID)) {
			$lstRegion = (int)(is_array($pObj->user ?? null) ? ($pObj->user['lst_region'] ?? 0) : 0);
			$expire = time() + (60 * 60 * 24) * 183; // ~ half a year
			// Always overwrite, even if a cookie existed before login.
			setcookie('selectedRegion', (string)$lstRegion, $expire, '/');
			$_COOKIE['selectedRegion'] = (string)$lstRegion;
			return;
		}

		// If the user changes region while logged in, persist cookie value back to fe_users.lst_region
		$selectedRegion = (int)($_COOKIE['selectedRegion'] ?? 0);
		$userData = $GLOBALS['TSFE']->fe_user->user ?? null;
		if (!is_array($userData) || empty($userData['uid'])) {
			return;
		}
		$currentRegion = (int)($userData['lst_region'] ?? 0);
		if ($selectedRegion !== $currentRegion) {
			$connection = GeneralUtility::makeInstance(ConnectionPool::class)
				->getConnectionForTable('fe_users');
			$connection->update(
				'fe_users',
				['lst_region' => $selectedRegion],
				['uid' => (int)$userData['uid']]
			);
			$GLOBALS['TSFE']->fe_user->user['lst_region'] = $selectedRegion;
		}
	}

	public function emailMarkers($params = null, $pObj) {
		if ($params['user']['uid'] > 0) {
			$userData = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', 'fe_users', ' uid = '.intval($params['user']['uid']));
			$replacements = array(
				'###USER_FIRSTNAME###' => $userData['first_name'],
				'###USER_LASTNAME###' => $userData['last_name']
			);
			$params['message'] = str_replace(array_keys($replacements), array_values($replacements), $params['message']);
			// delete all between <!###DELETE###>.....</!###DELETE###>
			$params['message'] = preg_replace('/<!###DELETE###>(.*)<\/!###DELETE###>/', '', $params['message']);
			// delete all temporary markers
			//$replacements = array(
			//	'<!###DELETE###>' => $userData['first_name'],
			//	'</!###DELETE###>' => $userData['last_name']
			//);
		}
	}

}