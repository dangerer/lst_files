<?php

namespace GTN\GtnSurvey\Controller;

use Symfony\Component\Debug\Debug;
use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use GTN\GtnSurvey\Domain\Model\UserAnswer;
use GTN\GtnSurvey\Domain\Model\Participant;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use TYPO3\CMS\Core\Mail\Mailer;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Annotation\Validate;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Context\Context;



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
class SurveyController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{


	public function initializeAction() {
		$querySettings = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
		$querySettings->setRespectStoragePage(FALSE);


		$this->questionGroupRepository->setDefaultQuerySettings($querySettings);
		$this->userAnswerRepository->setDefaultQuerySettings($querySettings);
		$this->surveyRepository->setDefaultQuerySettings($querySettings);
		$this->participantRepository->setDefaultQuerySettings($querySettings);
		$this->questionRepository->setDefaultQuerySettings($querySettings);
		$this->answerRepository->setDefaultQuerySettings($querySettings);

	}


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


	/**
	 * @param \GTN\GtnSurvey\Domain\Repository\SurveyRepository $surveyRepository
	 * @param \GTN\GtnSurvey\Domain\Repository\QuestionRepository $questionRepository
	 * @param \GTN\GtnSurvey\Domain\Repository\QuestionGroupRepository $questionGroupRepository
	 * @param \GTN\GtnSurvey\Domain\Repository\AnswerRepository $answerRepository
	 * @param \GTN\GtnSurvey\Domain\Repository\ParticipantRepository $participantRepository
	 * @param \GTN\GtnSurvey\Domain\Repository\UserAnswerRepository $userAnswerRepository
	 */
	public function __construct(\GTN\GtnSurvey\Domain\Repository\SurveyRepository        $surveyRepository,
								\GTN\GtnSurvey\Domain\Repository\QuestionRepository      $questionRepository,
								\GTN\GtnSurvey\Domain\Repository\QuestionGroupRepository $questionGroupRepository,
								\GTN\GtnSurvey\Domain\Repository\AnswerRepository        $answerRepository,
								\GTN\GtnSurvey\Domain\Repository\ParticipantRepository   $participantRepository,
								\GTN\GtnSurvey\Domain\Repository\UserAnswerRepository    $userAnswerRepository) {
		$this->surveyRepository = $surveyRepository;
		$this->questionRepository = $questionRepository;
		$this->questionGroupRepository = $questionGroupRepository;
		$this->answerRepository = $answerRepository;
		$this->participantRepository = $participantRepository;
		$this->userAnswerRepository = $userAnswerRepository;
	}

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		// DEBUG: log the last error (e.g. memory exhausted) when the request dies
		register_shutdown_function(function () {
			$err = error_get_last();
			if ($err !== null) {
				$this->debugStep('SHUTDOWN error: ' . $err['message'] . ' in ' . $err['file'] . ':' . $err['line']);
			}
		});
		$this->debugStep('listAction start, GET=' . json_encode($_GET) . ', settings.surveyid=' . ($this->settings['surveyid'] ?? 'null') . ', settings.ttAddressRequired=' . ($this->settings['ttAddressRequired'] ?? 'null'));

		// DebuggerUtility::var_dump(GeneralUtility::hmac((string)123045));die()
		$context = GeneralUtility::makeInstance(Context::class);

		$isBackendUserLoggedIn = $context->getPropertyFromAspect('backend.user', 'isLoggedIn');
		$beUsername = $context->getPropertyFromAspect('backend.user', 'username');

		if ($isBackendUserLoggedIn) {
			$this->addFlashMessage(
				"Sie sind als Backend-User '$beUsername' eingeloggt, die Absendung der Umfrage ist daher nicht möglich.",
				'Info',
				ContextualFeedbackSeverity::INFO
			);
			if (!!$this->settings['ttAddressRequired']) {
				$ttaddrUid = isset($_GET['ttahash']) && trim($_GET['ttahash']) != '' ? $_GET['ttahash'] : (isset($_GET['cA']) && trim($_GET['cA']) != '' ? $_GET['cA'] : null);
				if (isset($ttaddrUid) && trim($ttaddrUid) != '') {
						$this->addFlashMessage(
							'User hash in URL detected: ' . $ttaddrUid,
							'Info',
							ContextualFeedbackSeverity::INFO
						);
				}
			}
		}
		if (!$isBackendUserLoggedIn && !!$this->settings['ttAddressRequired']) {
			$ttaddrUid = isset($_GET['ttahash']) && trim($_GET['ttahash']) != '' ? $_GET['ttahash'] : (isset($_GET['cA']) && trim($_GET['cA']) != '' ? $_GET['cA'] : null);
			if (!isset($ttaddrUid) || trim($ttaddrUid) == '') {
				return $this->responseFactory->createResponse()->withBody($this->streamFactory->createStream('<h3>' .
					LocalizationUtility::translate('fe.hashMissing', 'GtnSurvey') . '</h3>'));
			}
			// Only fetch the uids (not the full records) to keep memory low, the table has a lot of entries
			$queryBuilder = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
				->getQueryBuilderForTable('tt_address');
			$queryBuilder->getRestrictions()->removeAll();
			$this->debugStep('BEFORE select uid from tt_address');
			$uidResult = $queryBuilder->select('uid')->from('tt_address')->executeQuery();
			$this->debugStep('AFTER select uid from tt_address');
			while (($adrUid = $uidResult->fetchOne()) !== false) {
				// Check if the hash matches the uid of the tt_address record
				if (GeneralUtility::hmac((string)$adrUid) == $ttaddrUid) {
					$curAdrUser = \nn\t3::Db()->findByUid('tt_address', (int)$adrUid, true);
					break;
				}
			}
			$uidResult->free();
			$this->debugStep('AFTER hmac loop, matched tt_address uid: ' . ($curAdrUser['uid'] ?? 'none'));
			if (!isset($curAdrUser)) {
				return $this->responseFactory->createResponse()->withBody($this->streamFactory->createStream('<h3>' .
					LocalizationUtility::translate('fe.hashInvalid', 'GtnSurvey') . '</h3>'));
			}


			$this->debugStep('BEFORE nnt3 Db()->findByValues(tx_gtnsurvey_domain_model_survey_respondees)');
			$userSurveyResponds = \nn\t3::Db()->findByValues('tx_gtnsurvey_domain_model_survey_respondees',
				['tt_address_id' => $curAdrUser['uid'], 'survey_id' => (int)$this->settings['surveyid']]);
			$this->debugStep('AFTER findByValues(respondees), rows: ' . count($userSurveyResponds ?? []));

			// GET THE TT_ADDRESS RECORD
			//$ttAddress = \nn\t3::Db()->findByValues('tt_address', ['uid' => $curAdrUser['uid']])->getFirst();

			if (!empty($userSurveyResponds)) {
				$this->debugStep('already responded -> forward to sendConfirmation');
				return new ForwardResponse('sendConfirmation');
			}

			// IF NOT FOUND, REDIRECT TO HOME PAGE
			/*	if ($curAdrUser == null) return $this->responseFactory->createResponse(302)
						->withHeader('Location', \nn\t3::Environment()->getBaseURL());
*/
			$this->view->assign('ttAddressUid', $curAdrUser['uid']);
		}

		$this->debugStep('BEFORE surveyRepository->findByUid(' . (int)$this->settings['surveyid'] . ')');
		$surveys = $this->surveyRepository->findByUid((int)$this->settings['surveyid']);
		$this->debugStep('AFTER surveyRepository->findByUid, found: ' . ($surveys !== null ? 'yes' : 'no'));

		$languageAspect = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Context\Context::class)->getAspect('language');

		$this->view->assignMultiple([
			'survey' => $surveys,
			'isBackendUserLoggedIn' => $isBackendUserLoggedIn,
			'langUid' => $languageAspect->getId()
		]);

		// Fluid rendering triggers lazy loading of the survey relations (question groups, questions, answers ...)
		$this->debugStep('BEFORE view->render()');
		$html = $this->view->render();
		$this->debugStep('AFTER view->render(), output length: ' . strlen($html));

		return $this->htmlResponse($html);
	}

	/**
	 * DEBUG: append a step with memory usage to var/log/gtn_survey_debug.log
	 * (written immediately, so it survives a fatal "memory exhausted" error)
	 */
	private function debugStep(string $message): void {
		file_put_contents(
			\TYPO3\CMS\Core\Core\Environment::getVarPath() . '/log/gtn_survey_debug.log',
			sprintf("[%s] %s | mem: %.1f MB | peak: %.1f MB\n",
				date('Y-m-d H:i:s'),
				$message,
				memory_get_usage(true) / 1048576,
				memory_get_peak_usage(true) / 1048576
			),
			FILE_APPEND
		);
	}


	/**
	 * action processAnswers
	 * @Validate(param="survey", validator="GTN\GtnSurvey\Domain\Validation\Validator\SurveyAnswerValidator")
	 * @return string
	 */
	public function processAnswerAction(\GTN\GtnSurvey\Domain\Model\Survey $survey) {


		$validatorResolver = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Validation\\ValidatorResolver');

		$arguments = GeneralUtility::_POST('tx_gtnsurvey_fesurvey');
		$participantInfo = $arguments['participant'];

		if (!!$this->settings['mailRequired']) {
			if (!empty(\GTN\GtnSurvey\Utility\ValidationUtility::validateEmail($validatorResolver, $participantInfo['mail'])->getErrors())) {
				$this->addFlashMessage(LocalizationUtility::translate('fe.error.mail.isMail', 'GtnSurvey'), '', 2);
				return new ForwardResponse('list');
			}
		}

		// if($this->request->hasArgument('answer'))
		//validate


		/**
		 * TODO
		 * SHOULD WE LINK THE PARTICIPANT DATA TO THE ANSWERS BY UID (login not required)?
		 * LINKING OF USER ANSWERS TO EITHER FRONTEND ID OR PARTICIPANT ID OR ELSE..?
		 */

		if (!$this->request->hasArgument('honeypot') || $this->request->getArgument('honeypot') != '') {
			die("Error submitting the form!");
		}

		if ($this->request->hasArgument('ttAddressUid')) {
			$ttAddressUid = (int)$this->request->getArgument('ttAddressUid');
			$ttAddressRec = \nn\t3::Db()->findByUid('tt_address', $ttAddressUid, true);

			if (empty($ttAddressRec)) {
				return $this->responseFactory->createResponse()->withBody($this->streamFactory->createStream('<h3>' .
					LocalizationUtility::translate('fe.hashInvalid', 'GtnSurvey') . '</h3>'));
			}

			\nn\t3::Db()->insert('tx_gtnsurvey_domain_model_survey_respondees', ['tt_address_id' => $ttAddressUid, 'survey_id' => (int)
			$survey->getUid()]);
		}

		$answers = $this->request->getArgument('answer');

		if ($this->request->hasArgument('participant')) {
			$participantInfo = $this->request->getArgument('participant');

			$participant = new Participant();

			$participant->setCompany($participantInfo['company']);
			$participant->setAddress($participantInfo['address']);
			$participant->setPostal((int)$participantInfo['postal']);
			$participant->setCity($participantInfo['city']);
			//  if(isset($participantInfo['date']) && $participantInfo['date'] != '') {
			$dt = isset($participantInfo['date']) && $participantInfo['date'] != '' ? new \DateTime($participantInfo['date']) : new \DateTime();
			$dt->setTimezone(new \DateTimeZone("Europe/Berlin"));
			$participant->setDate($dt);

			$participant->setContactPerson($participantInfo['consultant']);
			$participant->setMail($participantInfo['mail']);


			$curUserId = $this->participantRepository->findLatestEntry()->toArray()[0]->getUid() + 1;
			$this->participantRepository->add($participant);
			\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(PersistenceManagerInterface::class)->persistAll();

		}

		$mailHTML = '';
		ob_end_clean();

		$file = fopen('php://temp', 'w');


		$parsedNames = [];

		//\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($answers);

		foreach ($answers as $questId => $answer) {
			$questionObj = $this->questionRepository->findByUid($questId);

			$questionTypeObj = $questionObj->getQuestiongroup();
			$questionTypeType = $questionObj->getQuestiongroup()->getGroupType()->getType();
			// $this->answerRepository->findByUid($)
			$userAnswer = new UserAnswer();
			$userAnswer->setQuestionId($questId);
			$userAnswer->setFeUser($curUserId);

			if (!in_array($questionTypeObj->getName(), $parsedNames)) {
				$parsedNames[] = $questionTypeObj->getName();
				$mailHTML .= '<h4 style="">' . $questionTypeObj->getName() . '</h4>';
				$this->write_to_file($file, array(strtoupper($this->umlauts($questionTypeObj->getName()))), [], ';');
			}
			$mailHTML .= '<div style="font-family: \"source_sans_proregular\",Arial,sans-serif;margin-bottom:10px;">' . $questionObj->getTitle() . '</div>';

			#	DebuggerUtility::var_dump($questId);

			# questionTypeType == the Grouptype (1 = Default, 2 = 3 Smiley + Sekt etc.)
			switch ($questionTypeType) {
				case 2:
				case 4:

					$maxPoints = $questionTypeType == 2 ? 6 : 5;
					$this->write_to_file($file, array($this->umlauts($questionObj->getTitle())), [], ';');
					#	DebuggerUtility::var_dump($answer);
					foreach ($answer as $sectionId => $value) {
						$section = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('title',
							'tx_gtnsurvey_domain_model_section',
							'uid=' . (int)$sectionId

						);

						$mailHTML .= '<div style="margin-bottom:5px;"><strong>' . $section['title'] . '</strong></div>';
						#fputcsv($file, array($section['title']), ',');

						if (!isset($value) || $value == '') {
							$this->write_to_file($file, array($this->umlauts($section['title'])), [], ';');
						} else {
							if (!is_array($value)) {
								$value = array($value);
							}
							$sectionValues = [];
							foreach ($value as $val) {
								if ((int)$val > 0) {
									$userAnswer = new UserAnswer();
									$userAnswer->setQuestionId($questId);
									$userAnswer->setSectionId($sectionId);
									$userAnswer->setFeUser($curUserId);

									$userAnswer->setValue((int)$val);

									$this->userAnswerRepository->add($userAnswer);
									\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(PersistenceManagerInterface::class)->persistAll();
									$answerObj = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('text',
										'tx_gtnsurvey_domain_model_answer',
										'uid=' . (int)$val

									);
									$sectionValues[] = $this->pointScaleToGradingScale($maxPoints, (int)
									$answerObj['text']);
									$mailHTML .= '<div style="display: flex;
    align-items: center;">&#9632; ' . $this->pointScaleToGradingScale($maxPoints, (int)$answerObj['text']) . '</div>';
								}
							}
							$this->write_to_file($file, array($this->umlauts($section['title'])), $sectionValues, ';');
							/*} else {
								$sectionValues = [];
								#		DebuggerUtility::var_dump("b");

								#		DebuggerUtility::var_dump($value);

								if ((int)$value > 0) {
									$userAnswer = new UserAnswer();
									$userAnswer->setFeUser($curUserId);
									$userAnswer->setQuestionId($questId);
									$userAnswer->setSectionId($sectionId);
									$userAnswer->setValue((int)$value);

									$this->userAnswerRepository->add($userAnswer);
									\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(PersistenceManagerInterface::class)->persistAll();
									$answerObj = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('text',
										'tx_gtnsurvey_domain_model_answer',
										'uid=' . (int)$value);

									$mailHTML .= '<div style="display: flex;
    align-items: center;">&#9632; ' . $this->pointScaleToGradingScale($maxPoints, (int)$answerObj['text']) . '</div>';

									$sectionValues[] = $this->pointScaleToGradingScale($maxPoints, (int)$answerObj['text']);
								}
								$this->write_to_file($file, array($this->umlauts($section['title'])), $sectionValues, ';');

							}*/
						}
					}
					break;
				case 3:
				case 5:

					$maxPoints = $questionTypeType == 3 ? 6 : 5;

					$answerValues = [];

					if (isset($answer) && $answer != '') {
					//	\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($answer);
						foreach ($answer as $key => $answerVal) {
							//$answerVal = $answerObj[0];

							$userAnswer = new UserAnswer();
							$userAnswer->setQuestionId($questId);
							$userAnswer->setValue((int)$answerVal);
							$userAnswer->setFeUser($curUserId);
							$this->userAnswerRepository->add($userAnswer);
							\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(PersistenceManagerInterface::class)->persistAll();
							$answerObj = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('text',
								'tx_gtnsurvey_domain_model_answer',
								'uid=' . (int)$answerVal);

							$answerValues[] = $this->pointScaleToGradingScale($maxPoints, (int)$answerObj['text']);

							$mailHTML .= '<div style="display: flex;
    align-items: center;">&#9632; ' . $this->pointScaleToGradingScale($maxPoints, (int)$answerObj['text']) . '</div>';
							//  fputcsv($file, array($this->umlauts($questionObj->getTitle())), ';');
						}
					}
					$this->write_to_file($file, array($this->umlauts($questionObj->getTitle())), $answerValues, ';');

					break;
				default:

					if ($questionObj->getTitle() != '' && strlen($questionObj->getTitle()) > 0) {
						$this->write_to_file($file, array($this->umlauts($questionObj->getTitle())), [], ';');
					}

					//1 = text 2=multiselect 3=singleselect 4=textarea

					switch ($questionObj->getQuestionType()->getType()) {

						case 1:
						case 4:
							if (isset($answer) && $answer != '') {
								$userAnswer->setQuestionId($questId);
								$userAnswer->setValue($answer);
								$this->userAnswerRepository->add($userAnswer);
								\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(PersistenceManagerInterface::class)->persistAll();
								$mailHTML .= '<div style="display: flex;
    align-items: center;">&#9632; ' . $answer . '</div>';
								$lineArr = explode("\n", $answer);
								foreach ($lineArr as $line) {
									fputcsv($file, array($line), ';');
								}
							}
							break;
						case 2:
						case 3:
							$answerStrings = [];
							foreach ($answer as $answerVal) {
								if (isset($answerVal) && $answerVal != '') {
									$userAnswer = new UserAnswer();
									$userAnswer->setQuestionId($questId);
									$userAnswer->setFeUser($curUserId);
									$userAnswer->setValue($this->answerRepository->findByUid((int)$answerVal)->getText());
									$this->userAnswerRepository->add($userAnswer);
									\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(PersistenceManagerInterface::class)->persistAll();
									\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(PersistenceManagerInterface::class)->persistAll();
									$answerObj = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('text',
										'tx_gtnsurvey_domain_model_answer',
										'uid=' . (int)$answerVal);
									$answerStrings[] = $answerObj['text'];
									$mailHTML .= '<div style="display: flex;
    align-items: center;">&#9632; ' . (is_numeric($answerObj['text']) ? 0 : $answerObj['text'])
										. '</div>';
									//	fputcsv($file, array($this->umlauts($answerObj['text'])), ';');


								}
							}
							$questAnswers = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
								'text',
								'tx_gtnsurvey_domain_model_answer',
								'question=' . (int)$questId
							);
							foreach ($questAnswers as $qAnswer) {
								$tempArr = array($this->umlauts($qAnswer['text']));
								if (in_array($qAnswer['text'], $answerStrings)) {
									$tempArr[] = 'Ja';
								}
								$this->write_to_file($file, $tempArr, [], ';');
							}


							break;
					}

					break;
			}
		}

		if (isset($ttAddressRec) || isset($participantInfo)) {
			$mailHTML .= '<h3>Absender:</h3>';
			if (isset($ttAddressRec) && !!$this->settings['fetchRespondeeMail']) {
				$mailHTML .= '<div style=""><strong>E-mail: </strong>' . $ttAddressRec['email'] . '</div>';
			}

			if (isset($participantInfo)) {
				$mailHTML .= '<div style=""><strong>Firma: </strong>' . $participantInfo['company'] . '</div>'
					. '<div style=""><strong>Anschrift: </strong>' . $participantInfo['address'] . '</div>'
					. '<div style=""><strong>Postleitzahl: </strong>' . $participantInfo['postal'] . '</div>'
					. '<div style=""><strong>Ort: </strong>' . $participantInfo['city'] . '</div>'
					. '<div style=""><strong>Datum: </strong>' . $dt->format('d.m.Y') . '</div>'
					. '<div style=""><strong>Ansprechpartner: </strong>' . $participantInfo['consultant'] . '</div>'
					. '<div style=""><strong>E-Mail: </strong>' . $participantInfo['mail'] . '</div>';
			}
		}
		rewind($file);

		# require_once("./typo3conf/ext/gtn_survey/Resources/Private/Php/swiftmailer/lib/classes/Swift/Attachment.php");


		#       $objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\Extbase\\Object\\ObjectManager');
#              #  $querySettings = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');

		#      $attachment = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\Switch_Attachment', stream_get_contents($file), 'Antworten.csv', 'text/csv');
		#      $attachment->setFilename('Antworten.csv');


		if (isset($this->settings['receiverMail']) && $this->settings['receiverMail'] != '') {
			$subjectLine = "Fragebogen zur Kundenzufriedenheitsanalyse";
			if (trim($survey->getName()) != '') $subjectLine .= " - " . $survey->getName();


			$mail = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(Email::class);
			// \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($link);
			$mail
				// Give the message a subject
				->subject($subjectLine)
				// Set the From address with an associative array
				->from(new Address("office@lst.at", "Labor Strauss Gruppe"))
				// Set the To addresses with an associative array
				->to(...array_map(function ($mail) {
						return trim($mail);
					},
						explode(',', $this->settings['receiverMail'])
					)
				)
				// Give it a body
				// And optionally an alternative body
				->attach($file, 'Antworten.csv', 'text/csv')
				->html($mailHTML, 'text/html');

			GeneralUtility::makeInstance(Mailer::class)->send($mail);

		}


		return new ForwardResponse('sendConfirmation');
	}

//fputcsv($file, array(strtoupper($this->umlauts($questionTypeObj->getName()))), ';');

	public function write_to_file($fileHandle, $arrWithDel, $arrWithoutDel, $delimiter) {

		$str = '';
		foreach ($arrWithDel as $el) {
			$str .= '"' . $el . '"' . $delimiter;
		}
		foreach ($arrWithoutDel as $el2) {
			$str .= $el2 . $delimiter;
		}

		fwrite($fileHandle, rtrim($str, $delimiter) . PHP_EOL);
	}


	public function umlauts($string) {
		$search = array("Ä", "Ö", "Ü", "ä", "ö", "ü", "ß", "´");
		$replace = array("Ae", "Oe", "Ue", "ae", "oe", "ue", "ss", "");
		return str_replace($search, $replace, $string);
	}


	public function sendConfirmationAction() {
		# Redirect to page 12747
		$uri = $this->uriBuilder
			->reset()
			->setTargetPageUid(12747)
			->build();

		return $this->redirectToUri($uri);
		/*return $this->responseFactory->createResponse()->withBody($this->streamFactory->createStream('<h3>' .
			LocalizationUtility::translate('fe.afterSurveyMessage', 'GtnSurvey') . '</h3>'));*/
//		return $this->htmlResponse();
	}

	public function numToSmiley($val) {
		$img = '';
		switch ($val) {
			case 1:
				$img .= '<img style="margin-left:10px;" src="https://www.lst.at/typo3conf/ext/gtn_survey/Resources/Public/Icons/sad.png" width="28" height="28"/>';
				break;
			case 2:
				$img .= '<img style="margin-left:10px;" src="https://www.lst.at/typo3conf/ext/gtn_survey/Resources/Public/Icons/sad.png" width="28" height="28"/><span style="font-size:30px;">/</span>';
				$img .= '<img  src="https://www.lst.at/typo3conf/ext/gtn_survey/Resources/Public/Icons/neutral.png" width="28" height="28"/>';
				break;
			case 3:
				$img .= '<img style="margin-left:10px;" src="https://www.lst.at/typo3conf/ext/gtn_survey/Resources/Public/Icons/neutral.png" width="28" height="28"/>';
				break;
			case 4:
				$img .= '<img style="margin-left:10px;" src="https://www.lst.at/typo3conf/ext/gtn_survey/Resources/Public/Icons/neutral.png" width="28" height="28"/><span style="font-size:30px;">/</span>';
				$img .= '<img src="https://www.lst.at/typo3conf/ext/gtn_survey/Resources/Public/Icons/happy.png" width="28" height="28"/>';
				break;
			case 5:
				$img .= '<img style="margin-left:10px;" src="https://www.lst.at/typo3conf/ext/gtn_survey/Resources/Public/Icons/happy.png" width="28" height="28"/>';
				break;
		}
		return $img;
	}

	public function numToExcelSmiley($val) {
		switch ($val) {
			case 1:
				return "🙁";
				break;
			case 2:
				return "🙁/😐";
				break;
			case 3:
				return "😐";
				break;
			case 4:
				return "😐/🙂";
				break;
			case 5:
				return "🙂";
				break;
		}
	}

	public function sendAnswerMailAction() {
		/* $mail = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Mail\MailMessage::class);
		 $mailBody = $_POST['survey'];
		 // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($link);
		 $mail
			 // Give the message a subject
			 ->setSubject("Fragebogen zur Kundenzufriedenheitsanalyse")
			 // Set the From address with an associative array
			 ->setFrom(array("office@lst.at" => "Labor Strauss Gruppe"))
			 // Set the To addresses with an associative array
			 ->setTo(array("meisenberg@gtn-solutions.com" => "meisenberg@gtn-solutions.com", "eisenberg.martin@gmx.at" => "eisenberg.martin@gmx.at"))
			 // Give it a body
			 // And optionally an alternative body

			 ->setBody($mailBody, 'text/html')
			 ->send();*/
		return '';
	}

	private function pointScaleToGradingScale(int $maxPoints, int $points) {
		return $maxPoints - $points;
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
		DebugUtility::debug($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
		$GLOBALS['TYPO3_DB']->store_lastBuiltQuery = false;
		$GLOBALS['TYPO3_DB']->explainOutput = false;
		$GLOBALS['TYPO3_DB']->debugOutput = false;
	}

	/**
	 * action show
	 *
	 * @param \GTN\GtnSurvey\Domain\Model\Survey $survey
	 * @return void
	 */
	public function showAction(\GTN\GtnSurvey\Domain\Model\Survey $survey) {
		$this->view->assign('survey', $survey);
		return $this->htmlResponse();
	}
}
