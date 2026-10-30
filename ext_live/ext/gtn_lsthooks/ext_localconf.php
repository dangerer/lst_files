<?php
if (!defined('TYPO3')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'GtnLsthooks',
	'Ajax',
	array(
		\Gtn\GtnLsthooks\Controller\AjaxController::class => 'searchRegionsByZip',
	),
	// non-cacheable actions
	array(
        	\Gtn\GtnLsthooks\Controller\AjaxController::class => 'searchRegionsByZip',
	)
);

/**
 * Include page and user TS
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:gtn_lsthooks/Configuration/TSConfig/Page.ts">'
);


// hook to tt_content by cookie
//$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_content_content.php']['modifyDBRow']['gtn_lsthooks'] = 'Gtn\\GtnLsthooks\\Hooks\\Content';

// hook for registrations
//$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_content_content.php']['modifyDBRow']['gtn_lsthooks'] = 'Gtn\\GtnLsthooks\\Hooks\\Content';

// use TS conditions instead
#$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] =
#    \Vendor\Package\Hook\DataHandlerHook::class . '->postProcessClearCache';
$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['sr_feuser_register']['tx_srfeuserregister_pi1']['registrationProcess'][] = \Gtn\GtnLsthooks\Hooks\SrFeUserRegister::class;
$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['sr_feuser_register']['tx_srfeuserregister_pi1']['confirmRegistrationClass'][] = \Gtn\GtnLsthooks\Hooks\SrFeUserRegister::class;
$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['sr_feuser_register']['tx_srfeuserregister_pi1']['model'][] = \Gtn\GtnLsthooks\Hooks\SrFeUserRegister::class;
$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['sr_email_subscribe']['tx_sremailsubscribe_pi1']['model'][] = \Gtn\GtnLsthooks\Hooks\SrFeUserRegister::class;
$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['sr_email_subscribe']['tx_sremailsubscribe_pi1']['registrationProcess'][] = \Gtn\GtnLsthooks\Hooks\SrFeUserRegister::class;

// pageRepository hooks: used for redirected by selected region
// work only with manual links :-(
//$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['tslib_fe-PostProc'][] = 'Gtn\\GtnLsthooks\\Hooks\\TypoScriptFrontendController->redirectByTS';
// we need hook only AFTER page generation, because we need initialized some properties. so, use this place of hook:
//$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] = 'Gtn\\GtnLsthooks\\Hooks\\TypoScriptFrontendController->redirectByTS';
// for keeping selected language for users
// disabled 01.05.2021 SZ. Moved to input of fe_manager form
//$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] = 'Gtn\\GtnLsthooks\\Hooks\\TypoScriptFrontendController->keepUserLanguage';

// datahandler hooks
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \Gtn\GtnLsthooks\Hooks\DataHandler::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = \Gtn\GtnLsthooks\Hooks\DataHandler::class;
//['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all']

// Pages by selected region
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_page.php']['getPage'][] = \Gtn\GtnLsthooks\Hooks\PageRepository::class;
//$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/tslib/class.tslib_menu.php']['filterMenuPages'][] = \Gtn\GtnLsthooks\Hooks\AbstractMenuContentObject::class;

// after login
#$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['postUserLookUp'][] = 'Gtn\\GtnLsthooks\\Hooks\\FeLogin->regionAfterLogin';
// migrated to event listener (Configuration/Services.yaml -> LoginConfirmedRegionListener)

// email: forgot password
//$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['felogin']['forgotPasswordMail'][] = 'Gtn\\GtnLsthooks\\Hooks\\FeLogin->emailMarkers';

// content replacer
//$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['tslib_fe-contentStrReplace'][] = 'Gtn\\GtnLsthooks\\Hooks\\Content->strReplacer';

// for tx_news
$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['Domain/Repository/AbstractDemandedRepository.php']['findDemanded'][] = \Gtn\GtnLsthooks\Hooks\News::class . '->modifyDemand';
// add custom fields to news
$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['classes']['Domain/Model/News']['gtn_lsthooks'] = 'gtn_lsthooks';

// for direct_mail recipient list
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['direct_mail']['mod2']['cmd_compileMailGroup'][] =  \Gtn\GtnLsthooks\Hooks\DirectMailHooks::class;
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['direct_mail']['mod3']['cmd_compileMailGroup'][] =   \Gtn\GtnLsthooks\Hooks\DirectMailHooks::class;

// powermail RAN forms!!!!
/** @var TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
/*$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\SignalSlot\Dispatcher');

// before form render
$signalSlotDispatcher->connect(
    'In2code\Powermail\Controller\FormController',
    'formActionBeforeRenderView',
    'Gtn\GtnLsthooks\Hooks\PowermailHooks',
    'formBeforeRender',
    FALSE
);
// work with mail after saving to DB
$signalSlotDispatcher->connect(
    'In2code\Powermail\Controller\FormController',
    'createActionAfterMailDbSaved',
    'Gtn\GtnLsthooks\Hooks\PowermailHooks',
    'afterSaveDB',
    FALSE
);
// before preview render
$signalSlotDispatcher->connect(
    'In2code\Powermail\Controller\FormController',
    'confirmationActionBeforeRenderView',
    'Gtn\GtnLsthooks\Hooks\PowermailHooks',
    'formBeforePreviewRender',
    FALSE
);

// before creating
$signalSlotDispatcher->connect(
    'In2code\Powermail\Controller\FormController',
    'createActionBeforeRenderView',
    'Gtn\GtnLsthooks\Hooks\PowermailHooks',
    'createBeforePreviewRender',
    FALSE
);
// before send email
$signalSlotDispatcher->connect(
    'In2code\Powermail\Domain\Service\Mail\SendMailService',
    'sendTemplateEmailBeforeSend',
    'Gtn\GtnLsthooks\Hooks\PowermailHooks',
    'getFilesForAttach',
    FALSE
);

// for getting sender email from FE-user data (looks like here is a bug in powermail)
$signalSlotDispatcher->connect(
    'In2code\Powermail\Domain\Service\Mail\SenderMailPropertiesService',
    'getSenderEmail',
    'Gtn\GtnLsthooks\Hooks\PowermailHooks',
    'getSenderEmail',
    FALSE
);
// the same for 'name'
$signalSlotDispatcher->connect(
    'In2code\Powermail\Domain\Service\Mail\SenderMailPropertiesService',
    'getSenderName',
    'Gtn\GtnLsthooks\Hooks\PowermailHooks',
    'getSenderName',
    FALSE
);*/

// for custom field in femanager
/*$extbaseObjectContainer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class);
$extbaseObjectContainer->registerImplementation(
    \In2code\Femanager\Controller\NewController::class,
    \Gtn\GtnLsthooks\Controller\LstUserNewController::class
);
$extbaseObjectContainer->registerImplementation(
    \In2code\Femanager\Controller\EditController::class,
    \Gtn\GtnLsthooks\Controller\LstUserEditController::class
);*/
// also for PHP 7
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Extbase\Mvc\Controller\Argument::class] = [
'className' => \Gtn\GtnLsthooks\Xclass\Extbase\Mvc\Controller\Argument::class];


/*
if (TYPO3 === 'BE') {
    $icons = [
        'ran-international-request-item' => 'ran-international-request-item.svg',
        'ran-international-request-approve' => 'ran-international-request-approve.svg',
        'ran-international-request-decline' => 'ran-international-request-decline.svg',
    ];
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    foreach ($icons as $identifier => $path) {
        if (!$iconRegistry->isRegistered($identifier)) {
            $iconRegistry->registerIcon(
                $identifier,
                \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
                ['source' => 'EXT:gtn_lsthooks/Resources/Public/Icons/' . $path]
            );
        }
    }
}*/

// Hook for adding a button to approve the International RAN request
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/class.db_list_extra.inc']['actions'][] = 'Gtn\GtnLsthooks\Hooks\RecordListHook';

// add custom buttons (for RAN approve/decline) into the BE form:
$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1727875406995] = [
    'nodeName' => 'ranActionButtons',
    'priority' => 40,
    'class' => Gtn\GtnLsthooks\Form\Element\RanActionButtons::class,
];

// own user's properties for FeManager
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\In2code\Femanager\Domain\Model\User::class] = [
    'className' => \Gtn\GtnLsthooks\Domain\Model\LstUser::class,
];
// custom Controller classes for FeManager
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\In2code\Femanager\Controller\NewController::class] = [
    'className' => \Gtn\GtnLsthooks\Controller\LstUserNewController::class,
];
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\In2code\Femanager\Controller\EditController::class] = [
    'className' => \Gtn\GtnLsthooks\Controller\LstUserEditController::class,
];

// xClass to add funciton into Powermail Mail PlaintextService
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][In2code\Powermail\Domain\Service\Mail\PlaintextService::class] = [
    'className' => \Gtn\GtnLsthooks\Xclass\Powermail\Mail\PlaintextService::class,
];