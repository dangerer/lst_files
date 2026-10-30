<?php

namespace GTN\GtnSurvey\Domain\Validation\Validator;

use Pixelant\PxaSurvey\Domain\Model\Question;
use Pixelant\PxaSurvey\Domain\Model\Survey;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Validation\Error;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;
use TYPO3\CMS\Core\Utility\DebugUtility;


/**
 * Validate answers from user
 *
 * @package Pixelant\PxaSurvey\Domain\Validation
 */
class SurveyAnswerValidator extends AbstractValidator
{

    public function isValid($value) : void
    {
        $validatorResolver = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Validation\\ValidatorResolver');

        $arguments = GeneralUtility::_POST('tx_gtnsurvey_fesurvey');
        $participantInfo = $arguments['participant'];

        if(!!$this->settings['mailRequired']) {
            if(!empty(\GTN\GtnSurvey\Utility\ValidationUtility::validateEmail($validatorResolver, $participantInfo['mail'])->getErrors())) {
                $this->addError($this->translate('fe.error.mail.isMail'), 1510659341372);
            }
        }
    }

    /**
     * Translate wrapper
     * @param string $key
     * @return string
     */
    protected function translate(string $key)
    {
        return LocalizationUtility::translate($key, 'GtnSurvey');
    }

}
