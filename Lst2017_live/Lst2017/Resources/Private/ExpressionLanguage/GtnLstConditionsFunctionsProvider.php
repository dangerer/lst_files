<?php
namespace Gtn\GtnLsthooks\ExpressionLanguage;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class GtnLstConditionsFunctionsProvider implements ExpressionFunctionProviderInterface
{

    public function __construct() {

    }

    /**
     * @return ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        return [
            $this->getRegionSelected(),
        ];
    }

    protected function getRegionSelected(): ExpressionFunction
    {
        return new ExpressionFunction('regionSelected', function () {
            // Not implemented, we only use the evaluator
        }, function ($arguments, $groups) {
            $tsfe = $GLOBALS['TSFE'] ?? null;
            if (!$tsfe) { // only for Frontend!!!!
                return false;
            }
            $result = FALSE;
            // region from user's data is MAIN
            /*if ($GLOBALS["TSFE"]->fe_user->user["uid"] > 0) {
                $selectedRegion = $GLOBALS["TSFE"]->fe_user->user['lst_region'];
            } else {
                $selectedRegion = ($_COOKIE['selectedRegion'] ? $_COOKIE['selectedRegion'] : 0);
            }*/
            // region frm Cookie is MAIN
            $selectedRegion = ($_COOKIE['selectedRegion'] ? $_COOKIE['selectedRegion'] : 0);
            if (!$selectedRegion && $GLOBALS["TSFE"]->fe_user->user["uid"] > 0) {
                $selectedRegion = $GLOBALS["TSFE"]->fe_user->user['lst_region'];
            }
            if ($selectedRegion === 'null') {// it is possible from JS
                $selectedRegion = 0;
            }
            $selectedRegion = (string)$selectedRegion;
            $groupUids = GeneralUtility::trimExplode(',', $groups);
            if (in_array($selectedRegion, $groupUids)) {
                $result = TRUE;
            }
            return $result;

        });
    }


}