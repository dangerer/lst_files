<?php
namespace Gtn\GtnLsthooks\Hooks;

use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Frontend\ContentObject\Menu\AbstractMenuFilterPagesHookInterface;

class AbstractMenuContentObject implements AbstractMenuFilterPagesHookInterface {

    /** @var UtilsForHooks UtilsForHooks|null  */
    private $utils = null;

    function __construct() {

    
        /** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager */
        $this->utils = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(UtilsForHooks::class);
    }

    public function processFilter(array &$data, array $banUidArray, $spacer, \TYPO3\CMS\Frontend\ContentObject\Menu\AbstractMenuContentObject $obj) {
        $pageUid = $data['uid'];
        $utils = $this->utils;
        $result = $utils::checkPageCategory($pageUid);
        return $result;
//        return true;
    }

}
