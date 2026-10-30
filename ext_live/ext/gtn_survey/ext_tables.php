<?php
defined('TYPO3') || die('Access denied.');


\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'GtnSurvey',
	'Fesurvey',
	'FE Survey'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'GtnSurvey',
	'Fesurvey',
	'LLL:EXT:gtn_survey/Resources/Private/Language/locallang_be.xlf:plugin.fesurvey.title',
	null, 'Survey'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['gtnsurvey_fesurvey'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
	'gtnsurvey_fesurvey',
	'FILE:EXT:gtn_survey/Configuration/FlexForms/Fesurvey.xml'
);

/*
$extensionName = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase('gtn_survey');
$pluginSignature = strtolower($extensionName) . '_fesurvey';

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:gtn_survey/Configuration/FlexForms/flexform_fesurvey.xml');
*/
/*
if (TYPO3 === 'BE') {

	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'GTN.GtnSurvey',
		'web', // Make module a submodule of 'web'
		'besurvey', // Submodule key
		'', // Position
		[
			'SurveyAnalysis' => 'list, showSurvey',

		],
		[
			'access' => 'user,group',
			'icon' => 'EXT:gtn_survey/Resources/Public/Icons/user_mod_surveyanalysis.svg',
			'labels' => 'LLL:EXT:gtn_survey/Resources/Private/Language/locallang_besurvey.xlf',
		]
	);

}
*/
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('gtn_survey', 'Configuration/TypoScript', 'Surveys');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_gtnsurvey_domain_model_participant', 'EXT:gtn_survey/Resources/Private/Language/locallang_csh_tx_gtnsurvey_domain_model_participant.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_gtnsurvey_domain_model_participant');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_gtnsurvey_domain_model_answer', 'EXT:gtn_survey/Resources/Private/Language/locallang_csh_tx_gtnsurvey_domain_model_answer.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_gtnsurvey_domain_model_answer');


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_gtnsurvey_domain_model_question', 'EXT:gtn_survey/Resources/Private/Language/locallang_csh_tx_gtnsurvey_domain_model_question.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_gtnsurvey_domain_model_question');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_gtnsurvey_domain_model_questiongroup', 'EXT:gtn_survey/Resources/Private/Language/locallang_csh_tx_gtnsurvey_domain_model_questiongroup.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_gtnsurvey_domain_model_questiongroup');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_gtnsurvey_domain_model_survey', 'EXT:gtn_survey/Resources/Private/Language/locallang_csh_tx_gtnsurvey_domain_model_survey.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_gtnsurvey_domain_model_survey');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_gtnsurvey_domain_model_grouptype', 'EXT:gtn_survey/Resources/Private/Language/locallang_csh_tx_gtnsurvey_domain_model_grouptype.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_gtnsurvey_domain_model_grouptype');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_gtnsurvey_domain_model_questiontype', 'EXT:gtn_survey/Resources/Private/Language/locallang_csh_tx_gtnsurvey_domain_model_questiontype.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_gtnsurvey_domain_model_questiontype');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_gtnsurvey_domain_model_useranswer', 'EXT:gtn_survey/Resources/Private/Language/locallang_csh_tx_gtnsurvey_domain_model_useranswer.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_gtnsurvey_domain_model_useranswer');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_gtnsurvey_domain_model_section', 'EXT:gtn_survey/Resources/Private/Language/locallang_csh_tx_gtnsurvey_domain_model_section.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_gtnsurvey_domain_model_section');

