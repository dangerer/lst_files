<?php
namespace Gtn\GtnLsthooks\Hooks;

use Gtn\GtnLsthooks\Utils\Utils;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class UtilsForHooks {

    static function checkPageCategory($uids, $secondAttempt = false) {
        if (!is_array($uids)) {
            $uids = array(intval($uids));
        }
        $result = false;
       // $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['gtn_lsthooks']);

//        $extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('backend')["gtn_lsthooks"];
        $extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get("gtn_lsthooks");
        //$backendConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('backend');



        $sqlCats = 'SELECT * FROM sys_category WHERE parent = '.intval($extConf['regionRootCategoryUid']);
        $regionCategoriesRes = $GLOBALS['TYPO3_DB']->sql_query($sqlCats);
        while($res = $regionCategoriesRes->fetch_object()) {
            $regionCategories[] = $res->uid;
        }
        // add 'No region' to this list
        $regionCategories[] = $extConf['noRegionSelectedCategoryUid'];
        // get category from region
        $sql = 'SELECT * FROM sys_category_record_mm crmm                     
                    WHERE crmm.tablenames=\'pages\' 
                          AND crmm.fieldname=\'categories\' 
                          AND crmm.uid_foreign IN ('.implode(',', $uids).') ';
        $pageCategoriesRes = $GLOBALS['TYPO3_DB']->sql_query($sql);
        while ($res = $pageCategoriesRes->fetch_object()) {
            $pageCategories[] = $res->uid_local;
        }
        // only if the page has some 'region' category - next rules will work
        if (isset($pageCategories) && is_array($pageCategories) && is_array($regionCategories) && count(array_intersect($pageCategories, $regionCategories)) > 0) {
            $result = true;
            // get user category
            $utils = GeneralUtility::makeInstance(Utils::class);
            $region = $utils->getSelectedRegion();

            if (!in_array($region, $pageCategories)) {
                $result = false;
            }
            // if page has 'no selected region' category and the user has not selected region. look ext configuration
            if (!$result && !$region && in_array($extConf['noRegionSelectedCategoryUid'], $pageCategories)) {
                $result = true;
            }
        } else {
            // show page if it has no any region category
            $result = true;            

        }
//        if ($_SERVER['REMOTE_ADDR'] == '188.163.53.4' && in_array(90, $uids)) {
//            echo '!!!<br>';
//            print_r($uids);echo '<br>';
//            print_r($pageCategories);echo '<br>';
//            echo $sql;echo '<br>';
//            var_dump($result);echo '<br>';
//            var_dump( $secondAttempt); echo '<br>';
//            echo '<br>';
//            echo $pageCategoriesRes->num_rows.'<br>';
//            echo 'region selected: '.$region.'<br>';
//            var_dump($result);
//            echo '<br>';
//        }

        return $result;
    }

    static function getTreePids($parent = 0, $as_array = true){
        $depth = 999999;
        $queryGenerator = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( 'TYPO3\\CMS\\Core\\Database\\QueryGenerator' );
        $childPids = $queryGenerator->getTreeList($parent, $depth, 0, 1); //Will be a string like 1,2,3
        if ($as_array) {
            $childPids = explode(',',$childPids );
        }
        return $childPids;
    }

}