<?php
namespace Gtn\GtnLsthooks\Utils;
use Doctrine\Common\Util\Debug;
use Doctrine\DBAL\Connection;
use Symfony\Component\Config\Util\XmlUtils;
use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\MvcPropertyMappingConfigurationService;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Created by PhpStorm.
 * User: Ras
 * Date: 26.01.2018
 * Time: 16:18
 */
class Utils implements \TYPO3\CMS\Core\SingletonInterface {

	public function showRegionSelectForForm($content, $conf) {
		return self::getTemplateContent('SelectedRegionForForms');
	}

	public static function getTemplateContent($templateName, array $variables = array()) {
	    #return '!!!!!';
		//$configurationManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
		//$extbaseFrameworkConfiguration = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);

		$configurationManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Configuration\\BackendConfigurationManager');
		//if ($etapas->getPid() > 0)
		//	$configurationManager->currentPageId = $etapas->getPid();
		$extbaseFrameworkConfiguration = $configurationManager->getTypoScriptSetup();
		$extbaseFrameworkConfiguration = $extbaseFrameworkConfiguration['plugin.']['tx_gtnlsthooks.'];
		//if(empty($extbaseFrameworkConfiguration['view']['templateRootPath'])){
		//	$extbaseFrameworkConfiguration['view']['templateRootPath'] = $extbaseFrameworkConfiguration['view.']['templateRootPaths.'][0];
		//}

		$templateRootPath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($extbaseFrameworkConfiguration['view.']['templateRootPath']);

		/** @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
		$view = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
		$templatePathAndFilename = $templateRootPath . $templateName . '.html';
		$view->setTemplatePathAndFilename($templatePathAndFilename);
		$view->assignMultiple($variables); // assign Fluid variable
		$content = $view->render();
//		DebuggerUtility::var_dump($subject);
//		DebuggerUtility::var_dump($emailBody);
//		echo $emailBody;
//		exit;
		/** @var $message \TYPO3\CMS\Core\Mail\MailMessage */

		return $content;
	}

    public function getSelectedRegion()
    {
        // region from user's data is MAIN
        /*if ($GLOBALS["TSFE"]->fe_user->user["uid"] > 0) {
            $selectedRegion = $GLOBALS["TSFE"]->fe_user->user['lst_region'];
        } else {
            $selectedRegion = ($_COOKIE['selectedRegion'] ? $_COOKIE['selectedRegion'] : 0);
        }*/
        // region frm Cookie is MAIN
        $selectedRegion = @$_COOKIE['selectedRegion'] ?: 0;
        if (!$selectedRegion && $GLOBALS["TSFE"]->fe_user && $GLOBALS["TSFE"]->fe_user->user["uid"] > 0) {
            $selectedRegion = $GLOBALS["TSFE"]->fe_user->user['lst_region'];
        }
        if ($selectedRegion === 'null') {// it is possible from JS
            $selectedRegion = 0;
        }
        return $selectedRegion;
	}

    // course registration form: get number of already registered records
    public function getCourseExistingRegistrationNumber($courseUid, $courseRegistrationFormUid = 20, $courseUidMarker = 'kursuid')
    {
//        return 12;
        if (!$courseUid) {
            return 0;
        }
        $registered = 0;
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tx_powermail_domain_model_mail');
        $queryBuilder = $connection->createQueryBuilder();
        // get mails for this course (by answer with course uid)
        $statement = $queryBuilder->select('m.*')
            ->from('tx_powermail_domain_model_answer', 'a')
            ->leftJoin('a', 'tx_powermail_domain_model_field', 'f', $queryBuilder->expr()->eq('a.field', $queryBuilder->quoteIdentifier('f.uid')))
            ->leftJoin('a', 'tx_powermail_domain_model_mail', 'm', $queryBuilder->expr()->eq('a.mail', $queryBuilder->quoteIdentifier('m.uid')))
            ->leftJoin('m', 'tx_powermail_domain_model_form', 'form', $queryBuilder->expr()->eq('m.form', $queryBuilder->quoteIdentifier('form.uid')))
            ->where(
                $queryBuilder->expr()->eq('form.uid', $queryBuilder->createNamedParameter($courseRegistrationFormUid, \PDO::PARAM_INT)),
                $queryBuilder->expr()->eq('f.marker', $queryBuilder->createNamedParameter($courseUidMarker, \PDO::PARAM_INT)),
                $queryBuilder->expr()->eq('a.value', $queryBuilder->createNamedParameter($courseUid, \PDO::PARAM_INT))
            )
            ->executeQuery()
            ->fetchAllAssociative();
        if ($statement && count($statement) > 0) {
            $mailUids = array_map(function($m) {return $m['uid'];}, $statement);
        }
        if ($mailUids && count($mailUids) > 0) {
            // +1 participant if self-checked
            $queryBuilder = $connection->createQueryBuilder();
            $statementSelfParticipants = $queryBuilder->select('a.value')
                ->from('tx_powermail_domain_model_answer', 'a')
                ->join('a', 'tx_powermail_domain_model_field', 'f', $queryBuilder->expr()->eq('a.field', $queryBuilder->quoteIdentifier('f.uid')))
                ->leftJoin('a', 'tx_powermail_domain_model_mail', 'm', $queryBuilder->expr()->eq('a.mail', $queryBuilder->quoteIdentifier('m.uid')))
                ->where(
                    $queryBuilder->expr()->in('m.uid', $queryBuilder->createNamedParameter($mailUids, Connection::PARAM_INT_ARRAY)),
                    $queryBuilder->expr()->eq('f.marker', $queryBuilder->createNamedParameter('selbstfuerkursanmelden', \PDO::PARAM_STR))
                )
                ->executeQuery()
                ->fetchAllAssociative();
            if ($statementSelfParticipants && count($statementSelfParticipants) > 0) {
                foreach ($statementSelfParticipants as $mailSelfParticipants) {
                    if ($mailSelfParticipants['value']) {
                        $registered += 1;
                    };
                }
            }

            // get answers with json participants data. Searching by 'Vorname' field!!!! it contains JSON data
            $queryBuilder = $connection->createQueryBuilder();
            $statementParticipants = $queryBuilder->select('a.value')
                ->from('tx_powermail_domain_model_answer', 'a')
                ->join('a', 'tx_powermail_domain_model_field', 'f', $queryBuilder->expr()->eq('a.field', $queryBuilder->quoteIdentifier('f.uid')))
                ->leftJoin('a', 'tx_powermail_domain_model_mail', 'm', $queryBuilder->expr()->eq('a.mail', $queryBuilder->quoteIdentifier('m.uid')))
                ->where(
                    $queryBuilder->expr()->in('m.uid', $queryBuilder->createNamedParameter($mailUids, Connection::PARAM_INT_ARRAY)),
                    $queryBuilder->expr()->eq('f.marker', $queryBuilder->createNamedParameter('vorname_01', \PDO::PARAM_STR))
                )
                ->executeQuery()
                ->fetchAllAssociative();
            if ($statementParticipants && count($statementParticipants) > 0) {
                foreach ($statementParticipants as $mailParticipants) {
                    $mailParticipantsArr = (array)json_decode($mailParticipants['value']);
                    $mailParticipantsArr = array_filter($mailParticipantsArr); // not empty Vorname !
                    $registered += count($mailParticipantsArr);
                }
            }
        }
        return $registered;
    }
    // course registrations: get max possible participants - option from DCE element
    public function getCourseMaxParticipants($courseUid)
    {

        // 1. the course is DCE element = typo3 conetent element. The DCE data is in FLEX format in 'pi_flexform' field
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tt_content');
        $queryBuilder = $connection->createQueryBuilder();
        $piData = $queryBuilder->select('c.pi_flexform')
            ->from('tt_content', 'c')
            ->where(
                $queryBuilder->expr()->eq('c.uid', $queryBuilder->createNamedParameter($courseUid, \PDO::PARAM_INT))
            )
            ->executeQuery()
            ->fetchOne();
		/*if ($_GET["d"]==1) {
			echo "<pre>";
			var_dump(htmlspecialchars($dataArray));echo "</pre>";die();
		}*/
        if ($piData) {
            $flexFormTools = GeneralUtility::makeInstance(FlexFormService::class);
            $dataArray = $flexFormTools->convertFlexFormContentToArray($piData);
            if ($max = @$dataArray['settings']['maxnumberofparticipants']) {
				return $max;
            }
        }
        return 9999;
    }
    // course registrations: get course data - options from DCE element
    public function getCourseData($courseUid)
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tt_content');
        $queryBuilder = $connection->createQueryBuilder();

        // 1. prepare lang conditoins
        $where = [];
        $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
        if ($languageAspect->getId() > 0) {
            // language by ID
            $where[] = $queryBuilder->expr()->eq('c.l18n_parent', $queryBuilder->createNamedParameter($courseUid, \PDO::PARAM_INT));
            $where[] = $queryBuilder->expr()->eq('c.sys_language_uid', $queryBuilder->createNamedParameter($languageAspect->getId(), \PDO::PARAM_INT));
        } else {
            // DE lang - default
            $where[] = $queryBuilder->expr()->eq('c.uid', $queryBuilder->createNamedParameter($courseUid, \PDO::PARAM_INT));
        }

        // 2. the course is DCE element = typo3 content element. The DCE data is in FLEX format in 'pi_flexform' field
        $courseData = $queryBuilder->select('c.pi_flexform', 'c.pid')
            ->from('tt_content', 'c')
            ->where(
                ...$where
            )
            ->executeQuery()
            ->fetchAllAssociative();
        if ($courseData) {
            $piData = @$courseData[0]['pi_flexform'];
            $pid = @$courseData[0]['pid'];
            $flexFormTools = GeneralUtility::makeInstance(FlexFormService::class);
            $dataArray = $flexFormTools->convertFlexFormContentToArray($piData);
            if (@$dataArray['settings']) {
                $dataArray['settings']['uid'] = $courseUid; // course uid = tt_content uid
                $dataArray['settings']['pid'] = $pid; // page uid (to get links)
                return $dataArray['settings'];
            }
        }
        return null;
    }

    public function getCategoryTitle($categoryUid)
    {
        // For some reason Category titles are always in the same language (English??)
        // So - tramslate them into current site language
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('sys_category');

        $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
//        return $categoryUid.' = '.$languageAspect->getId();

//        $categoryRepository = GeneralUtility::makeInstance(CategoryRepository::class);
//        $category = $categoryRepository->findByUid($categoryUid);

        // here is MAIN category (DEUTSCH title)
        $queryBuilder = $connection->createQueryBuilder();
        $categoryData = $queryBuilder->select('c.*')
            ->from('sys_category', 'c')
            ->where(
                $queryBuilder->expr()->eq('c.uid', $queryBuilder->createNamedParameter($categoryUid, \PDO::PARAM_INT))
            )
            ->executeQuery()
            ->fetchAllAssociative();
        $category = $categoryData[0];
        if ($languageAspect->getId() > 0) {
            // language by ID
            $queryBuilder = $connection->createQueryBuilder();
            $categoryData = $queryBuilder->select('c.*')
                ->from('sys_category', 'c')
                ->where(
                    $queryBuilder->expr()->eq('c.l10n_parent', $queryBuilder->createNamedParameter($categoryUid, \PDO::PARAM_INT)),
                    $queryBuilder->expr()->eq('c.sys_language_uid', $queryBuilder->createNamedParameter($languageAspect->getId(), \PDO::PARAM_INT))
                )
                ->executeQuery()
                ->fetchAllAssociative();
            $category = $categoryData[0];
            return $category['title'];
        } else {
            // return DEUTSCH by default
            return $category['title'];
        }
        return '';
    }

    /**
     * @param array $ranRecord
     * @param array $articleFields
     * @param int $ranFormUid
     * @param int $ranFormPageUid
     * @param string $action
     * @return string
     */
    public function getRanApproveDeclineUrl($ranRecord, $articleFields, $ranFormUid, $ranFormPageUid, $action = 'approve')
    {
        $ignoreRecordField = ['uid', 'pid', 'tstamp', 'crdate', 'cruser_id', 'deleted', 'hidden', 'starttime', 'endtime', 'sys_language_uid', 'hash', 'status', 'decline_reason', 'submit_tstamp', 'approve_decline_buttons'];
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
        foreach ($articleFields as $fieldName) {
            $arrData[$fieldName] = (array)json_decode($ranRecord[$fieldName]);
        }
        $artikelnummers = (array)json_decode($ranRecord['artikelnummer']);
        foreach ($artikelnummers as $k => $articleData) {
            foreach ($articleFields as $fieldName) {
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
        $paramsArray['tx_powermail_pi1']['mail']['form'] = $ranFormUid;

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
            'parameter' => $ranFormPageUid,
            'additionalParams' => '&'.$paramsString,
            'forceAbsoluteUrl' => 1,
            'language' => @$ranRecord['sys_language_uid'] ?: 0,
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



}