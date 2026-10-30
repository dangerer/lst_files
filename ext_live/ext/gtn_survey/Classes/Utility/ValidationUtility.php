<?php
/**
 * Created by PhpStorm.
 * User: sigi
 * Date: 9/2/2019
 * Time: 9:04 AM
 */

namespace GTN\GtnSurvey\Utility;


class ValidationUtility
{
    public static function validateEmail($validatorResolver, $email) {
        $conjunctionValidatorEmail = $validatorResolver->createValidator('Conjunction');
        $conjunctionValidatorEmail->addValidator($validatorResolver->createValidator('NotEmptyValidator'));
        $conjunctionValidatorEmail->addValidator($validatorResolver->createValidator('EmailAddressValidator'));
        return $conjunctionValidatorEmail->validate($email);
    }

    public static function validateNumber($validatorResolver, $numberStr) {
        $conjunctionValidatorNumber = $validatorResolver->createValidator('Conjunction');
        $conjunctionValidatorNumber->addValidator($validatorResolver->createValidator('NumberValidator'));
        return $conjunctionValidatorNumber->validate($numberStr);
    }

    public static function validateDatetime($validatorResolver, $dateStr) {
        $conjunctionValidatorDatetime = $validatorResolver->createValidator('Conjunction');
        $conjunctionValidatorDatetime->addValidator($validatorResolver->createValidator('DateTime'));
        return $conjunctionValidatorDatetime->validate($dateStr);
    }

    public static function validateNotEmpty($validatorResolver, $str) {
        $conjunctionValidatorNotEmpty = $validatorResolver->createValidator('Conjunction');
        $conjunctionValidatorNotEmpty->addValidator($validatorResolver->createValidator('NotEmptyValidator'));
        return $conjunctionValidatorNotEmpty->validate($str);
    }

}