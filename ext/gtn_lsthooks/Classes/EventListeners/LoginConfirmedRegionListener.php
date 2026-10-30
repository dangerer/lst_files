<?php

namespace Gtn\GtnLsthooks\EventListeners;

use TYPO3\CMS\FrontendLogin\Event\LoginConfirmedEvent;

final class LoginConfirmedRegionListener
{
	/**
	 * After a successful FE login, sync selectedRegion cookie to the user's lst_region.
	 */
	public function onLoginConfirmed(LoginConfirmedEvent $event): void
	{
		$user = $GLOBALS['TSFE']->fe_user->user ?? [];
		$lstRegion = (int)($user['lst_region'] ?? 0);

		// Always overwrite the cookie on login so cookie and user data stay in sync.
		$expire = time() + (60 * 60 * 24) * 183; // ~ half a year
		setcookie('selectedRegion', (string)$lstRegion, $expire, '/');
		$_COOKIE['selectedRegion'] = (string)$lstRegion;
	}
}
