<?php
namespace Gtn\GtnLsthooks\Condition;


use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class FeLanguageSelectedCondition extends \TYPO3\CMS\Core\Configuration\TypoScript\ConditionMatching\AbstractCondition {

	/**
	 * Evaluate condition: the FE user selected this language or not? Look also hook for FeLogin
	 *
	 * @param array $conditionParameters
	 * @return bool
	 */
	public function matchCondition(array $conditionParameters) {
		$result = FALSE;
		if (isset($GLOBALS["TSFE"]->fe_user) && $GLOBALS["TSFE"]->fe_user->user["uid"] > 0) {
		    // only for fe users
            if ($GLOBALS["TSFE"]->fe_user->user["fe_language"] > 0) {
                $selectedLang = $GLOBALS["TSFE"]->fe_user->user['fe_language'];
            }
            if (GeneralUtility::_GP('L') === null && $GLOBALS["TSFE"]->fe_user->user['fe_language'] == 0) {
                // no any from FE
                $selectedLang = 0;
            }
            if (GeneralUtility::_GP('L') != '' && GeneralUtility::_GP('L') > 0 && GeneralUtility::_GP('L') != $selectedLang) {
                $selectedLang = intval(GeneralUtility::_GP('L'));
            }
            // if force L by Typoscript
            if (GeneralUtility::_GP('fL') == '1')
                $selectedLang = GeneralUtility::_GP('L');
//            if ($_SERVER['REMOTE_ADDR'] == '188.163.53.4') {
//                DebuggerUtility::var_dump($GLOBALS["TSFE"]->fe_user->user['fe_language'], 'from user');
//                DebuggerUtility::var_dump(GeneralUtility::_GP('L'), 'GP');
//                DebuggerUtility::var_dump($selectedLang, 'selected');
//            }
            // change L by new language
//            GeneralUtility::_GETset($selectedLang, 'L'); // disabled SZ: 01.05.2021
        } else  {
            // for not logged in users
            $selectedLang = intval(GeneralUtility::_GP('L'));
        }
        // also we need to compare with URL - if language is chanfging just now
//        if (GeneralUtility::_GP('L') != '' && GeneralUtility::_GP('L') != $selectedLang)
//            return $result; // JUST NOW language is changing. we does not need to use this condition!
		if (!empty($conditionParameters) && substr($conditionParameters[0], 0, 1) === '=') {
			$conditionParameters[0] = trim(substr($conditionParameters[0], 1));
			if ($conditionParameters[0] == $selectedLang) {
				$result = TRUE;
			}
		}
		return $result;
	}

}