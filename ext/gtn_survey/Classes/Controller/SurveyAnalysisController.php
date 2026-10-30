<?php

namespace GTN\GtnSurvey\Controller;

use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use GTN\GtnSurvey\Domain\Model\UserAnswer;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Page\AssetCollector;

/***
 *
 * This file is part of the "Surveys" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019
 *
 ***/

/**
 * SurveyController
 */
#[AsController]
class SurveyAnalysisController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

	/**
	 * surveyRepository
	 *
	 * @var \GTN\GtnSurvey\Domain\Repository\SurveyRepository
	 */
	protected $surveyRepository = null;

	/**
	 * questionRepository
	 *
	 * @var \GTN\GtnSurvey\Domain\Repository\QuestionRepository
	 */
	protected $questionRepository = null;

	/**
	 * questionRepository
	 *
	 * @var \GTN\GtnSurvey\Domain\Repository\QuestionGroupRepository
	 */
	protected $questionGroupRepository = null;

	/**
	 * questionRepository
	 *
	 * @var \GTN\GtnSurvey\Domain\Repository\AnswerRepository
	 */
	protected $answerRepository = null;


	/**
	 * questionRepository
	 *
	 * @var \GTN\GtnSurvey\Domain\Repository\ParticipantRepository
	 */
	protected $participantRepository = null;
	/**
	 * questionRepository
	 *
	 * @var \GTN\GtnSurvey\Domain\Repository\UserAnswerRepository
	 */
	protected $userAnswerRepository = null;

	private ModuleTemplate $moduleTemplate;

	protected PageRenderer $pageRenderer;

	protected AssetCollector $assetCollector;


	/**
	 * @param ModuleTemplateFactory $moduleTemplateFactory
	 * @param AssetCollector $assetCollector
	 * @param \GTN\GtnSurvey\Domain\Repository\SurveyRepository $surveyRepository
	 * @param \GTN\GtnSurvey\Domain\Repository\QuestionRepository $questionRepository
	 * @param \GTN\GtnSurvey\Domain\Repository\QuestionGroupRepository $questionGroupRepository
	 * @param \GTN\GtnSurvey\Domain\Repository\AnswerRepository $answerRepository
	 * @param \GTN\GtnSurvey\Domain\Repository\ParticipantRepository $participantRepository
	 * @param \GTN\GtnSurvey\Domain\Repository\UserAnswerRepository $userAnswerRepository
	 */
	public function __construct(ModuleTemplateFactory                                    $moduleTemplateFactory,
								PageRenderer                                             $pageRenderer,
								AssetCollector                                           $assetCollector,
								\GTN\GtnSurvey\Domain\Repository\SurveyRepository        $surveyRepository,
								\GTN\GtnSurvey\Domain\Repository\QuestionRepository      $questionRepository,
								\GTN\GtnSurvey\Domain\Repository\QuestionGroupRepository $questionGroupRepository,
								\GTN\GtnSurvey\Domain\Repository\AnswerRepository        $answerRepository,
								\GTN\GtnSurvey\Domain\Repository\ParticipantRepository   $participantRepository,
								\GTN\GtnSurvey\Domain\Repository\UserAnswerRepository    $userAnswerRepository) {
		$this->moduleTemplateFactory = $moduleTemplateFactory;
		$this->pageRenderer = $pageRenderer;
		$this->assetCollector = $assetCollector;
		$this->surveyRepository = $surveyRepository;
		$this->questionRepository = $questionRepository;
		$this->questionGroupRepository = $questionGroupRepository;
		$this->answerRepository = $answerRepository;
		$this->participantRepository = $participantRepository;
		$this->userAnswerRepository = $userAnswerRepository;
	}

	public function initializeAction(): void {
		$this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);


		$this->assetCollector->addStyleSheet(
			'gtn-survey-bootstrap-css',
			'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',
		);
		$this->assetCollector->addStyleSheet(
			'gtn-survey-styles-css',
			'EXT:gtn_survey/Resources/Public/css/styles.css',
		);


		$this->assetCollector->addJavaScript(
			'gtn-survey-jquery',
			'https://code.jquery.com/jquery-3.7.1.min.js',
		);


		$this->assetCollector->addJavaScript(
			'gtn-survey-styles-daterangepicker',
			'EXT:gtn_survey/Resources/Public/JavaScript/daterangepicker.js',
		);

		$this->assetCollector->addJavaScript(
			'gtn-survey-graph-module',
			'EXT:gtn_survey/Resources/Public/JavaScript/GraphModule.js',
		);

		$this->assetCollector->addJavaScript(
			'gtn-survey-daterangepicker',
			'EXT:gtn_survey/Resources/Public/JavaScript/daterangepicker.js'
		);
		$this->assetCollector->addJavaScript(
			'gtn-survey-html2canvas',
			'EXT:gtn_survey/Resources/Public/JavaScript/html2canvas.js'
		);
		$this->assetCollector->addJavaScript(
			'gtn-survey-jspdf',
			'EXT:gtn_survey/Resources/Public/JavaScript/jspdf.min.js'
		);


		$this->assetCollector->addJavaScript(
			'gtn-survey-graph-graphmodule',
			'EXT:gtn_survey/Resources/Public/JavaScript/GraphModule.js',
		);

		/*$this->assetCollector->addJavaScript(
			'gtn-survey-graph-surveygraphs',
			'EXT:gtn_survey/Resources/Public/JavaScript/surveyGraphs.js',
		);

		$this->assetCollector->addJavaScript(
			'gtn-survey-graph-surveyPicker',
			'EXT:gtn_survey/Resources/Public/JavaScript/surveyPicker.js',
		);

		$this->assetCollector->addJavaScript(
			'gtn-survey-graph-surveyPDF',
			'EXT:gtn_survey/Resources/Public/JavaScript/surveyPDF.js',
		);*/

		#$this->pageRenderer->addJsFile('EXT:gtn_survey/Resources/Public/JavaScript/GraphModule.js');
		#$this->pageRenderer->addJsFile('EXT:gtn_survey/Resources/Public/JavaScript/Graph.js');

		#$this->pageRenderer->addJsFile('EXT:gtn_survey/Resources/Public/JavaScript/GraphModule.js');


		#$this->pageRenderer->addJsFooterFile('https://code.jquery.com/jquery-3.4.1.slim.js');
		#$this->pageRenderer->addJsFooterFile('EXT:gtn_survey/Resources/Public/JavaScript/jquery-ui.min.js');
		/*$this->pageRenderer->addRequireJsConfiguration([
			'paths' => [
				'Graph' => '../typo3conf/ext/gtn_survey/Resources/Public/JavaScript/Graph',
				'jquery-ui' => '../typo3conf/ext/gtn_survey/Resources/Public/JavaScript/jquery-ui.min',
				'jsPDF' => 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.0/jspdf.min.js',
				'html2canvas' => '../typo3conf/ext/gtn_survey/Resources/Public/JavaScript/html2canvas.js',
				'daterangepicker' => '../typo3conf/ext/gtn_survey/Resources/Public/JavaScript/daterangepicker.js'
			]
		]);*/


		#$this->view = GeneralUtility::makeInstance(StandaloneView::class);
		#$this->view->setTemplateRootPaths(['EXT:gtn_survey/Resources/Private/Backend/Templates']);

		$querySettings = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
		$querySettings->setRespectStoragePage(FALSE);
		$this->questionGroupRepository->setDefaultQuerySettings($querySettings);
		$this->userAnswerRepository->setDefaultQuerySettings($querySettings);
		$this->surveyRepository->setDefaultQuerySettings($querySettings);
		$this->participantRepository->setDefaultQuerySettings($querySettings);
	}

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		#	$this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
		#\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->moduleTemplate);die();
		$surveys = $this->surveyRepository->findAll()->toArray();


		#$this->moduleTemplate->setContent($this->view->render('List'));

		$this->moduleTemplate->assign('surveys', $surveys);
		#	$this->moduleTemplate->assign('surveys', $surveys);
		return $this->moduleTemplate->renderResponse();
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $queryResult
	 * @param $explainOutput
	 */
	public function debugQuery(\TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $queryResult, $explainOutput = FALSE) {
		$GLOBALS['TYPO3_DB']->debugOutput = 2;
		if ($explainOutput) {
			$GLOBALS['TYPO3_DB']->explainOutput = true;
		}
		$GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
		$queryResult->toArray();
		$GLOBALS['TYPO3_DB']->store_lastBuiltQuery = false;
		$GLOBALS['TYPO3_DB']->explainOutput = false;
		$GLOBALS['TYPO3_DB']->debugOutput = false;
	}

	/**
	 * action show
	 *
	 * @param \GTN\GtnSurvey\Domain\Model\Survey $survey
	 * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("survey")
	 * @return void
	 */
	public function showSurveyAction(\GTN\GtnSurvey\Domain\Model\Survey $survey) {
#		<f:asset.script identifier="surveyGraphs" src="{f:uri.resource(path: 'js/surveyGraphs.js')}" />
#        <f:asset.script identifier="surveyPicker" src="{f:uri.resource(path: 'js/surveyPicker.js')}" />
#        <f:asset.script identifier="surveyPDF" src="{f:uri.resource(path: 'js/surveyPDF.js')}" />


		#$this->pageRenderer->addJsFile('EXT:gtn_survey/Resources/Public/js/surveyGraphs.js');
		#$this->pageRenderer->addJsFile( 'EXT:gtn_survey/Resources/Public/js/surveyPicker.js');
		#$this->pageRenderer->addJsFile('EXT:gtn_survey/Resources/Public/js/surveyPDF.js');

		$fromTimestamp = null;
		$toTimestamp = null;

		if ($this->request->hasArgument('surveyDateRangeFrom') && $this->request->hasArgument('surveyDateRangeTo')) {
			$timezone = 'Europe/Vienna';

			$from = $this->request->getArgument('surveyDateRangeFrom');
			$fromParts = explode(":", $from, 2);

			$dateSecsFrom = strtotime(str_replace('.', '-', $fromParts[0]) . ' ' . $timezone);
			sscanf(trim($fromParts[1]), "%d:%d:%d", $hours, $minutes, $seconds);
			$timeSecsFrom = $hours * 3600 + $minutes * 60 + $seconds;

			//$timeSecs = strtotime(trim($fromParts[1]) . ' ' . $timezone);

			$to = $this->request->getArgument('surveyDateRangeTo');
			$toParts = explode(":", $to, 2);
			$dateSecsTo = strtotime(str_replace('.', '-', $toParts[0]) . ' ' . $timezone);
			sscanf(trim($toParts[1]), "%d:%d:%d", $hours, $minutes, $seconds);
			$timeSecsTo = $hours * 3600 + $minutes * 60 + $seconds;

			$fromTimestamp = $dateSecsFrom + $timeSecsFrom;
			$toTimestamp = $dateSecsTo + $timeSecsTo;

			$this->view->assign('timeframe', $from . ' - ' . $to);
		}

		foreach ($survey->getQuestionGroups() as $questGroup) {
			$questions = $questGroup->getQuestions();

			$questionArr = [];

			#	\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($questGroup);
			#	\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($questGroup->getGroupType()->getType());


			switch ($questGroup->getGroupType()->getType()) {
				case 1 :
					foreach ($questions as $question) {

						switch ($question->getQuestionType()->getType()) {
							case 2 :
								//multiple select
							case 3 :
								//single select

							$userAnswers = $this->userAnswerRepository->getByQuestionId($question->getUid(), $fromTimestamp, $toTimestamp);
								$usrAnswerCnt = [];

								foreach ($question->getAnswers() as $qAnswer)
									$usrAnswerCnt[$qAnswer->getText()] = 0;
								foreach ($userAnswers as $userAnswer) {
									$usrAnswerCnt[$userAnswer->getValue()]++;
								}
								$questionArr[$question->getTitle()] = array('qType' => $question->getQuestionType()->getTitle(), 'groupType' => $questGroup->getGroupType()->getType(), 'counts' => $usrAnswerCnt);
								break;
							case 4 :
								$userAnswers = $this->userAnswerRepository->getByQuestionId($question->getUid(), $fromTimestamp, $toTimestamp);
								$userAnswerTexts = [];
								foreach ($userAnswers as $userAnswer) {
									$userAnswerTexts[] = $userAnswer->getValue();
								}
								$questionArr[$question->getTitle()] = array('qType' => $question->getQuestionType()->getTitle(), 'groupType' => $questGroup->getGroupType()->getType(), 'answers' => $userAnswerTexts);

								break;
						}
					}
					break;
				case 3:
				case 5:
					$maxSectionAswers = $questGroup->getGroupType()->getType() == 3 ? 5 : 4;

				foreach ($questions as $question) {
						//$userAnswers = $this->userAnswerRepository->getByQuestionId($question->getUid(), $fromTimestamp, $toTimestamp);
						$userAnswers = $this->userAnswerRepository->getByQuestionId($question->getUid(), $fromTimestamp, $toTimestamp);
						$sectionValues = [];
						for ($i = 1; $i <= $maxSectionAswers; $i++) {
							$sectionValues[$i] = 0;
						}

					foreach ($userAnswers as $userAnswer) {
							$answr = $this->answerRepository->findByUid($userAnswer->getValue());
							$sectionValues[(int)$answr->getText()]++;
						}

						$questionArr[$question->getTitle()] = array('qType' => $question->getQuestionType()->getTitle(), 'groupType' => $questGroup->getGroupType()->getType(), 'counts' => $sectionValues);
					}
					break;
				case 2:
				case 4:

					$maxSectionAswers = $questGroup->getGroupType()->getType() == 2 ? 5 : 4;
					foreach ($questions as $question) {
						$sectionArr = [];
						foreach ($questGroup->getSections() as $section) {
							$userAnswersOfSection = $this->userAnswerRepository->getByQuestionAndSectionId($question->getUid(), $section->getUid(), $fromTimestamp, $toTimestamp);

							$sectionValues = [];
							for ($i = 1; $i <= $maxSectionAswers; $i++) {
								$sectionValues[$i] = 0;
							}

							foreach ($userAnswersOfSection as $userAnswerOfSection) {
								$answr = $this->answerRepository->findByUid($userAnswerOfSection->getValue());
								$sectionValues[(int)$answr->getText()]++;
							}
							$sectionArr[$section->getTitle()] = $sectionValues;
						}
						$questionArr[$question->getTitle()] = array('qType' => $question->getQuestionType()->getTitle(), 'groupType' => $questGroup->getGroupType()->getType(), 'sections' => $sectionArr);
					}
					break;
			}

			$questionsOfGroup[$questGroup->getName()] = $questionArr;
		}
		$this->view->assign('noOfParticipants', sizeof($this->participantRepository->getByTime($fromTimestamp, $toTimestamp)->toArray()));
		$this->view->assign('survey', $survey);
		$this->view->assign('jsonEncoded', json_encode($questionsOfGroup));

		return $this->htmlResponse();
	}

}
