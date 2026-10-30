<?php
namespace Gtn\GtnLsthooks\ViewHelpers;


use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class SelectedRegionViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper {

    public function initializeArguments()
    {
    }

	/**
	 * @return string
	 */
	public function render() {

	    // region from user's data is MAIN
/*        if ($GLOBALS["TSFE"]->fe_user->user["uid"] > 0) {
            $selectedRegion = $GLOBALS["TSFE"]->fe_user->user['lst_region'];
        } else {
            $selectedRegion = ($_COOKIE['selectedRegion'] ? $_COOKIE['selectedRegion'] : 0);
        }*/
        // region frm Cookie is MAIN
        $selectedRegion = ($_COOKIE['selectedRegion'] ? $_COOKIE['selectedRegion'] : 0);
        if (!$selectedRegion && $GLOBALS["TSFE"]->fe_user->user["uid"] > 0) {
            $selectedRegion = $GLOBALS["TSFE"]->fe_user->user['lst_region'];
        }
        if ($selectedRegion === 'null') // it is possible from JS
            $selectedRegion = 0;
        return $selectedRegion;
	}

}