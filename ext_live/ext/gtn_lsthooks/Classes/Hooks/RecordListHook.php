<?php
namespace Gtn\GtnLsthooks\Hooks;

use Gtn\GtnLsthooks\Utils\Utils;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\MvcPropertyMappingConfigurationService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
class RecordListHook
{

    private $articleFields = array('anzahl', 'type', 'artikelnummer', 'deliverynumber', 'seriennummer', 'returnreason', 'beschreibung', 'auftragsnummer');
//    private $ranFormPid = 12435; // TEST page
    private $ranFormPid = 32;
    private $ranFormUid = 3;

    public function makeClip($table, $row, $cells, &$parentObject)
    {
        $cells = []; // remove all clipboard buttons
        return $cells;
    }

    public function makeControl($table, $row, $cells, &$parentObject)
    {
        // only for "international_ran_requests"
        if ($table === 'international_ran_requests') {
            $cells = []; // CLEAR all buttons
            $cells['ranGroup'] = [];
            if ($row['status']) {
                $cells['ranGroup']['status'] = $this->getStatusIcon($row);
                $cells['status'] = $this->getStatusIcon($row);
            } else {
                $cells['ranGroup']['approve'] = $this->getApproveIcon($row);
                $cells['ranGroup']['decline'] = $this->getDeclineIcon($row);
                $cells['approve'] = $this->getApproveIcon($row);
                $cells['decline'] = $this->getDeclineIcon($row);
            }
            // clear only selected fields
            /*foreach ($this->buttonsToHide as $buttonToHide) {
                unset($cells[$buttonToHide]);
                unset($cells['primary'][$buttonToHide]);
                unset($cells['secondary'][$buttonToHide]);
            }*/

        }

        return $cells;
    }

    public function renderListHeader($table, $currentIdList, $headerColumns, &$parentObject)
    {
        return $headerColumns;
    }

    public function renderListHeaderActions($table, $currentIdList, $cells, &$parentObject)
    {
        return $cells;
    }

    public function getApproveIcon($ranRecord)
    {
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $iconHtml = $iconFactory->getIcon('ran-international-request-approve', Icon::SIZE_SMALL);
        $title = 'Diese Anfrage annehmen';
        $utils = GeneralUtility::makeInstance(Utils::class);
        $link = $utils->getRanApproveDeclineUrl($ranRecord, $this->articleFields, $this->ranFormUid, $this->ranFormPid, 'approve');
//        $link = $this->getApproveDeclineUrl($ranRecord, 'approve');
        $content = '<a title="' . $title . '" href="' . $link . '" target="_blank" class="btn btn-default">' . $iconHtml . '</a>';
        return $content;
    }

    public function getDeclineIcon($ranRecord)
    {
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $iconHtml = $iconFactory->getIcon('ran-international-request-decline', Icon::SIZE_SMALL);
        $title = 'Diese Anfrage ablehnen';
        $utils = GeneralUtility::makeInstance(Utils::class);
        $link = $utils->getRanApproveDeclineUrl($ranRecord, $this->articleFields, $this->ranFormUid, $this->ranFormPid, 'decline');
//        $link = $this->getApproveDeclineUrl($ranRecord, 'decline');
        $content = '<a title="' . $title . '" href="' . $link . '" target="_blank" class="btn btn-default">' . $iconHtml . '</a>';
        return $content;
    }

    public function getStatusIcon($ranRecord)
    {
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        switch ($ranRecord['status']) {
            case 'decline':
                $iconHtml = $iconFactory->getIcon('ran-international-request-decline', Icon::SIZE_SMALL);
                break;
            case 'approve':
            default:
                $iconHtml = $iconFactory->getIcon('ran-international-request-approve', Icon::SIZE_SMALL);
        }
        $date = date('d.m.Y H.i.s', $ranRecord['submit_tstamp']);
        $title = 'Der Status: '.$ranRecord['status'].' '.$date.($ranRecord['decline_reason'] ? "\r\n\r\nGrund: \r\n".$ranRecord['decline_reason'] : '');
        $link = '#';
        $content = '<a title="' . $title . '" href="' . $link . '" onClick="return false;" class="btn btn-light">' . $iconHtml . ' '.$date.'</a>';
        return $content;
    }

    // deprecated? moved into Utils
    public function getApproveDeclineUrl($ranRecord, $action = 'approve')
    {
        $ignoreRecordField = ['uid', 'pid', 'tstamp', 'crdate', 'cruser_id', 'deleted', 'hidden', 'starttime', 'endtime', 'sys_language_uid', 'hash'];
        $paramsArray = [];
        foreach ($ranRecord as $fieldName => $ranValue) {
            if (in_array($fieldName, $ignoreRecordField)) {
                continue;
            }
            $paramsArray['tx_powermail_pi1']['field'][$fieldName] = $ranValue;
        }
        // articlesdata - additional code for special field - mix of different article datas
        $articlesdata = [];
        $i = 1;
        $arrData = [];
        foreach ($this->articleFields as $fieldName) {
            $arrData[$fieldName] = (array)json_decode($ranRecord[$fieldName]);
        }
        $artikelnummers = (array)json_decode($ranRecord['artikelnummer']);
        foreach ($artikelnummers as $k => $articleData) {
            foreach ($this->articleFields as $fieldName) {
                @$articlesdata[$i][$fieldName] = @$arrData[$fieldName][$k];
            }
            $i++;
        }
        // force region! -> only International
        $paramsArray['tx_powermail_pi1']['field']['forceRegion'] = 'Internationaler Verkauf';

        // merge articles data
        $paramsArray['tx_powermail_pi1']['field']['articlesdata'] = json_encode($articlesdata);

        // system params
        $paramsArray['tx_powermail_pi1']['__referrer']['@extension'] = 'Powermail';
        $paramsArray['tx_powermail_pi1']['__referrer']['@controller'] = 'Form';
        $paramsArray['tx_powermail_pi1']['__referrer']['@action'] = 'form';
        $paramsArray['tx_powermail_pi1']['controller'] = 'Form';
        $paramsArray['tx_powermail_pi1']['action'] = 'confirmation';
        $paramsArray['tx_powermail_pi1']['mail']['form'] = $this->ranFormUid;

        // signal about approving / declining!
        switch ($action) {
            case 'decline':
                $paramsArray['tx_powermail_pi1']['field']['forceDecline'] = $ranRecord['uid'].':'.$ranRecord['hash'];
                $paramsArray['tx_powermail_pi1']['field']['declineReason'] = '';
                break;
            case 'approve':
            default:
                $paramsArray['tx_powermail_pi1']['field']['forceApprove'] = $ranRecord['uid'].':'.$ranRecord['hash'];
        }

        // add __trustedProperties
        $paramsArray['tx_powermail_pi1']['__trustedProperties'] = $this->getTrustedParameter($paramsArray['tx_powermail_pi1']['field']);

//        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($paramsArray, 'RecordListHook.php:100', 4); exit; // !!!!!!!!!! delete it
        $paramsString = ltrim(http_build_query($paramsArray), '&');

        $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $typoLinkConfig = [
            'parameter' => $this->ranFormPid,
            'additionalParams' => '&'.$paramsString,
            'forceAbsoluteUrl' => 1,
        ];
        $pageUrl = $contentObjectRenderer->typoLink_URL($typoLinkConfig);

        return $pageUrl;
    }

    public function getTrustedParameter($data)
    {
        $mvcService = GeneralUtility::makeInstance(MvcPropertyMappingConfigurationService::class);
        $propsToDelete = [];
        $trustedProps = [];
        foreach ($data as $prop => $value) {
            if (!in_array($prop, $propsToDelete)) {
                if ($prop == 'datenschutz') {
                    $trustedProps[] = 'tx_powermail_pi1[field][' . $prop . '][]';
                } else {
                    $trustedProps[] = 'tx_powermail_pi1[field][' . $prop . ']';
                }
            }
        }
        $trustedProps[] = 'tx_powermail_pi1[field][__hp]';
        $trustedProps[] = 'tx_powermail_pi1[mail][form]';

        $res = $mvcService->generateTrustedPropertiesToken($trustedProps, 'tx_powermail_pi1');

        return $res;
    }

    public function getRecordLabel(&$PA)
    {
//        $recordData = $PA['row'];
        // $row is not full, so get the data right from DB:
        $recordData = BackendUtility::getRecord('international_ran_requests', $PA['row']['uid']);
//        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($recordData, 'RecordListHook.php:168', 4); exit; // !!!!!!!!!! delete it
        $titleData = [];

        $titleData[] = 'Datum: '.date('d.m.Y H:i', $recordData['crdate']);
        $titleData[] = 'Kundennummer: '.$recordData['kundennummer'];
        $titleData[] = 'Kundenreferenz: '.$recordData['kundenreferenz'];
        $titleData[] = 'E-Mail: '.$recordData['email'];
        $titleData[] = 'Telefon: '.$recordData['telefon'];
        $titleData[] = 'Kundennummer: '.$recordData['vorname'];
        $titleData[] = 'Vorname: '.$recordData['nachname'];
        $titleData[] = 'Nachname: '.$recordData['vorname'];
        $titleData[] = 'Land: '.$recordData['land2']; // country
        $titleData[] = 'Ort: '.$recordData['marker']; // city
        $articlesTemp = (array)json_decode($recordData['artikelnummer']);
        $titleData[] = 'Artikeln: '.count($articlesTemp);

        $PA['title'] = implode(' / '."\r\n", $titleData);
    }

}

