<?php
namespace SJBR\SrFeuserRegister\Setfixed;

/*
 *  Copyright notice
 *
 *  (c) 2007-2023 Stanislas Rolland <typo3AAAA(arobas)sjbr.ca>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

use Psr\Http\Message\ServerRequestInterface;
use SJBR\SrFeuserRegister\Security\Authentication;
use SJBR\SrFeuserRegister\Utility\HashUtility;
use SJBR\SrFeuserRegister\Utility\UrlUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * Compute setfixed urls
 */
class SetfixedUrls
{
	/**
	 * Computes the setfixed url's
	 *
	 * @param string $cmd: the current command
	 * @param array $record: the record row
	 * @param ServerRequestInterface $request
	 * @return array array of key => url pairs
	 */
	public static function compute(
		$prefixId,
		$theTable,
		array $conf,
		array $pids,
		$cmd,
		array $record,
		ServerRequestInterface $request
	) {
		$setfixedUrls = [];
		if (isset($conf['setfixed.']) && is_array($conf['setfixed.'])) {
			foreach ($conf['setfixed.'] as $theKey => $data) {
				if (strstr($theKey, '.')) {
					$theKey = substr($theKey, 0, -1);
				}
				$parametersArray = [];
	
				$parametersArray[$prefixId]['rU'] = $record['uid'];
				$fieldList = $data['_FIELDLIST'] ?? '';
				$fieldListArray = GeneralUtility::trimExplode(',', $fieldList);
				foreach ($fieldListArray as $fieldname) {
					if (isset($data[$fieldname])) {
						$record[$fieldname] = $data[$fieldname];
					}
				}

				if ($theTable !== 'fe_users' && $theKey === 'EDIT') {
					$theCmd = $pidCmd = 'edit';
					if ($conf['edit.']['setfixed'] ?? false) {
						$addSetfixedHash = true;
					} else {
						$addSetfixedHash = false;
						$parametersArray[$prefixId]['aC'] = Authentication::authCode($record, $conf, $fieldList);
					}
				} else {
					$theCmd = 'setfixed';
					$pidCmd = ($cmd === 'invite' ? 'confirmInvitation' : 'confirm');
					$parametersArray[$prefixId]['sFK'] = $theKey;
					$addSetfixedHash = true;
					if (isset($record['auto_login_key']) && $record['auto_login_key']) {
						$parametersArray[$prefixId]['key'] = $record['auto_login_key'];
					}
				}
				if ($addSetfixedHash) {
					$parametersArray[$prefixId]['aC'] = Authentication::setfixedHash($record, $conf, $fieldList);
				}
				$parametersArray[$prefixId]['cmd'] = $theCmd;
	
				if (is_array($data) ) {
					foreach ($data as $fieldname => $fieldValue) {
						if (strpos($fieldname, '.') !== false) {
							continue;
						}
						$parametersArray['fD'][$fieldname] = rawurlencode($fieldValue);
					}
				}
	
				$linkPID = $pids[$pidCmd];
                $L = $request->getParsedBody()['L'] ?? ($request->getQueryParams()['L'] ?? null);
				if ($L && !GeneralUtility::inList($request->getAttribute('frontend.controller')->config['config']['linkVars'] ?? '', 'L')) {
					$parametersArray['L'] = $L;
				}
	
				if ($conf['useShortUrls'] ?? false) {
					$thisHash = HashUtility::getHashFromParameters($parametersArray);
					$parametersArray = [$prefixId => ['regHash' => $thisHash]];
				}
				$urlConf = [];
				$urlConf['disableGroupAccessCheck'] = true;
				$confirmType = (isset($conf['confirmType']) && MathUtility::canBeInterpretedAsInteger($conf['confirmType'])) ? (int)$conf['confirmType'] : $request->getAttribute('routing')->getPageType();
				$url = UrlUtility::getTypoLink_URL($linkPID . ',' . $confirmType, $parametersArray, '', $urlConf);
				$setfixedUrls[$theKey] = $url;
			}
		}
		return $setfixedUrls;
	}
}