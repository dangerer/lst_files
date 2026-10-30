<?php
namespace Gtn\GtnLsthooks\ExpressionLanguage;

use Gtn\GtnLsthooks\Utils\Utils;
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
            $utils = GeneralUtility::makeInstance(Utils::class);
            $selectedRegion = $utils->getSelectedRegion();
            $selectedRegion = (string)$selectedRegion;
            $groupUids = GeneralUtility::trimExplode(',', $groups);
            if (in_array($selectedRegion, $groupUids)) {
                $result = TRUE;
            }
            return $result;

        });
    }


}