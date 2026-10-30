<?php
namespace SJBR\SrFeuserRegister\View;

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
use SJBR\SrFeuserRegister\Exception;
use SJBR\SrFeuserRegister\Domain\Data;
use SJBR\SrFeuserRegister\Request\Parameters;
use SJBR\SrFeuserRegister\Mail\Message;
use SJBR\SrFeuserRegister\Setfixed\SetfixedUrls;
use SJBR\SrFeuserRegister\Utility\CssUtility;
use SJBR\SrFeuserRegister\Utility\HtmlUtility;
use SJBR\SrFeuserRegister\Utility\LocalizationUtility;
use SJBR\SrFeuserRegister\View\Marker;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * Email functions
 */
class Email
{
	/**
	 * Extension key
	 *
	 * @var string
	 */
	protected $extensionKey;

	/**
	 *  Extension name
	 *
	 * @var string Extension name
	 */
	protected $extensionName;

	/**
	 * Prefix used for CSS classes and variables
	 *
	 * @var string
	 */
	protected $prefixId;

	/**
	 * The table being used
	 *
	 * @var string
	 */
	protected $theTable;

	/**
	 * The plugin configuration
	 *
	 * @var array
	 */
	protected $conf;

	/**
	 * The data object
	 *
	 * @var Data
	 */
	protected $data;

	/**
	 * The request parameters object
	 *
	 * @var Parameters
	 */
	protected $parameters;

	/**
	 * The marker object
	 *
	 * @var Marker
	 */
	protected $marker;

	public $infomailPrefix = 'INFOMAIL_';
	public $emailMarkPrefix = 'EMAIL_TEMPLATE_';
	public $emailMarkAdminSuffix = '_ADMIN';
	public $emailMarkHTMLSuffix = '_HTML';

	/**
	 * Constructor
	 *
	 * @param string $extensionKey: the extension key
	 * @param string $prefixId: the prefixId
	 * @param string $theTable: the name of the table in use
	 * @param array $conf: the plugin configuration
	 * @param Data $data: the data object
	 * @param Parameters $parameters: the request parameters object
	 * @param Marker $marker: the marker object
	 * @return void
	 */
	public function __construct(
		$extensionKey,
		$prefixId,
		$theTable,
		array $conf,
		Data $data,
		Parameters $parameters,
		Marker $marker
	) {
		$this->extensionKey = $extensionKey;
		$this->extensionName = GeneralUtility::underscoredToUpperCamelCase($extensionKey);
	 	$this->prefixId = $prefixId;
		$this->theTable = $theTable;
		$this->conf = $conf;
	 	$this->data = $data;
	 	$this->parameters = $parameters;
	 	$this->marker = $marker;
	}

	public function isHTMLMailEnabled()
	{
		return $this->conf['email.']['HTMLMail'] ?? true;
	}

	/**
	 * Prepares an email message
	 *
	 * @param string $key: template key
	 * @param array $dataArray: a row of fe_users: entered data
	 * @param array $originalArray: a row of fe_users: original data in the database
	 * @param string $recipient: an email or the id of a front user
	 * @return void
	 */
	public function compile(
		$key,
		array $dataArray,
		array $originalArray,
		array $securedArray,
		$recipient,
		$cmd,
		$cmdKey
	) {
		$missingSubpartArray = [];
		$userSubpartsFound = 0;
		$adminSubpartsFound = 0;

		$setFixedConfig = $this->conf['setfixed.'] ?? [];
		$viewOnly = true;
        $requiredFields = $this->data->getRequiredFieldsArray($cmdKey);

		$originalArray = $originalArray ?? [];
		$dataArray = $dataArray ?? $originalArray;
		$currentRow = array_merge($originalArray, $dataArray);

        // Whether html will be used
		$htmlAllowed = $this->isHTMLMailEnabled() && ($currentRow['module_sys_dmail_html'] ?? false);
		// Setting CSS style markers if required
		if ($htmlAllowed) {
			$this->marker->addCSSStyleMarkers();
		}

		// Build row of modified fields
		$mrow = $currentRow;
		$changesOnly = (((isset($this->conf['email.']['EDIT_SAVED']) && $this->conf['email.']['EDIT_SAVED'] == '2') || (isset($this->conf['notify.']['EDIT_SAVED']) && $this->conf['notify.']['EDIT_SAVED'] == '2')) && $cmd == 'edit');
		$keepFields = $changesOnly ? ['uid', 'pid', 'tstamp', 'first_name', 'last_name', 'username'] : [];
		if ($changesOnly) {
			$mrow = [];
			foreach ($currentRow as $theField => $value) {
				if (in_array($theField, $keepFields)) {
					$mrow[$theField] = $value;
				} else {
					// Determine whether the value of the field was changed
					$valueChanged = !isset($dataArray[$theField]) && isset($originalArray[$theField]);
					if (isset($dataArray[$theField])) {
						if (isset($originalArray[$theField])) {
							if (is_array($dataArray[$theField]) && is_array($originalArray[$theField])) {
								$valueChanged = count(array_diff($dataArray[$theField], $originalArray[$theField])) || count(array_diff($originalArray[$theField], $dataArray[$theField]));
							} else {
								$valueChanged = ($dataArray[$theField] != $originalArray[$theField]);
							}
						} else {
							$valueChanged = true;
						}
					}
					if ($valueChanged) {
						$mrow[$theField] = $value;
					}
				}
			}
		}

		$content = ['user' => [], 'userhtml' => [], 'admin' => [], 'adminhtml' => [], 'mail' => []];
		$content['mail'] = '';
		$content['user']['all'] = '';
		$content['userhtml']['all'] = '';
		$content['admin']['all'] = '';
		$content['adminhtml']['all'] = '';
		$setfixedArray = ['SETFIXED_CREATE', 'SETFIXED_CREATE_REVIEW', 'SETFIXED_INVITE', 'SETFIXED_REVIEW'];
		$infomailArray = ['INFOMAIL', 'INFOMAIL_NORECORD'];
		if (
			(isset($this->conf['email.'][$key]) && $this->conf['email.'][$key] != '0')
			|| (($this->conf['enableEmailConfirmation'] ?? false) && in_array($key, $setfixedArray))
			|| (
				    // Silently refuse to send infomail to non-subscriber, if so requested
				    ($this->conf['infomail'] ?? false)
				    && in_array($key, $infomailArray)
				    && !($key === 'INFOMAIL_NORECORD' && isset($this->conf['email.'][$key]) && $this->conf['email.'][$key] == '0')
			    )
		) {
			$subpartMarker = '###' . $this->emailMarkPrefix . $key . '###';
			$content['user']['all'] = trim($this->marker->getSubpart($this->marker->getTemplateCode(), $subpartMarker));
			if ($content['user']['all'] == '') {
				$missingSubpartArray[] = $subpartMarker;
			} else {
				$content['user']['all'] = $this->marker->removeRequired($content['user']['all'], $this->data->getFailure(), $requiredFields, $this->data->getFieldList(), $this->data->getSpecialFieldList(), $cmdKey, $viewOnly);
				$userSubpartsFound++;
			}
			if ($htmlAllowed) {
				$subpartMarker = '###' . $this->emailMarkPrefix . $key . $this->emailMarkHTMLSuffix . '###';
				$content['userhtml']['all'] = trim($this->marker->getSubpart($this->marker->getTemplateCode(),  $subpartMarker));
				if ($content['userhtml']['all'] == '') {
					$missingSubpartArray[] = $subpartMarker;
				} else {
					$content['userhtml']['all'] = $this->marker->removeRequired($content['userhtml']['all'], $this->data->getFailure(), $requiredFields, $this->data->getFieldList(), $this->data->getSpecialFieldList(), $cmdKey, $viewOnly);
					$userSubpartsFound++;
				}
			}
		}

		if ($this->conf['notify.'][$key] ?? true) {
			$subpartMarker = '###' . $this->emailMarkPrefix . $key . $this->emailMarkAdminSuffix . '###';
			$content['admin']['all'] = trim($this->marker->getSubpart($this->marker->getTemplateCode(), $subpartMarker));
			if ($content['admin']['all'] == '') {
				$missingSubpartArray[] = $subpartMarker;
			} else {
				$content['admin']['all'] = $this->marker->removeRequired($content['admin']['all'], $this->data->getFailure(), $requiredFields, $this->data->getFieldList(), $this->data->getSpecialFieldList(), $cmdKey, $viewOnly);
				$adminSubpartsFound++;
			}

			if ($this->isHTMLMailEnabled()) {
				$subpartMarker =  '###' . $this->emailMarkPrefix . $key . $this->emailMarkAdminSuffix . $this->emailMarkHTMLSuffix . '###';
				$content['adminhtml']['all'] = trim($this->marker->getSubpart($this->marker->getTemplateCode(), $subpartMarker));
				if ($content['adminhtml']['all'] == '')	{
					$missingSubpartArray[] = $subpartMarker;
				} else {
					$content['adminhtml']['all'] = $this->marker->removeRequired($content['adminhtml']['all'], $this->data->getFailure(), $requiredFields, $this->data->getFieldList(), $this->data->getSpecialFieldList(), $cmdKey, $viewOnly);
					$adminSubpartsFound++;
				}
			}
		}

		$contentIndexArray = [];
		$contentIndexArray['text'] = [];
		$contentIndexArray['html'] = [];

		if ($content['user']['all']) {
			$content['user']['rec'] = $this->marker->getSubpart($content['user']['all'], '###SUB_RECORD###');
			$contentIndexArray['text'][] = 'user';
		}
		if ($content['userhtml']['all']) {
			$content['userhtml']['rec'] = $this->marker->getSubpart($content['userhtml']['all'], '###SUB_RECORD###');
			$contentIndexArray['html'][] = 'userhtml';
		}
		if ($content['admin']['all']) {
			$content['admin']['rec'] = $this->marker->getSubpart($content['admin']['all'], '###SUB_RECORD###');
			$contentIndexArray['text'][] = 'admin';
		}
		if ($content['adminhtml']['all']) {
			$content['adminhtml']['rec'] = $this->marker->getSubpart($content['adminhtml']['all'], '###SUB_RECORD###');
			$contentIndexArray['html'][] = 'adminhtml';
		}

		$this->marker->fillInMarkerArray($dataArray, $securedArray, '', false);
		$this->marker->addLabelMarkers($dataArray, $originalArray, $securedArray, $keepFields, $requiredFields, $this->data->getFieldList(), $this->data->getSpecialFieldList(), $changesOnly);
		$content['user']['all'] = $this->marker->substituteMarkerArray($content['user']['all'], $this->marker->getMarkerArray());
		$content['userhtml']['all'] = $this->marker->substituteMarkerArray($content['userhtml']['all'], $this->marker->getMarkerArray());
		$content['admin']['all'] = $this->marker->substituteMarkerArray($content['admin']['all'], $this->marker->getMarkerArray());
		$content['adminhtml']['all'] = $this->marker->substituteMarkerArray($content['adminhtml']['all'], $this->marker->getMarkerArray());

		$this->marker->addAuthCodeMarker($dataArray);
		// If setfixed is enabled
		if (
			((($this->conf['enableEmailConfirmation'] ?? false) || ($this->conf['setfixed'] ?? false)) && ($this->theTable === 'fe_users' || isset($currentRow['uid'])))
			|| ($this->theTable === 'fe_users' && ($this->conf['enableAdminReview'] ?? false))
			|| ($this->theTable !== 'fe_users' && ($this->conf['infomail'] ?? false) && ($cmd === 'setfixed' || $cmd === 'infomail') && isset($currentRow['uid']))
		) {
		    $request = $this->parameters->getRequest();
			$setfixedUrls = SetfixedUrls::compute($this->prefixId, $this->theTable, $this->conf, $this->parameters->getPids(), $cmd, $currentRow, $request);
			$this->marker->addSetfixedUrlMarkers($setfixedUrls);
		}
		$this->marker->addStaticInfoMarkers($dataArray, $viewOnly);
		$this->marker->addLabelMarkers($dataArray, $originalArray ?? [], $securedArray, $keepFields, $this->data->getRequiredFieldsArray($cmdKey), $this->data->getFieldList(), $this->data->getSpecialFieldList());
		$infoFields = implode(',', array_keys($dataArray));
		foreach ($contentIndexArray as $emailType => $indexArray) {
			$this->marker->fillInMarkerArray($mrow, $securedArray, '', false, 'FIELD_', ($emailType === 'html'));
			$this->marker->addTcaMarkers($dataArray, $originalArray ?? [], $cmd, $cmdKey, $viewOnly, $this->data->getRequiredFieldsArray($cmdKey), 'email', $changesOnly, ($emailType === 'html'));
			$this->marker->addAdditionalMarkers($infoFields, $cmd, $cmdKey, $dataArray, $viewOnly, 'email', ($emailType === 'html'));
			foreach ($indexArray as $index) {
				$content[$index]['rec'] = $this->marker->removeNonIncluded($content[$index]['rec'], array_keys($mrow), $cmdKey);
				$content[$index]['rec'] = $this->marker->removeStaticInfoSubparts($content[$index]['rec'], $viewOnly);
				$content[$index]['accum'] = ($content[$index]['accum'] ?? '') . $this->marker->substituteMarkerArray($content[$index]['rec'], $this->marker->getMarkerArray());
				if ($emailType === 'text') {
					$content[$index]['accum'] = htmlSpecialChars_decode($content[$index]['accum'], ENT_QUOTES);
				}
			}
		}

		// Substitute the markers and eliminate HTML markup from plain text versions
		if ($content['user']['all']) {
			$content['user']['final'] = $this->marker->substituteSubpart($content['user']['all'], '###SUB_RECORD###', $content['user']['accum']);
			$content['user']['final'] = HtmlUtility::removeHTMLComments($content['user']['final']);
			$content['user']['final'] = HtmlUtility::replaceHTMLBr($content['user']['final']);
			$content['user']['final'] = HtmlUtility::removeHtmlTags($content['user']['final']);
			$content['user']['final'] = HtmlUtility::removeSuperfluousLineFeeds($content['user']['final']);
			// Remove erroneous \n from locallang file
			$content['user']['final'] = str_replace('\n', '', $content['user']['final']);
		}
		if ($content['userhtml']['all']) {
			$content['userhtml']['final'] = $this->marker->substituteSubpart($content['userhtml']['all'], '###SUB_RECORD###', CssUtility::wrapInBaseClass($this->prefixId, $content['userhtml']['accum']));
			// Remove HTML comments
			$content['userhtml']['final'] = HtmlUtility::removeHTMLComments($content['userhtml']['final'], true);
			// Remove erroneous \n from locallang file
			$content['userhtml']['final'] = str_replace('\n', '', $content['userhtml']['final']);
		}
		if ($content['admin']['all']) {
			$content['admin']['final'] = $this->marker->substituteSubpart($content['admin']['all'], '###SUB_RECORD###', $content['admin']['accum']);
			$content['admin']['final'] = HtmlUtility::removeHTMLComments($content['admin']['final']);
			$content['admin']['final'] = HtmlUtility::replaceHTMLBr($content['admin']['final']);
			$content['admin']['final'] = HtmlUtility::removeHtmlTags($content['admin']['final']);
			$content['admin']['final'] = HtmlUtility::removeSuperfluousLineFeeds($content['admin']['final']);
			// Remove erroneous \n from locallang file
			$content['admin']['final'] = str_replace('\n', '', $content['admin']['final']);
		}

		if ($content['adminhtml']['all']) {
			$content['adminhtml']['final'] = $this->marker->substituteSubpart($content['adminhtml']['all'], '###SUB_RECORD###', CssUtility::wrapInBaseClass($this->prefixId, $content['adminhtml']['accum']));
			// Remove HTML comments
			$content['adminhtml']['final'] = HtmlUtility::removeHTMLComments($content['adminhtml']['final'], true);
			// Remove erroneous \n from locallang file
			$content['adminhtml']['final'] = str_replace('\n', '', $content['adminhtml']['final']);
		}

		$bRecipientIsInt = MathUtility::canBeInterpretedAsInteger($recipient);
		if ($bRecipientIsInt) {
			$fe_userRec = $GLOBALS['TSFE']->sys_page->getRawRecord('fe_users', $recipient);
			$recipient = $fe_userRec['email'];
		}
		// Check if we need to add an attachment
		if (
			isset($this->conf['addAttachment'], $this->conf['addAttachment.']['cmd'], $this->conf['addAttachment.']['sFK'])
			&& $this->conf['addAttachment'] === '1'
			&& $this->conf['addAttachment.']['cmd'] === $cmd
			&& $this->conf['addAttachment.']['sFK'] === $this->parameters->getFeUserData('sFK')
		) {
			$file = (isset($this->conf['addAttachment.']['file']) && $this->conf['addAttachment.']['file']) ? GeneralUtility::getFileAbsFileName($this->conf['addAttachment.']['file']) : '';
		}
		// SETFIXED_REVIEW will be sent to user only if the admin part is present
		if (($userSubpartsFound + $adminSubpartsFound >= 1) && ($adminSubpartsFound >= 1 || $key !== 'SETFIXED_REVIEW')) {
			// Uncomment to inspect confirmation mail (subject/body/recipients) before send:
			// $adminHtml = (string)($content['adminhtml']['final'] ?? '');
			// $adminPlain = (string)($content['admin']['final'] ?? '');
			// $subjectFromHtml = $adminHtml !== '' ? trim((string)(preg_split('/<title>|<\\/title>/i', $adminHtml, 3)[1] ?? '')) : '';
			// \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump([
			//     'key' => $key,
			//     'pidTitle' => $this->parameters->getPidTitle(),
			//     'adminRecipient' => $this->conf['email.']['admin'] ?? '',
			//     'userRecipient' => $recipient,
			//     'adminSubject' => $subjectFromHtml !== '' ? strip_tags($subjectFromHtml) : trim((string)(explode("\n", $adminPlain, 2)[0] ?? '')),
			//     'adminBodyPreview' => mb_substr(trim(preg_replace('/\s+/', ' ', strip_tags($adminHtml !== '' ? $adminHtml : $adminPlain))), 0, 400),
			// ], 'LST DEBUG: confirmation mail');
			// die('LST DEBUG: stopped before mail send');
			Message::send($recipient, $this->conf['email.']['admin'] ?? '', $content['user']['final'] ?? '', $content['userhtml']['final'] ?? '', $content['admin']['final'] ?? '', $content['adminhtml']['final'] ?? '', $file ?? '', $this->conf);
		} else if ($this->conf['notify.'][$key] ?? false) {
			$errorText = LocalizationUtility::translate('internal_no_subtemplate', $this->extensionName);
			$errorText = sprintf($errorText, implode(', ' , $missingSubpartArray));
			throw new Exception($errorText, Exception::MISSING_SUBPART);
		}
	}
}