<?php
namespace Gtn\GtnLsthooks\Hooks;


use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class DataHandler
{




    /**
     * @var array
     */
    private $tsSettings;

    /**
     * @var array
     */
    private $tsSettingsSrfeuserregister;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    protected $persistenceManager = null;

    function __construct() {
        $configurationManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
        $extbaseFrameworkConfiguration = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
       # $this->tsSettings = $extbaseFrameworkConfiguration['plugin.']['tx_gtnlsthooks.']['settings.'];
        $this->tsSettingsSrfeuserregister = $extbaseFrameworkConfiguration['plugin.']['tx_srfeuserregister_pi1.'];
        $this->persistenceManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class);
    }


    public function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, &$pObj) {
        if ($table == 'fe_users') {
            if ($status == 'update') {
                // if old "disable' == 1 and new "disable" == 0
                if (array_key_exists('disable', $fieldArray) && $fieldArray['disable'] === '0' && $pObj->checkValue_currentRecord['disable'] === 1) {
                    /** @var FrontendUserRepository $feUserRepository */
//                    $feUserRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(FrontendUserRepository::class);
//                    $querySettings = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(Typo3QuerySettings::class);
//                    $querySettings->setRespectStoragePage(false)->setRespectSysLanguage(false)->setIgnoreEnableFields(true); //now the user is disabled?
//                    $feUserRepository->setDefaultQuerySettings($querySettings);
//                    $feUser = $feUserRepository->findByUid($id);
//                    $query = $feUserRepository->createQuery();
//                    $sql = 'SELECT * FROM fe_users WHERE uid = '.intval($id).' AND deleted = 0 ';
//                    $query->statement($sql);
//                    $feUser = $query->execute()->getFirst();
                    $feUser = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'fe_users', ' uid = '.intval($id).' AND deleted = 0 ')[0];
                    if ($feUser['language'] != '') {
                        $langKey = strtolower($feUser['language']);
                    } else {
                        $langKey = 'de';
                    }
//                    DebuggerUtility::var_dump($feUser);
                    $this->sendEmailAfterFeUserEnabled($feUser, $langKey);
                }
            }
        }
    }

/*    public function processDatamap_afterDatabaseOperations($status, $table, $id, &$fieldArray, &$pObj) {
        echo '<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>!!!!!!!!!!!!!!!!!!!!!!!<br><br>';
        DebuggerUtility::var_dump($status, 'status');
//        DebuggerUtility::var_dump($table, 'table');
//        DebuggerUtility::var_dump($fieldArray, 'fieldarr');
//        DebuggerUtility::var_dump($pObj, 'pObj');
    }

    public function processCmdmap_afterDatabaseOperations($status, $table, $id, &$fieldArray, &$pObj) {
        echo '<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>!!!!!!!!!!!!!!!!!!!!!!!<br><br>';
        DebuggerUtility::var_dump($status, 'status');
    }*/

    public function sendEmailAfterFeUserEnabled($feUser, $langKey = '') {
        /* @var \Gtn\ElcEtapas3\Utilites\MailService $mailService */
        $mailService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Gtn\\GtnLsthooks\\Utils\\MailService');

//        if ($feUser instanceof \TYPO3\CMS\Extbase\Domain\Model\FrontendUser) // email from owner
//            $recipient = array($feUser->getEmail() => $feUser->getFirstName().' '.$feUser->getLastName());
        if (is_array($feUser) && $feUser['email'] != '')
            $recipient = array($feUser['email'] => $feUser['first_name'].' '.$feUser['last_name']);
        else
            return ;

        $cObj = new ContentObjectRenderer();
        // settings from Powermail plugin
        $sender = array($this->tsSettingsSrfeuserregister['email.']['from'] => $this->tsSettingsSrfeuserregister['email.']['fromName']);

        $linkToHome = $cObj->typoLink_URL(array('parameter'=>1, 'forceAbsoluteUrl' => 1));
        $linkToLogin = $cObj->typoLink_URL(array('parameter'=>$this->tsSettingsSrfeuserregister['loginPID'], 'forceAbsoluteUrl' => 1));
        $variables = array(
            'user' => $feUser,
            'linkToHome' => $linkToHome,
            'linkToLogin' => $linkToLogin
        );
        if ($langKey === '') {
            $langKey = 'de';
        }
//DebuggerUtility::var_dump($langKey);
        /** @var $languageFactory \TYPO3\CMS\Core\Localization\LocalizationFactory */
        $languageFactory = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Localization\LocalizationFactory::class);
        $allStrings = $languageFactory->getParsedData('EXT:gtn_lsthooks/Resources/Private/Language/locallang.xlf', $langKey);
//        DebuggerUtility::var_dump($allStrings);

        $subject = ($allStrings[$langKey]['emailFeUserEnabled.subject'][0]['target'] ? $allStrings[$langKey]['emailFeUserEnabled.subject'][0]['target'] : $allStrings['default']['emailFeUserEnabled.subject'][0]['source']);
        // add region to subject
        $regionTitle = '';
        $region = 0;
        if ($feUser['lst_region'] > 0) {
            $region = $feUser['lst_region'];
        }
        if ($region > 0) {
            $categoryRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Gtn\GtnLsthooks\Domain\Repository\CategoryRepository::class);
            $selectedCategory = $categoryRepository->findByUid($region);
            if ($selectedCategory)
                $regionTitle = $selectedCategory->getTitle();
        }
        $subject = sprintf($subject, $regionTitle);
//        \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('emailFeUserEnabled.subject', 'gtn_lsthooks');
//        $text = LocalizationUtility::translate('emailFeUserEnabled', 'gtn_lsthooks');
        $text = ($allStrings[$langKey]['emailFeUserEnabled'][0]['target'] ? $allStrings[$langKey]['emailFeUserEnabled'][0]['target'] : $allStrings['default']['emailFeUserEnabled'][0]['source']);
//        DebuggerUtility::var_dump($subject);
//        DebuggerUtility::var_dump($text);

        // DO NOT SEND the email if it is TEXT server
        if (strpos($linkToHome, 'test.') !== false) {
            return true;
        }

        $mailService->sendTemplateEmail(
            $recipient,
            $sender,
            $subject,
            $text,
            $variables,
            null,
            true
        );
    }


}
