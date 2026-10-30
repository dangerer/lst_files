<?php
namespace Gtn\GtnLsthooks\ViewHelpers;

use Gtn\GtnLsthooks\Utils\Utils;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class ContentCategoryConditionViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper {

    public function initializeArguments()
    {
        $this->registerArgument('contentUid', 'integer', 'Content Uid', true);
    }
	/**
	 * @param integer $contentUid
	 * @return boolean
	 */
	public function render() {

	    $contentUid = $this->arguments["contentUid"];
		$result = true;
		//$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['gtn_lsthooks']);
//        $extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('backend')["gtn_lsthooks"];
        $extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get("gtn_lsthooks");
//        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($extConf, 'ContentCategoryConditionViewHelper.php:24', 4); exit; // !!!!!!!!!! delete it
        $utils = GeneralUtility::makeInstance(Utils::class);
        $selectedRegion = $utils->getSelectedRegion();
		// get categories for content element
		if ($contentUid > 0) {
			$sql = 'SELECT * FROM sys_category_record_mm WHERE uid_foreign='.intval($contentUid).' AND tablenames = \'tt_content\'';
			$categoriesRes = $GLOBALS['TYPO3_DB']->sql_query($sql);
			$contentCategories = array();
			while($res = $categoriesRes->fetch_object()) {
                $contentCategories[] = $res->uid_local;
            }
            //DebuggerUtility::var_dump($categories, 'content Categories:'. $contentUid);
			if (count($contentCategories) > 0) {
				$sql2 = 'SELECT * FROM sys_category WHERE parent = '.intval($extConf['regionRootCategoryUid']);
				$regionCategoriesRes = $GLOBALS['TYPO3_DB']->sql_query($sql2);
				while($res = $regionCategoriesRes->fetch_object()) {
                    $regionCategories[] = $res->uid;
                }
				// add 'No region' to this list
				$regionCategories[] = $extConf['noRegionSelectedCategoryUid'];
				//DebuggerUtility::var_dump($regionCategories, 'region Categories for root:'. $extConf['regionRootCategoryUid']);
				// if content has region category in selected list
				if (count(array_intersect($regionCategories, $contentCategories)) > 0 ) {
					// also hide CE if the user has no selected region
					if (!$selectedRegion || !in_array($selectedRegion, $contentCategories))
						$result = false;
				}
				// if CE has 'no selected region' category and the user has not selected region. look ext configuration
				if (!$result && !$selectedRegion && in_array($extConf['noRegionSelectedCategoryUid'], $contentCategories))
					$result = true;
			}
		}
		return $result;

	}





}