<?php
namespace Gtn\GtnLsthooks\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Core\Domain\Repository\PageRepositoryGetPageHookInterface;
use Typoheads\Formhandler\Session\TYPO3;

class PageRepository implements PageRepositoryGetPageHookInterface {

	private $tsSettings = null;
	/** @var UtilsForHooks UtilsForHooks|null  */
	private $utils = null;

	function __construct() {

		/** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager */
//		$configurationManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
//		$settings = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
//		$this->tsSettings = $settings['plugin.']['lstHooks.'];
        /** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager */
		$this->utils = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(UtilsForHooks::class);
	}


	/**
	 * @param int $uid
	 * @param bool $disableGroupAccessCheck
	 * @param \TYPO3\CMS\Core\Domain\Repository\PageRepository $parentObject
	 * @return bool
	 */
	public function getPage_preProcess(&$uid, &$disableGroupAccessCheck, \TYPO3\CMS\Core\Domain\Repository\PageRepository $parentObject) {
//	    $result = true;
        $utils = $this->utils;
        // disabled now
        $result = $utils::checkPageCategory($uid);

//        if ($_SERVER['REMOTE_ADDR'] == '188.163.53.4') {
//            echo '<br>'.$uid.':'.var_dump($result).'<br>';
//        }
        // if result is FALSE (page is hidden) and page has child - we need to check all subpages for this page
       /* if (!$result) {
            // get ALL child
            $childs = $this->getTreePids($uid, true);
            if (count($childs) > 1) {
                $result = $this->checkPageCategory($childs, true);
            }
        }*/
       if (!$result)
           $parentObject->where_hid_del = ' AND pages.uid=1111111 ';

	}

    /*public function checkEnableFields($params, $pObj) {
	    echo '!!';
        if ($_SERVER['REMOTE_ADDR'] == '188.163.53.4') {
            print_r($params);
        }
    }*/




}