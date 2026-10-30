<?php
namespace SJBR\SrFeuserRegister\Controller;

/*
 *  Copyright notice
 *
 *  (c) 1999-2003 Kasper Skårhøj <kasperYYYY@typo3.com>
 *  (c) 2004-2023 Stanislas Rolland <typo3AAAA(arobas)sjbr.ca>
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

use SJBR\SrFeuserRegister\Exception;
use SJBR\SrFeuserRegister\Security\Authentication;
use SJBR\SrFeuserRegister\Security\SessionData;
use SJBR\SrFeuserRegister\Security\StorageSecurity;
use SJBR\SrFeuserRegister\Utility\LocalizationUtility;
use SJBR\SrFeuserRegister\View\AbstractView;
use SJBR\SrFeuserRegister\View\EditView;
use SJBR\SrFeuserRegister\View\Marker;
use SJBR\SrFeuserRegister\View\PlainView;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Setfixed action funtions
 */
class SetfixedActionController extends AbstractActionController
{
	/**
	 * Process the front end user reply to the confirmation request
	 *
	 * @param array $dataArray: array of form input fields
	 * @param string $cmd: the command
	 * @param string $cmdKey: the command key
	 * @return string the template with substituted markers
	 */
	public function doProcessing(array $dataArray, $cmd, $cmdKey) {
		// If setfixed is configured
		if (
			empty($this->conf['enableEmailConfirmation'] ?? '')
			&& ($this->theTable !== 'fe_users' || empty($this->conf['enableAdminReview'] ?? ''))
			&& empty($this->conf['setfixed'] ?? '')
			&& ($this->theTable === 'fe_users' || empty($this->conf['infomail'] ?? ''))
		) {
			$errorText = LocalizationUtility::translate('internal_setfixed_option', $this->extensionName);
			throw new Exception($errorText, Exception::MISCONFIGURATION);			
		}
		$content = '';
		$uid = $this->data->getRecUid();
		$origArray = $this->data->getOrigArray();
		$securedArray = ($this->theTable === 'fe_users') ? SessionData::readSecuredArray($this->extensionKey) : [];
		$origArray = $this->data->parseIncomingData($origArray, false);
		$feuData = $this->parameters->getFeUserData();
		$row = $origArray;
		$autoLoginIsRequested = false;
		$origUsergroup = $row['usergroup'] ?? '';
		$setfixedUsergroup = '';
		$sFK = $feuData['sFK'] ?? '';
		$setfixedSuffix = $sFK;
		$fD = $this->parameters->getSetFixedVars();
		$fieldArr = [];
		if (isset($fD) && is_array($fD)) {
			foreach ($fD as $field => $value) {
				$row[$field] = rawurldecode($value);
				if ($field === 'usergroup') {
					$setfixedUsergroup = rawurldecode($value);
				}
				$fieldArr[] = $field;
			}
		}
		if ($this->theTable === 'fe_users') {
			// Determine whether auto login is requested
			$autoLoginIsRequested = StorageSecurity::getAutoLoginIsRequested($feuData, $row);
		}
		// Calculate the setfixed hash from incoming data
		$fieldList = $row['_FIELDLIST'] ?? ($this->conf['setfixed.'][$sFK . '.']['_FIELDLIST'] ?? '');
		$codeLength = strlen($this->parameters->getAuthCode());
		// Let's try with a code length of 8 in case this link is coming from direct mail
		if ($codeLength == 8 && in_array($sFK, array('DELETE', 'EDIT', 'UNSUBSCRIBE'))) {
			$theCode = Authentication::setfixedHash($row, $this->conf, $fieldList, $codeLength);
		} else {
			$theCode = Authentication::setfixedHash($row, $this->conf, $fieldList);
		}
		if (
			!strcmp($this->parameters->getAuthCode(), $theCode)
			&& !(
			    $sFK === 'APPROVE'
			    && ((isset($origArray['disable']) && $origArray['disable'] == '0') || (isset($origArray['hidden']) && $origArray['hidden'] == '0'))
			    )
		) {
			if ($sFK === 'EDIT') {
				$this->marker->addGeneralHiddenFieldsMarkers('edit', $this->parameters->getAuthCode(), $this->parameters->getBackURL());
				$editView = GeneralUtility::makeInstance(EditView::class, $this->extensionKey, $this->prefixId, $this->theTable, $this->conf, $this->data, $this->parameters, $this->marker);
				$content = $editView->render(
					$dataArray,
					$origArray,
					$securedArray,
					'setfixed',
					$cmdKey,
					AbstractView::MODE_NORMAL,
					$this->data->inError
				);
			} else if ($sFK === 'DELETE' || $sFK === 'REFUSE') {
				if (!($GLOBALS['TCA'][$this->theTable]['ctrl']['delete'] ?? false) || ($this->conf['forceFileDelete'] ?? false)) {
					// If the record is fully deleted... then remove the image attached.
					$this->data->deleteFilesFromRecord($uid);
				}
				$res = $this->data->deleteRecordByUid($uid);
				$this->data->deleteMMRelations($uid, $row);
			} else {
				if ($this->theTable === 'fe_users') {
					if ($this->conf['create.']['allowUserGroupSelection'] ?? false) {
						$originalGroups = is_array($origUsergroup) ? $origUsergroup : GeneralUtility::trimExplode(',', $origUsergroup, true);
						$overwriteGroups = GeneralUtility::trimExplode(',', $this->conf['create.']['overrideValues.']['usergroup'] ?? '', true);
						$remainingGroups = array_diff($originalGroups, $overwriteGroups);
						$groupsToAdd = GeneralUtility::trimExplode(',', $setfixedUsergroup, true);
						$finalGroups = array_merge(
							$remainingGroups, $groupsToAdd
						);
						$row['usergroup'] = implode(',', array_unique($finalGroups));
					}
				}
				// Hook: first we initialize the hooks
				$hookObjectsArr = [];
				$extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get($this->extensionKey);
				if (
					isset($extensionConfiguration[$this->prefixId]['confirmRegistrationClass'])
					&& is_array($extensionConfiguration[$this->prefixId]['confirmRegistrationClass'])
				) {
					foreach ($extensionConfiguration[$this->prefixId]['confirmRegistrationClass'] as $classRef) {
						$hookObjectsArr[] = GeneralUtility::makeInstance($classRef);
					}
				}
				// Hook: confirmRegistrationClass_preProcess
				foreach($hookObjectsArr as $hookObj) {
					if (method_exists($hookObj, 'confirmRegistrationClass_preProcess')) {
						$hookObj->confirmRegistrationClass_preProcess($row, $this);
					}
				}
				$newFieldList = implode(',', array_intersect(
					GeneralUtility::trimExplode(',', $this->data->getFieldList(), 1),
					GeneralUtility::trimExplode(',', implode(',', $fieldArr), 1)
				));

				if ($sFK === 'UNSUBSCRIBE') {
					$newFieldList = implode(',', array_intersect(
						GeneralUtility::trimExplode(',', $newFieldList),
						GeneralUtility::trimExplode(',', $this->conf['unsubscribeAllowedFields'] ?? '', 1)
					));
				}

				if ($sFK !== 'ENTER' && $newFieldList != '') {
					$res = $this->data->updateRecord($uid, $row, $newFieldList);
				}
				$currArr = $origArray;
				if ($autoLoginIsRequested) {
					StorageSecurity::decryptPasswordForAutoLogin($currArr, $row);
				}
				$modArray = [];
				$currArr = $this->data->modifyTcaMMfields($currArr, $modArray);
				$row = array_merge($row, $modArray);

				// Hook: confirmRegistrationClass_postProcess
				foreach ($hookObjectsArr as $hookObj) {
					if (method_exists($hookObj, 'confirmRegistrationClass_postProcess')) {
						$hookObj->confirmRegistrationClass_postProcess($row, $this);
					}
				}
			}

			// Outputting template
			if ($this->theTable === 'fe_users' && in_array($sFK, ['APPROVE', 'ENTER', 'LOGIN'])) {
				if (!($row['by_invitation'] ?? false)) {
					$this->marker->addGeneralHiddenFieldsMarkers('login', $this->parameters->getAuthCode(), $this->parameters->getBackURL());
				}
			} else {
				$this->marker->addGeneralHiddenFieldsMarkers('setfixed', $this->parameters->getAuthCode(), $this->parameters->getBackURL());
			}

			if ($sFK === 'EDIT') {
				// Nothing to do
			} else {
				if (
					$this->theTable === 'fe_users'
					&& ($sFK === 'APPROVE' || $sFK === 'ENTER')
					&& ($row['by_invitation'] ?? false)
				) {
					// Auto login
					$loginSuccess = $this->login($currArr['username'], $currArr['password'], false);
					if ($loginSuccess) {
						$editView = GeneralUtility::makeInstance(EditView::class, $this->extensionKey, $this->prefixId, $this->theTable, $this->conf, $this->data, $this->parameters, $this->marker);
						$content = $editView->render(
							$dataArray,
							$origArray,
							$securedArray,
							'password',
							'password',
							AbstractView::MODE_NORMAL
						);
					} else {
						// Login failed
						$plainView = GeneralUtility::makeInstance(PlainView::class, $this->extensionKey, $this->prefixId, $this->theTable, $this->conf, $this->data, $this->parameters, $this->marker);
						$content = $plainView->render('###TEMPLATE_SETFIXED_FAILED###', $row, $origArray, $securedArray, $cmd, $cmdKey);
					}
				}

				if (($this->conf['enableAdminReview'] ?? false) && $sFK === 'APPROVE') {
					$setfixedSuffix .= '_REVIEW';
				}
				if (!$content) {
					$plainView = GeneralUtility::makeInstance(PlainView::class, $this->extensionKey, $this->prefixId, $this->theTable, $this->conf, $this->data, $this->parameters, $this->marker);
					try {
						$subpartMarker = '###TEMPLATE_' . Marker::SETFIXED_PREFIX . 'OK_' . $setfixedSuffix . '###';
						$content = $plainView->render($subpartMarker, $row, $origArray, $securedArray, $cmd, $cmdKey);
					} catch (\Exception $e) {
						$subpartMarker = '###TEMPLATE_' . Marker::SETFIXED_PREFIX .'OK###';
						$content = $plainView->render($subpartMarker, $row, $origArray, $securedArray, $cmd, $cmdKey);
					}
				}
				if (
					($this->conf['email.']['SETFIXED_REFUSE'] ?? false)
					|| ($this->conf['enableEmailConfirmation'] ?? false)
					|| ($this->conf['infomail'] ?? false)
				) {
					$this->email->compile(
						Marker::SETFIXED_PREFIX . $setfixedSuffix,
						$row,
						$origArray,
						$securedArray,
						$origArray[$this->conf['email.']['field']] ?? '',
						'setfixed',
						$cmdKey
					);
				}
				if ($this->theTable === 'fe_users') {
					// If applicable, send admin a request to review the registration request
					if (
						($this->conf['enableAdminReview'] ?? false)
						&& $sFK === 'APPROVE'
						&& !($row['by_invitation'] ?? false)
					) {
						$this->email->compile(
							Marker::SETFIXED_PREFIX . 'REVIEW',
							$row,
							$origArray,
							$securedArray,
							$origArray[$this->conf['email.']['field']] ?? '',
							'setfixed',
							$cmdKey
						);
					}
					if (
						($this->conf['enableAutoLoginOnConfirmation'] ?? false)
						&& !($row['by_invitation'] ?? false)
						&& (($sFK === 'APPROVE' && !($this->conf['enableAdminReview'] ?? false)) || $sFK === 'ENTER')
						&& $autoLoginIsRequested
					) {
						// Auto login on confirmation
						$loginSuccess = $this->login($currArr['username'], $currArr['password']);
						if ($loginSuccess) {
							// Login was successful
							exit;
						} else {
							// Login failed
							$plainView = GeneralUtility::makeInstance(PlainView::class, $this->extensionKey, $this->prefixId, $this->theTable, $this->conf, $this->data, $this->parameters, $this->marker);
							$content = $plainView->render('###TEMPLATE_SETFIXED_FAILED###', [], $origArray, $securedArray, $cmd, $cmdKey);
						}
					}
				}
			}
		} else {
			$plainView = GeneralUtility::makeInstance(PlainView::class, $this->extensionKey, $this->prefixId, $this->theTable, $this->conf, $this->data, $this->parameters, $this->marker);
			$content = $plainView->render('###TEMPLATE_SETFIXED_FAILED###', [], $origArray, $securedArray, $cmd, $cmdKey);
		}
		return $content;
	}
}