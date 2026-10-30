<?php
namespace Gtn\GtnLsthooks\Controller;
use \Gtn\GtnLsthooks\Domain\Model\LstUser as User;

class LstUserEditController extends \In2code\Femanager\Controller\EditController {

    /**
     * @return void
     */
    public function initializeUpdateAction()
    {
        if ($this->arguments->hasArgument('user')) {
            // Workaround to avoid php7 warnings of wrong type hint.
            /** @var \Gtn\GtnLsthooks\Domain\Model\LstUser $user */
            $user = $this->arguments['user'];
            $user->setDataType(\Gtn\GtnLsthooks\Domain\Model\LstUser::class);
        }
    }

    /**
     * @param User $user
     * @TYPO3\CMS\Extbase\Annotation\Validate("In2code\Femanager\Domain\Validator\ServersideValidator", param="user")
     * @TYPO3\CMS\Extbase\Annotation\Validate("In2code\Femanager\Domain\Validator\PasswordValidator", param="user")
     * @TYPO3\CMS\Extbase\Annotation\Validate("In2code\Femanager\Domain\Validator\CaptchaValidator", param="user")
     */
    public function updateAction(\In2code\Femanager\Domain\Model\User $user, string $captcha = null) {
        // if the user changes own 'newsletter language' - we need to create a new record in tt_adress (in new PID)
        if ($user->_isDirty('feLanguage')) {
            $pidsByLang = [
                0 => 114,
                1 => 335,
            ];
            if (array_key_exists($user->getFeLanguage(), $pidsByLang)) {
                $pid = $pidsByLang[$user->getFeLanguage()];
                // check existing on any language
                $existingAll = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', 'tt_address',
                                    ' email = \'' . $user->getEmail() . '\' 
                                        AND deleted = 0 
                                        AND hidden = 0');
                // check existing by new language
                $existing = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', 'tt_address',
                    ' email = \'' . $user->getEmail() . '\'
                        AND pid = '.$pid.' 
                        AND deleted = 0 
                        AND hidden = 0');
                if ($existingAll && !$existing) {
                    // new record
                    $data = array(
                        'pid' => $pid,
                        'email' => $user->getEmail(),
                        'name' => trim($user->getFirstName() . ' ' . $user->getLastName()),
                        'first_name' => $user->getFirstName(),
                        'last_name' => $user->getLastName(),
                        'gender' => $user->getGender(),
                        'company' => $user->getCompany(),
                        'zip' => $user->getZip(),
                        'static_info_country' => $user->getCountry(),
                        'module_sys_dmail_html' => 1,
                        'terms_acknowledged' => 1,
                        'tstamp' => time(),
                        'crdate' => time(),
                    );
                    $GLOBALS['TYPO3_DB']->exec_INSERTquery('tt_address', $data);
                };
            }
        }
        return parent::updateAction($user);
    }
}