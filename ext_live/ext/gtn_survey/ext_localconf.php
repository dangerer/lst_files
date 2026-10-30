<?php
defined('TYPO3') || die('Access denied.');

#if (TYPO3 == "BE") {
	//$pageRenderer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
	//$pageRenderer->loadRequireJsModule('TYPO3/CMS/gtn_survey/GraphModule');
#}


\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'GtnSurvey',
	'Fesurvey',
	[
		\GTN\GtnSurvey\Controller\SurveyController::class => 'list,show,processAnswer,sendAnswerMail,sendConfirmation'
	],
	// non-cacheable actions
	[

		\GTN\GtnSurvey\Controller\SurveyController::class => 'list,processAnswer'
	]
);

// wizards
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
	'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    fesurvey {
                        iconIdentifier = gtn_survey-plugin-fesurvey
                        title = LLL:EXT:gtn_survey/Resources/Private/Language/locallang_db.xlf:tx_gtn_survey_fesurvey.name
                        description = LLL:EXT:gtn_survey/Resources/Private/Language/locallang_db.xlf:tx_gtn_survey_fesurvey.description
                        tt_content_defValues {
                            CType = list
                            list_type = gtnsurvey_fesurvey
                        }
                    }
                }
                show = *
            }
       }'
);
#$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);

/*$iconRegistry->registerIcon(
	'gtn_survey-plugin-fesurvey',
	\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
	['source' => 'EXT:gtn_survey/Resources/Public/Icons/user_plugin_fesurvey.svg']
);;*/
