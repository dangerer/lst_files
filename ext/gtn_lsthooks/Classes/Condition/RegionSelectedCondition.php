<?php
namespace Gtn\GtnLsthooks\Condition;


use Gtn\GtnLsthooks\Utils\Utils;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

// DEPRECATED. look class GtnLstConditionsFunctionsProvider

class RegionSelectedCondition extends \TYPO3\CMS\Core\Configuration\TypoScript\ConditionMatching\AbstractCondition {

	/**
	 * Evaluate condition
	 *
	 * @param array $conditionParameters
	 * @return bool
	 */
	public function matchCondition(array $conditionParameters) {
		$result = FALSE;
        $utils = GeneralUtility::makeInstance(Utils::class);
        $selectedRegion = $utils->getSelectedRegion();
		if (!empty($conditionParameters) && substr($conditionParameters[0], 0, 1) === '=') {
			$conditionParameters[0] = trim(substr($conditionParameters[0], 1));
			if ($conditionParameters[0] == $selectedRegion) {
				$result = TRUE;
			}
		}
		return $result;
	}

}