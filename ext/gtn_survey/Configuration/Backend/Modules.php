<?php

return [
	'web_besurvey' => [
		'parent' => 'web',
		'position' => [],
		'access' => 'user,group',
		'iconIdentifier' => 'module-besurvey',
		'labels' => 'LLL:EXT:gtn_survey/Resources/Private/Language/locallang_besurvey.xlf',
		'extensionName' => 'GtnSurvey',
		'controllerActions' => [
			\GTN\GtnSurvey\Controller\SurveyAnalysisController::class => [
				'list', 'showSurvey'
			],
		],
	],
];
