<?php
namespace Gtn\GtnLsthooks\ViewHelpers;


use Gtn\GtnLsthooks\Utils\Utils;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class SelectedRegionViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper {

    public function initializeArguments()
    {
    }

	/**
	 * @return string
	 */
	public function render() {

        $utils = GeneralUtility::makeInstance(Utils::class);
        $selectedRegion = $utils->getSelectedRegion();
        return $selectedRegion;
	}

}