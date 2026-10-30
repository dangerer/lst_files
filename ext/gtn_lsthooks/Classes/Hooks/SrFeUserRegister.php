<?php
namespace Gtn\GtnLsthooks\Hooks;


use SJBR\SrFeuserRegister\Controller\SetfixedActionController;
use SJBR\SrFeuserRegister\Utility\HashUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Service\EnvironmentService;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Context\Context;

class SrFeUserRegister {

	private $tsSettings = null;

	function __construct() {
		/** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager */
		$configurationManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
		$settings = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		$this->tsSettings = $settings['plugin.']['tx_srfeuserregister_pi1.'];
	}

	// after saving. For example - set Category by selected region for tt_address
	public function registrationProcess_afterSaveEdit($table, $dataArray, $origArray, $token, &$newRow, $cmd, $cmdKey, $pid, $newFieldList, $pObj) {
		if ($table === 'fe_users') { // from tx_srfeuserregister_pi1
            $insertData = array();
			$selectedRegion = intval(($_COOKIE['selectedRegion'] ? $_COOKIE['selectedRegion'] : 0));
            if ($selectedRegion === 'null') // it is possible from JS
                $selectedRegion = 0;
			if ($selectedRegion > 0) {
				$insertData['lst_region'] = $selectedRegion;
			}
            $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');

            if ($languageAspect->getId() > 0) {
                $insertData['fe_language'] = $languageAspect->getId();
            }
            $insertData['language'] = strtoupper($GLOBALS['TSFE']->sys_language_isocode);
			if (count($insertData) > 0)
                $GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', ' uid = '.intval($newRow['uid']), $insertData);
		}
		if ($table === 'tt_address') { // from tx_sremailsubscribe_pi1
			$selectedRegion = intval(($_COOKIE['selectedRegion'] ? $_COOKIE['selectedRegion'] : 0));
            if ($selectedRegion === 'null') // it is possible from JS
                $selectedRegion = 0;
			if ($selectedRegion > 0) {
				//DebuggerUtility::var_dump($newRow); exit;
				//$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['gtn_lsthooks']);
//                $extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('backend')["gtn_lsthooks"];
                $extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get("gtn_lsthooks");

                $sql2 = 'SELECT * FROM sys_category WHERE parent = '.intval($extConf['regionRootCategoryUid']);
				$regionCategoriesRes = $GLOBALS['TYPO3_DB']->sql_query($sql2);
				while($res = $regionCategoriesRes->fetch_object())
					$regionCategories[] = $res->uid;
				// delete all old relations to regions
				$del = $GLOBALS['TYPO3_DB']->exec_DELETEquery('sys_category_record_mm', ' uid_local IN ('.implode(',', $regionCategories).') AND uid_foreign = '.intval($newRow['uid'].' AND tablenames=\'tt_address\' AND fieldname=\'categories\''));
				// create new relation
				$mm_data = array(
					'uid_local' => $selectedRegion,
					'uid_foreign' => intval($newRow['uid']),
					'tablenames' => 'tt_address',
					'fieldname' => 'categories'
				);
				$new = $GLOBALS['TYPO3_DB']->exec_INSERTquery('sys_category_record_mm', $mm_data);
				// update field categories in tt_address
				//$currCat = $newRow['categories'];
				//$currCat++;
				//$upd = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tt_address', ' uid = '.intval($newRow['uid']), array('categories' => $currCat));

				// bad code :-(
				//$addressRep = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\TtAddress\Domain\Repository\AddressRepository::class);
				/** @var \TYPO3\TtAddress\Domain\Model\Address $address */
				//$address = $addressRep->findByUid($newRow['uid']);
				//if ($address) {
				//	$address->
				//}
				/** @var \TYPO3\CMS\Core\Category\Collection\CategoryCollection $collection */
				//$collection = \TYPO3\CMS\Core\Category\Collection\CategoryCollection::load($selectedRegion, true, 'tt_address', 'categories');
				//$items = $collection->getItems();
				//DebuggerUtility::var_dump($items);
				//$newRow['fieldname'] = 'categories'; // for correct relation
				//$collection->add($newRow);
				//$collection->persist();
				//$items = $collection->getItems();
				//DebuggerUtility::var_dump($items);exit;
				//$items = $collection->
				//$items = $collection->getItems();
				//$GLOBALS['TYPO3_DB']->sql_query('UPDATE fe_users SET pid = '.intval($newPid).' WHERE uid = '.intval($newRow['uid']));
			}
		}
		//$S = $this->getSettingsByLstZone($dataArray['lstZone'], $pid);
		//$newPid = $S['userPidId'];
		// save result to DB
		//if ($newPid > 0 && $newRow['uid'] > 0)
		//	$GLOBALS['TYPO3_DB']->sql_query('UPDATE fe_users SET pid = '.intval($newPid).' WHERE uid = '.intval($newRow['uid']));
		//DebuggerUtility::var_dump($dataArray, 'dataArray');
		//DebuggerUtility::var_dump($origArray, 'origArray');
		//DebuggerUtility::var_dump($this->tsSettings, 'TS');
		//DebuggerUtility::var_dump($zonePidRelations, 'lstPidRelations');
		//DebuggerUtility::var_dump($newRow, 'newRow');
		//DebuggerUtility::var_dump($newPid, 'newPid');
		//DebuggerUtility::var_dump($cmd, 'cmd');
		//DebuggerUtility::var_dump($cmdKey, 'cmdKey');
		//DebuggerUtility::var_dump($newFieldList, 'newFieldList');
		//exit;
	}

	public function registrationProcess_afterSaveCreate($table, $dataArray, $origArray, $token, &$newRow, $cmd, $cmdKey, $pid, $extraList, $pObj) {
		return $this->registrationProcess_afterSaveEdit($table, $dataArray, $origArray, $token, $newRow, $cmd, $cmdKey, $pid, $extraList, $pObj);
	}

    /**
     * @param array $recordArray
     * @param SetfixedActionController $pObj
     */
    public function confirmRegistrationClass_postProcess($recordArray, $pObj) {
        // add tt_address record if the user selects LST Newsletter
        // name, first_name, last_name, gender, email, company, zip, static_info_country, modulte_sys_dmail_html = 1, terms_acknowledged = 1
        // pid by lang
        if ($pObj->getTable() === 'fe_users') {
            if ($recordArray['lst_newsletter'] == 1) {
                $email = $recordArray['email'];
                // pids look in lib.sr_emailsubscribe.ts
                // [Gtn\GtnLsthooks\Condition\FeLanguageSelectedCondition = 1]...
                $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
                $langUid = $languageAspect->getId();
                if ($recordArray['fe_language'] != $langUid) {
                    $langUid = $recordArray['fe_language']; // changed from form
                }
                switch ($langUid) {
                    case 1:
                        $pidByLang = 335;
                        break;
                    default:
                        $pidByLang = 114;
                }
                $data = array(
                    'pid' => $pidByLang,
                    'email' => $email,
                    'name' => $recordArray['first_name'] . ' ' . $recordArray['last_name'],
                    'first_name' => $recordArray['first_name'],
                    'last_name' => $recordArray['last_name'],
                    'gender' => $recordArray['gender'],
                    'company' => $recordArray['company'],
                    'zip' => $recordArray['zip'],
                    'static_info_country' => $recordArray['static_info_country'],
                    'module_sys_dmail_html' => 1,
                    'terms_acknowledged' => 1,
                    'tstamp' => time()
                );
                $existing = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', 'tt_address', ' email = \'' . $email . '\' AND deleted = 0 AND hidden = 0');
                if ($existing) {
                    // already existing. update
                    $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tt_address', 'uid=' . $existing['uid'], $data);
                } else {
                    // new record
                    $data['crdate'] = time();
                    $GLOBALS['TYPO3_DB']->exec_INSERTquery('tt_address', $data);
                }
            }
        }
    }

	protected function ____getSettingsByLstZone($lstZone = 0, $defaultPid = 47) {
		// change pid by selected LST zone
		$settings = null;
		$zonePidRelations = $this->tsSettings['lstSettings.']['zonePidRelations.'];
		if ($lstZone > 0 && is_array($zonePidRelations[$lstZone.'.']))
			$settings = $zonePidRelations[$lstZone.'.'];
		if (!$settings['userPidId'])
			$settings['userPidId'] = $defaultPid;
		return $settings;
	}

	/** не работает
	 * попробовать запретить отправку сообщений и отправлять самостоятельно
	 */
	public function ____registrationProcess_beforeConfirmCreate($table, $dataArray, $parameters, $cmdKey, &$conf) {
		//$S = $this->getSettingsByLstZone($dataArray['lstZone']);
		$conf['email.']['admin'] = 'razavs@gmail.com';
	}

	/**
	 * Hook for add custom markers
	*/
	public function addGlobalMarkers(&$markerArray, $pObj, $cmdKey, $conf) {
        $cObj = new ContentObjectRenderer();
		$pid = 47;
		// get user uid from GP (after redirecting!)
		$gpData = GeneralUtility::_GP('tx_srfeuserregister_pi1');
		$userUid = $gpData['rU'];
		// get user pid
		$userRow = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', 'fe_users', 'uid = ' . intval($userUid));
		if(is_array($userRow) && count($userRow))
			$pid = $userRow['pid'];
		$markerArray['###CONFIRMATION_USER_PID###'] = $pid;
        $kontaktPid = $this->tsSettings['lstSettings.']['kontaktPid'];
        $url = $cObj->typoLink_URL(array('parameter'=>$kontaktPid, 'forceAbsoluteUrl' => 1));
        $markerArray['###LINK_TO_KONTAKT###'] = $url;
        // regions name by user
        $regionPath = '';
        $regions = $this->tsSettings['lstSettings.']['regionDescriptions.'];
        if(is_array($userRow) && count($userRow) && $userRow['lst_region'] > 0) {
            $userRegion = $userRow['lst_region'].'.';
            if (array_key_exists($userRegion, $regions))
                $regionPath = $regions[$userRegion]['shortPath'];
        }
        $markerArray['###LST_REGION_PATH###'] = $regionPath;

        # rules for password
        $markerArray['###LST_PASSWORDRULESTOOLTIP###'] = LocalizationUtility::translate('lst_passwordRulesForTooltip', 'SrFeuserRegister');
        if(is_array($userRow) && count($userRow) && isset($userRow['gender'])) {
            // only for admin emails, so - without translation mode
            if (intval($userRow['gender']) === 0)
                $markerArray['###LST_GENDER_TITLE###'] = 'Herr';
            else
                $markerArray['###LST_GENDER_TITLE###'] = 'Frau';
        }
	}

	/**
	 * Hook for add custom markers
	 */
	public function addMarkers($table, $field, $cmd, $cmdKey, $dataArray, $viewOnly, $activity, $bHtml, $extensionName, $prefixId, $conf) {
		$markerArray = array();
		// AGB pid
		$agbPid = $this->tsSettings['lstSettings.']['agbPid'];
		//$httpRequest = new \TYPO3\CMS\Extbase\Mvc\Request();
		//$envService = new EnvironmentService();
		//$confManager = new \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface();
		//$uB = new UriBuilder();
		//$uB->setRequest($httpRequest);
		//$uB->injectEnvironmentService($envService);
		//$uB->injectConfigurationManager($confManager);
		//$uri = $uB->setTargetPageUid($agbPid)->setCreateAbsoluteUri(TRUE)->build();
		$cObj = new ContentObjectRenderer();
		$url = $cObj->typoLink_URL(array('parameter'=>$agbPid, 'forceAbsoluteUrl' => 1));
		$markerArray['###LINK_TO_AGB###'] = $url;
        $markerArray['###AGB_CONFIRMATION_MESSAGE###'] = LocalizationUtility::translate('lst_agb_confirmation_label', 'SrFeuserRegister', array($url));

        // different translations
        $markerArray['###LST_NEWSLETTER_SUBSCRIBE###'] = LocalizationUtility::translate('newsletter_subscribe', 'SrEmailSubscribe', array($url));
        $markerArray['###LST_NEWSLETTER_SUBSCRIBE_MESSAGE1###'] = LocalizationUtility::translate('newsletter_subscribe_message1', 'SrEmailSubscribe', array($url));
        $markerArray['###LST_NEWSLETTER_SUBSCRIBE_MESSAGE2###'] = LocalizationUtility::translate('newsletter_subscribe_message2', 'SrEmailSubscribe', array($url));
        $markerArray['###LST_GENDER_M###'] = LocalizationUtility::translate('gender_m', 'SrEmailSubscribe', array($url));
        $markerArray['###LST_GENDER_W###'] = LocalizationUtility::translate('gender_w', 'SrEmailSubscribe', array($url));
        $markerArray['###LST_NOTE_REQUIRED_FIELDS###'] = LocalizationUtility::translate('note_required_fields', 'SrFeuserRegister', array($url));
        $markerArray['###LST_FE_LANGUAGE_0###'] = LocalizationUtility::translate('fe_language_0', 'SrEmailSubscribe');
        $markerArray['###LST_FE_LANGUAGE_1###'] = LocalizationUtility::translate('fe_language_1', 'SrEmailSubscribe');

		$markerArray['###LST_REGION_SELECT_FOR_FORMS###'] = \Gtn\GtnLsthooks\Utils\Utils::getTemplateContent('SelectedRegionForForms');

        # user gender
        if(is_array($dataArray) && count($dataArray) && isset($dataArray['gender'])) {
            // only for admin emails, so - without translation mode
            if (intval($dataArray['gender']) === 0)
                $markerArray['###LST_GENDER_TITLE###'] = 'Herr';
            else
                $markerArray['###LST_GENDER_TITLE###'] = 'Frau';
        }

       # DebuggerUtility::var_dump($url);
		return $markerArray;
	}

	/**
	 * Hook for evaluate the password
	 */
	public function evalValues($table, $dataArray, $theField, $cmdKey, $cmdParts) {
		if ($theField == 'password' && $cmdParts[0] == 'gtnLstHook') {
		    // one letter, uppercase, special
//			$pattern = '/^(?=.*)(?=.*[a-z])(?=.*[A-Z])(?=.*[`~@#$%^&+=.,:;?!\-_*()\\\[\]"\'|])([a-zA-Z0-9`~@#$%^&+=*.,:;?!\-_()\\\[\]"\'|]){1,100}$/';
            // letter, upper, digital
//			$pattern = '/^(?=.*)(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*)([a-zA-Z0-9]){1,100}$/';
            // letter, upper, digital = mandatory and any character - is not mandatory
			$pattern = '/^(?=.*)(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*)([a-zA-Z0-9\S]){1,100}$/';
			$matches = array();
			$test = preg_match($pattern, $dataArray[$theField], $matches);
			if (count($matches) === 0)
				return $theField; // return fieldname if error
		}
		return '';
	}


}