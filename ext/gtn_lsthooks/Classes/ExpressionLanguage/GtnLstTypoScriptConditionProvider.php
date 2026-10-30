<?php

namespace Gtn\GtnLsthooks\ExpressionLanguage;

use Gtn\GtnLsthooks\Utils\Utils;
use TYPO3\CMS\Core\ExpressionLanguage\AbstractProvider;
use TYPO3\CMS\Core\ExpressionLanguage\RequestWrapper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class GtnLstTypoScriptConditionProvider extends AbstractProvider
{

    public function __construct() {

        // not done and not used. used as TypoScript conditions for powermail
//        $this->expressionLanguageVariables = [
//            'powermailCourseIsOnline' => $this->powermailCourseIsOnline(),
//        ];

        $this->expressionLanguageProviders = [
            GtnLstConditionsFunctionsProvider::class,
        ];

    }

    public function powermailCourseIsOnline()
    {
        // get data from the request
        // get course data
        // check if this course is online
//        $request = GeneralUtility::makeInstance(RequestWrapper::class, $GLOBALS['TYPO3_REQUEST'] ?? null);
//        $utils = GeneralUtility::makeInstance(Utils::class);
//        $courseData = $utils->getCourseData();
    }

}