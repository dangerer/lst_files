<?php
namespace Gtn\GtnLsthooks\Hooks;

use Gtn\GtnLsthooks\Utils\Utils;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Frontend\Page\PageRepositoryGetPageHookInterface;
use Typoheads\Formhandler\Session\TYPO3;

class TypoScriptFrontendController extends \TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager{

	/**
	 * @param $params
	 * @param \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $ref
	 * @return bool
	 */
	public function redirectByTS(&$params, $ref) {
		// !!!!get Typoscript across BE, because FE is not generated yet !!!!
		/** @var \TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager $configurationManager */
		//$configurationManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
		$configurationManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Configuration\\BackendConfigurationManager');
		$configurationManager->currentPageId = $ref->id;
		$extbaseFrameworkConfiguration = $configurationManager->getTypoScriptSetup();
		$tsSettings = $extbaseFrameworkConfiguration['plugin.']['lstHooks.'];

		$pageRedirectsByRegion = $tsSettings['pageRedirectsByRegion.'];

		if (!$pageRedirectsByRegion)
			return true;
		$uid = $ref->id;
        $utils = GeneralUtility::makeInstance(Utils::class);
        $region = $utils->getSelectedRegion();
		//$region = ($_COOKIE['selectedRegion'] ? $_COOKIE['selectedRegion'] : 0);
		// change uid of page
		if (is_array($pageRedirectsByRegion) && is_array($pageRedirectsByRegion[$uid.'.'])) {
			//echo $uid.'-';
			if ($pageRedirectsByRegion[$uid.'.'][$region] > 0 ) {
				//$refCopy = $ref;
				//$ref->newCObj();
				$redirectUri = $ref->cObj->getTypoLink_URL($pageRedirectsByRegion[$uid.'.'][$region]);
				\TYPO3\CMS\Core\Utility\HttpUtility::redirect($redirectUri);//, \TYPO3\CMS\Core\Utility\HttpUtility::HTTP_STATUS_302);
				die;
				/** @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $cObj */
				//$cObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer');
				//DebuggerUtility::var_dump($cObj); exit;
				//$redirectUri = $refCopy->cObj->typoLink_URL(array('parameter' => $uid));
				//$uriBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(UriBuilder::class);
				//$redirectUri = $uriBuilder->reset()
				//	->setTargetPageUid($pageRedirectsByRegion[$uid.'.'][$region])
				//	->setCreateAbsoluteUri(TRUE)
				//	->build();
				//DebuggerUtility::var_dump($pageRedirectsByRegion);				)
				//DebuggerUtility::var_dump($redirectUri, 'uri');
				//DebuggerUtility::var_dump($uid, 'uid');
			}
		}
	}

    public function keepUserLanguage(&$params, $ref)
    {
        // if the user changes FE language - save to database
//        if ($_SERVER['REMOTE_ADDR'] == '188.163.53.4')
//            DebuggerUtility::var_dump($GLOBALS["TSFE"]->sys_language_uid);
        $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
        if (isset($GLOBALS["TSFE"]->fe_user) && $languageAspect->getId() != $GLOBALS["TSFE"]->fe_user->user['fe_language']) {
            $insertData = array(
                'fe_language' => $languageAspect->getId(),
                'language' => strtoupper($GLOBALS["TSFE"]->sys_language_isocode)
            );
            // DISABLED: SZ 01.05.2021. moved to fe_manager form
//            $GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', ' uid = ' . intval($GLOBALS["TSFE"]->fe_user->user['uid']), $insertData);
//            $GLOBALS["TSFE"]->fe_user->user["fe_language"] = $GLOBALS["TSFE"]->sys_language_uid;
        }
    }

    public function checkEnableFields($params, $pObj) {
        echo '!!';
        if ($_SERVER['REMOTE_ADDR'] == '188.163.53.4') {
            print_r($params);
        }
    }


}