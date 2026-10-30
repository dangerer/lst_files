<?php

namespace Gtn\GtnLsthooks\ExpressionLanguage;

use TYPO3\CMS\Core\ExpressionLanguage\AbstractProvider;

class GtnLstTypoScriptConditionProvider extends AbstractProvider
{

    public function __construct() {

        $this->expressionLanguageProviders = [
            GtnLstConditionsFunctionsProvider::class,
        ];

    }

}