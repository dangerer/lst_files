<?php
namespace Gtn\GtnLsthooks\Hooks;

use Fab\Vidi\Formatter\Date;
use Gtn\GtnLsthooks\Utils\Utils;
use In2code\Powermail\Controller\AbstractController;
use In2code\Powermail\Controller\FormController;
use In2code\Powermail\DataProcessor\AbstractDataProcessor;
use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Model\Page;
use In2code\Powermail\Domain\Repository\AnswerRepository;
use In2code\Powermail\Domain\Service\Mail\SenderMailPropertiesService;
use In2code\Powermail\Domain\Service\Mail\SendMailService;
use In2code\Powermail\Domain\Service\UploadService;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

use Gtn\GtnLsthooks\Domain\Repository\CategoryRepository;
/**
 * Powermail hooks
 *
 */
class PowermailHooks
{

    private $ranFormUid = 3;
    private $articlePages = array(19, 12, 11);
    private $ranFormFields = array('kundennummer', 'kundenreferenz', 'anrede', 'vorname', 'nachname', 'email', 'telefon', 'firma', 'plz', 'marker' /*city*/, 'marker_02' /*address*/, 'land2');
    private $articleFields = array('anzahl', 'type', 'artikelnummer', 'deliverynumber', 'seriennummer', 'returnreason', 'beschreibung', 'auftragsnummer');
    private $fieldRANnrUid = 190;
    private $formPageUidRAN = 32;

    private $dayCodeFormUid = 10;
    private $dayCodeFormUidEn = 11;
    private $dayCodeFormFields = array('anrede', 'vorname', 'nachname', 'e_mail', 'telefon', 'uebermittlungdestagescodeper', 'firma', 'land2');
    private $dayCodeAdditionalFields = array('anlagenbetreiber', 'zentraltype', 'seriennummer', 'grundderanfrage', 'aktuellesanlagendatum', 'schluesselzahl');

    private $techSupportFormUid = 2;

    private $courseRegistrationFormUid = 20;
    private $courseUidMarker = 'kursuid';
    private $participantPages = array(122, 121);
    private $participantFields = array('titel', 'vorname_01', 'nachname_01', 'email', 'gleicheanschriftgleichefirma', 'firma_01', 'plz_01', 'ort', 'adresse', 'land', 'zertifikat');

    // INRENATIONAL with approve/decline
    private $tempInternRanTableName = 'international_ran_requests';
    private $tempInternRanDataPid = 130;
    private $messageAboutApprovingPid = 12436;
//    private $ranPid = 12435; // TEST pid
    private $ranPid = 32;

    /**
     * Change field to array of fields:   name="tx_powermail_pi1[field][anzahl]"  => name="tx_powermail_pi1[field][XXXX][anzahl]"
     *
     * @param Form $form
     * @param \In2code\Powermail\Controller\FormController $pObj
     */
    public function formBeforeRender(Form $form, $pObj)
    {
        
        // if it is after form error (already filled fields)
        if (GeneralUtility::_GP('tx_powermail_pi1')) {
            $gp = GeneralUtility::_GP('tx_powermail_pi1');
            $formData = $gp['field'];
            $articleUid = 1;
            $articles = array();
            foreach ($this->articleFields as $field) {
                if ($formData[$field]) {
                    if (!is_array($formData[$field]))
                        $decoded = json_decode($formData[$field]);
                    else
                        $decoded = false;
                    if ($decoded && $formData[$field] != $decoded) {
                        $decoded = (array)$decoded;
                        // from back button
                        foreach ($decoded as $indJ => $valJ) {
                            $articles[$indJ][$field] = $valJ;
                        }
                    } else {
                        // from the same form
                        if (isset($formData[$field]) && is_array($formData[$field])) {
                            foreach ($formData[$field] as $ind => $value) {
                                $articles[$ind][$field] = $value;
                            }
                        }
                    }
                }
            }
            $jsonArticles = json_encode($articles);
//                DebuggerUtility::var_dump($jsonArticles);
            // add this value to hidden field
            /** @var Page $page */
            foreach ($form->getPages() as $page) {
                /** @var Field $field */
                foreach ($page->getFields() as $field) {
//                        echo $field->getMarker().'<br>';
                    if ($field->getMarker() == 'articlesdata') {
//                            $field->val
                        $field->setPrefillValue($jsonArticles);
                        // it is not enough only prefilled. Also we need new virtual field property
                        $field->lstAddJs = '<script>var articlesData = \''.$jsonArticles.'\';</script>';
//                            piVars['field']['articlesdata'] = $jsonArticles;
//                            DebuggerUtility::var_dump($field);
                        break 2;
                    }
                }
            }
        }
        // Only for RAN uid = 3
        if ($form->getUid() == $this->ranFormUid) {
            $articleUid = 1;
            /** @var Page $page */
            foreach ($form->getPages() as $page) {
                if (in_array($page->getUid(), $this->articlePages)) {
                    $articleClass = $page->getCss().' ranArticleFieldset ';
                    $page->setCss($articleClass);
                    $page->articleUid = $articleUid;
                }
                $articleHeader = 'articleHeader'; // marker in the form
                // Work only with needed Fields (by markers)
                /** @var Field $field */
                foreach ($page->getFields() as $field) {
                    $marker = $field->getMarker();
                    if ($marker == $articleHeader) {
                        $articleUid++;
                    }
                    if (in_array($marker, $this->articleFields)) {
                        // Add class to the wrapper
                        $field->setCss($field->getCss().' ranInputItem tempCss');
//                            DebuggerUtility::var_dump(true, 'field');
//                            DebuggerUtility::var_dump($pObj, 'pObj');
                    }
                }
            }
        }

        // only for COURSE REGISTRATION form
        if ($form->getUid() == $this->courseRegistrationFormUid) {

            // after form error...:
            if (GeneralUtility::_GP('tx_powermail_pi1')) {
                $gp = GeneralUtility::_GP('tx_powermail_pi1');
                $formData = $gp['field'];
                $participantUid = 1;
                $participants = array();
                foreach ($this->participantFields as $field) {
                    if ($formData[$field]) {
                        if (!is_array($formData[$field])) {
                            $decoded = json_decode($formData[$field]);
//                            if (is_array($decoded)) {
//                                $decoded = array_filter($decoded);
//                            }
                        } else {
                            $decoded = false;
                        }
                        if ($decoded && $formData[$field] != $decoded) {
                            $decoded = (array)$decoded;
                            // from back button
                            foreach ($decoded as $indJ => $valJ) {
                                $participants[$indJ][$field] = $valJ;
                            }
                        } else {
                            // from the same form
                            if (isset($formData[$field]) && is_array($formData[$field])) {
                                foreach ($formData[$field] as $ind => $value) {
                                    $participants[$ind][$field] = $value;
                                }
                            }
                        }
                    }
                }
                // clean empty:
                $participants = array_filter($participants, function($participant) {
                    return array_filter($participant, function($value) {
                        return !empty($value);
                    });
                });
                $jsonParticipants = json_encode($participants);
                // add this value to hidden field
                /** @var Page $page */
                foreach ($form->getPages() as $page) {
                    /** @var Field $field */
                    foreach ($page->getFields() as $field) {
                        if ($field->getMarker() == 'participantsdata') {
                            $field->setPrefillValue($jsonParticipants);
                            // it is not enough only prefilled. Also we need new virtual field property
                            $field->lstAddJs = '<script>var participantsData = \''.$jsonParticipants.'\';</script>';
                            break 2;
                        }
                    }
                }
            }

            // change HTML of 'participant' fields
            $participantUid = 1;
            /** @var Page $page */
            foreach ($form->getPages() as $page) {
                if (in_array($page->getUid(), $this->participantPages)) {
                    $participantClass = $page->getCss().' participantFieldset ';
                    $page->setCss($participantClass);
                    $page->participantUid = $participantUid;
                }
                $participantHeader = 'participantHeader'; // marker in the form
                // Work only with needed Fields (by markers)
                /** @var Field $field */
                foreach ($page->getFields() as $field) {
                    $marker = $field->getMarker();
                    if ($marker == $participantHeader) {
                        $participantUid++;
                    }
                    if (in_array($marker, $this->participantFields)) {
                        // Add class to the wrapper
                        $field->setCss($field->getCss().' courseInputItem ');
                    }
                }
            }
            // moved into viewhelper IfCourseIsFull
//            $courseUid = $this->getCourseAttribute($form, 'uid');
//            $maxPossibleParticipants = $this->getCourseAttribute($form, 'maxParticipants');
//            $utils = GeneralUtility::makeInstance(Utils::class);
//            $registrationsExistingCount = $utils->getCourseExistingRegistrationNumber($courseUid);
//            if ($registrationsExistingCount >= $maxPossibleParticipants) {
                // it is A MARKER of FULL COURSE - used as a condition in the FLUID templates
//                $pObj->addFlashMessage('The course is full', 'full course', AbstractMessage::INFO, false);
//            }



        }

    }

    /**
     * @param Mail $mail
     * @param FormController $pObj
     */
    public function formBeforePreviewRender($mail, $pObj)
    {
        // only for Course registration form
        if ($mail->getForm()->getUid() == $this->courseRegistrationFormUid) {
            $utils = GeneralUtility::makeInstance(Utils::class);
            $courseUid = $this->getCourseAttribute($mail->getForm(), 'uid');
//            $maxPossibleParticipants = $this->getCourseAttribute($mail->getForm(), 'maxParticipants');
            $maxPossibleParticipants = $utils->getCourseMaxParticipants($courseUid);
            $registrationsExistingCount = $utils->getCourseExistingRegistrationNumber($courseUid);
            if ($registrationsExistingCount >= $maxPossibleParticipants) {
                $pObj->forward('form');
            }
        }
        // only for RAN form
        if ($mail->getForm()->getUid() == $this->ranFormUid) {
            // only for International request
            if ($this->isInternationalContext() || $this->isRequestApproving() || $this->isRequestDecline()) {
                // only for approving:
                if ($this->isRequestApproving()) {
                    $gp = GeneralUtility::_GET();
                    $approvingHash = $gp['tx_powermail_pi1']['field']['forceApprove'];
                    // add new hidden field - forceApprove
                    $approveField = GeneralUtility::makeInstance(Field::class);
                    $approveField->setType('hidden');
                    $approveField->setMarker('forceApprove');
                    $approveAnswer = GeneralUtility::makeInstance(Answer::class);
                    $approveAnswer->setField($approveField);
                    $approveAnswer->setValue($approvingHash);
                    $mail->addAnswer($approveAnswer);
                }
                // only for declining:
                if ($this->isRequestDecline()) {
                    $gp = GeneralUtility::_GET();
                    $declineHash = $gp['tx_powermail_pi1']['field']['forceDecline'];
                    // add new hidden field - forceDecline
                    $declineField = GeneralUtility::makeInstance(Field::class);
                    $declineField->setType('hidden');
                    $declineField->setMarker('forceDecline');
                    $declineAnswer = GeneralUtility::makeInstance(Answer::class);
                    $declineAnswer->setField($declineField);
                    $declineAnswer->setValue($declineHash);
                    $mail->addAnswer($declineAnswer);
                    // add new field - declineReason
                    $declineReasonField = GeneralUtility::makeInstance(Field::class);
                    $declineReasonField->setType('textarea');
                    $declineReasonField->setMarker('declineReason');
                    $declineReasonAnswer = GeneralUtility::makeInstance(Answer::class);
                    $declineReasonAnswer->setField($declineReasonField);
                    $declineReasonAnswer->setValue('');
                    $mail->addAnswer($declineReasonAnswer);
                }
            }
        }
        $this->mailConvertToArticles($mail, $pObj);
        $this->mailConvertToParticipants($mail, $pObj);
    }

    // Slot ist called directly after the mail was persisted
    public function afterSaveDB($mail, $pObj) {
        $this->mailConvertToArticles($mail, $pObj);
        $this->mailConvertToParticipants($mail, $pObj);
    }

    /**
     * @param Mail $mail
     * @param mixed $pObj
     */
    private function mailConvertToArticles($mail, $pObj) {
            // Only for RAN uid = 3
        if ($mail->getForm()->getUid() == $this->ranFormUid) {
            /** @var ObjectStorage $articles */
            $articles = array();
            // fill article answers with ->articleField = true
            /** @var Answer $answer */
            foreach ($mail->getAnswers() as $answer) {
                $marker = $answer->getField()->getMarker();
                if (in_array($marker, $this->articleFields)) {
                    $answer->articleField = true;
                    $fieldValues = json_decode($answer->getRawValue());
                    foreach ($fieldValues as $articleUid => $value) {
                        if (!key_exists($articleUid, $articles)) {
                            $articles[$articleUid]['answers'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage');
                            $articles[$articleUid]['title'] = LocalizationUtility::translate('article', 'gtn_lsthooks').' '.$articleUid;
                        }
                        $newAnsw = clone($answer);
                        $newAnsw->setValue($value);
//                            $articles[$articleUid]['answers'][] = $newAnsw;
                        $articles[$articleUid]['answers']->attach($newAnsw);
                    }
                } else if ($marker == 'region') { // only for region marker - we need to preview selected region and sent it in emails
                    $regionAnsw = clone($answer);
                    $gp = GeneralUtility::_GET();
                    if (@$gp['tx_powermail_pi1']['field']['forceRegion']) {
                        // it is possible from BE list: approve - decline internatinal requests
                        $regionAnsw->setValue($gp['tx_powermail_pi1']['field']['forceRegion']);
                    } elseif (($this->isRequestApproving() || $this->isRequestDecline()) && @$_POST['tx_powermail_pi1']['field']['region']) {
                        $regionAnsw->setValue($_POST['tx_powermail_pi1']['field']['region']);
                    } else {
                        $regionAnsw->setValue($this->getRegionTitle(null, '', $mail));
                    }
                }

            }
            // insert region answer to the mail
            if ($regionAnsw && $regionAnsw instanceof Answer) {
                $allAnswers = $mail->getAnswers();
                $newAnswers = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectStorage::class);
                foreach ($allAnswers as $answer) {
                    $marker = $answer->getField()->getMarker();
                    if ($marker == 'region') {
                        $newAnswers->attach($regionAnsw);
                    } else {
                        $newAnswers->attach($answer);
                    }
                }
                // attach new list answers!
                $mail->setAnswers($newAnswers);
            }
            if (count($articles) > 0) {
                // move 'auftragsnummer' to the end of article datas
                foreach ($articles as $articleUid => $artData) {
                    $clone = null;
                    /** @var Answer $answerTemp */
                    foreach ($articles[$articleUid]['answers'] as $answerTemp) {
                        if ($answerTemp->getField()->getMarker() == 'auftragsnummer') {
                            $clone = clone($answerTemp);
                            $articles[$articleUid]['answers']->detach($answerTemp);
                        }
                    }
                    if ($clone) {
                        $articles[$articleUid]['answers']->attach($clone);
                    }
                }
                $mail->articles = $articles;
            }
        }
        // for DayCode form (Tagescode)
        else if ($mail->getForm()->getUid() == $this->dayCodeFormUid || $mail->getForm()->getUid() == $this->dayCodeFormUidEn) {
            foreach ($mail->getAnswers() as $answer) {
                $marker = $answer->getField()->getMarker();
                if ($marker == 'region') { // only for region marker - we need to preview selected region and sent it in emails
                    /** @var Answer $regionAnsw */
                    $regionAnsw = clone($answer);
                    $regionAnsw->setValue($this->getRegionTitle(0, 'Internationaler Verkauf', $mail));
//                    $regionAnsw->getField()->setType('input'); // hidden to visible
                }
            }
            // insert region answer to the mail
            if ($regionAnsw && $regionAnsw instanceof Answer) {
                $allAnswers = $mail->getAnswers();
//                DebuggerUtility::var_dump($regionAnsw);
                $newAnswers = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectStorage::class);
                foreach ($allAnswers as $answer) {
                    $marker = $answer->getField()->getMarker();
//                    DebuggerUtility::var_dump($answer);
//                    DebuggerUtility::var_dump($marker);
                    if ($marker == 'region') {
                        $newAnswers->attach($regionAnsw);
                    } else {
                        $newAnswers->attach($answer);
                    }
                }
                // attach new list answers!
                $mail->setAnswers($newAnswers);
            }
        }
        // for "technischer-support" form: uid = 2
        else if ($mail->getForm()->getUid() == $this->techSupportFormUid) {            
            foreach ($mail->getAnswers() as $answer) {
                $marker = $answer->getField()->getMarker();
                if ($marker == 'region') { // only for region marker - we need to preview selected region and sent it in emails
                    /** @var Answer $regionAnsw */
                    $regionAnsw = clone($answer);
                    $regionAnsw->setValue($this->getRegionTitle(0, '', $mail));
//                    $regionAnsw->getField()->setType('input'); // hidden to visible
                }
            }
            // insert region answer to the mail
            if ($regionAnsw && $regionAnsw instanceof Answer) {
                $allAnswers = $mail->getAnswers();
//                DebuggerUtility::var_dump($regionAnsw);
                $newAnswers = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectStorage::class);
                foreach ($allAnswers as $answer) {
                    $marker = $answer->getField()->getMarker();
//                    DebuggerUtility::var_dump($answer);
//                    DebuggerUtility::var_dump($marker);
                    if ($marker == 'region') {
                        $newAnswers->attach($regionAnsw);
                    } else {
                        $newAnswers->attach($answer);
                    }
                }
                // attach new list answers!
                $mail->setAnswers($newAnswers);
            }
        }
    }

    /**
     * Slot is called before the mail and answers are persisted and before the emails are sent
     * @param Mail $mail
     * @param string $hash
     * @param FormController $pObj
     */
    public function createBeforePreviewRender($mail, $hash, $pObj) {
        // Only for RAN uid = 3
        if ($mail->getForm()->getUid() == $this->ranFormUid) {

            // only for INTERNATIONAL users
            if ($this->isInternationalContext() || $this->isRequestApproving() || $this->isRequestDecline())
            {
                // prepend the data to handle it:
                list($values, $htmlVars) = $this->prependRanVarsForViewRender($mail);

                if ($this->isRequestApproving()) {

                    // THE BE USER REQUESTS APPROVING
                    // update the requests table
                    $secretString = $this->ranInternationalGetRequestedSecretHash('approve');
                    $this->ranSetInternationalRequestStatus($secretString, 'approve', '');

                } elseif ($this->isRequestDecline()) {

                    $htmlVars['declineReason'] = '';
                    $gp = GeneralUtility::_POST('tx_powermail_pi1');
                    if (@$gp['field']['declineReason']) {
                        $htmlVars['declineReason'] = $gp['field']['declineReason'];
                    }

                    // send an email to the USER
                    $subject = LocalizationUtility::translate('internationalRAN.emailToUserDeclined.subject', 'gtn_lsthooks');
                    $name = $values['vorname'] . ' ' . $values['nachname'];
                    $recipient = [$values['email'] => $name];
                    $sender = ['noreply@laborstrauss.com' => 'Labor Strauss Gruppe'];
                    $this->sendTemplateEmail($recipient, $sender, $subject, 'InternRanDeclineToUser', $htmlVars);

                    // update the requests table
                    $secretString = $this->ranInternationalGetRequestedSecretHash('decline');
                    $this->ranSetInternationalRequestStatus($secretString, 'decline', $htmlVars['declineReason']);

                    // a message for the admin after submitting:
                    // redirected later
                    /*echo '<h4>DECLINED</h4><br>';
                    echo 'Here is: <br><ul>
                        <li>The user will get an email about declined his request</li>
                        </ul>                                            
                    ';*/
                } else {

                    // THE USER FILLS THE FORM and CONFIRMS it

                    // 1. save the data into custom table
                    $databaseConnection = GeneralUtility::makeInstance(ConnectionPool::class)
                        ->getConnectionForTable($this->tempInternRanTableName);
                    $databaseConnection->insert($this->tempInternRanTableName, $values);
                    $values['uid'] = $databaseConnection->lastInsertId($this->tempInternRanTableName);

                    // add approve/decline links to the email
                    $utils = GeneralUtility::makeInstance(Utils::class);
                    $links = [
                        'approve' => $utils->getRanApproveDeclineUrl($values, $this->articleFields, $this->ranFormUid, $this->ranPid, 'approve'),
                        'decline' => $utils->getRanApproveDeclineUrl($values, $this->articleFields, $this->ranFormUid, $this->ranPid, 'decline'),
                    ];
                    $htmlVars['links'] = $links;

                    // 2. send an email to ADMIN
//                    $recipient = ['szavarzin@gtn-solutions.com' => 'RAN admin'];
//                    $recipients = [
//                        ['andreas.kurzweil@lst.at' => 'RAN admin']
//                    ];

                    $configurationManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Configuration\\BackendConfigurationManager');
                    $extbaseFrameworkConfiguration = $configurationManager->getTypoScriptSetup();
                    $tsSetup = $extbaseFrameworkConfiguration['plugin.']['tx_gtnlsthooks.'];
                    if (is_array($tsSetup)) {
                        $typoscriptService = GeneralUtility::makeInstance(\TYPO3\CMS\Core\TypoScript\TypoScriptService::class);
                        $tsSetup = $typoscriptService->convertTypoScriptArrayToPlainArray($tsSetup['settings.']);
                        $recipients = GeneralUtility::trimExplode(',', $tsSetup['adminEmailsToRANapprove']);
                    }

 /*                   if ($GLOBALS['TSFE']->id != $this->ranTestingPid) { // only for TESTING !!!!!! remove this condition on real page!!!!!!
                        $settings = $pObj->getSettings();
                        $recipient = [$settings['receiver']['email'] => 'RAN admin'];
                    }*/

                    // TESTING ADRESSES
//                    $recipients = [
//                        'szavarzin@gtn-solutions.com',
//                        'andreas.kurzweil@lst.at'
//                    ];

                    $sender = ['noreply@laborstrauss.com' => 'Labor Strauss Gruppe'];
                    foreach ($recipients as $recipientEmail) {
                        $recipient = [$recipientEmail => 'RAN admin'];
                        $this->sendTemplateEmail($recipient, $sender, 'RMA-Anfrage: bitte um Prüfung', 'InternRanToAdmin', $htmlVars);
                    }
                    // 3. send an email to the USER
//                    $GLOBALS['LANG']

                    $subject = LocalizationUtility::translate('internationalRAN.emailToUser.subject', 'gtn_lsthooks');
                    $name = $values['vorname'] . ' ' . $values['nachname'];
                    $recipient = [$values['email'] => $name];
                    $sender = ['noreply@laborstrauss.com' => 'Labor Strauss Gruppe'];
                    $this->sendTemplateEmail($recipient, $sender, $subject, 'InternRanToUser', $htmlVars);

                    // 4. Redirect to "Thanks page"
                    $uri = $this->getUrlByPageUid($this->messageAboutApprovingPid);
                    header('Location: ' . $uri);
                    exit;
                }
//                \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($uri, 'PowermailHooks.php:405', 4); exit; // !!!!!!!!!! delete it
            }

            // generate new RAN number:
            // 18001: Year and numbers from 001
            // 190001: Year and numbers 0001 from 01.01.2019
            if (date('y') >= 19) {
                $currNr = intval(date('y') . '0001');
            } else {
                $currNr = intval(date('y') . '001');
            }

            // get last RAN number directly from DB!
            $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
            $connection = $connectionPool->getConnectionForTable('tx_powermail_domain_model_answer');
            $queryBuilder = $connection->createQueryBuilder();
            $queryResult = $queryBuilder
                ->select('value')
                ->from('tx_powermail_domain_model_answer')
                ->where(
                    $queryBuilder->expr()->eq('field', $queryBuilder->createNamedParameter($this->fieldRANnrUid, \PDO::PARAM_INT)),
                )
                ->orderBy('value', 'DESC')
                ->setMaxResults(3) // just as an example
                ->executeQuery();
            while ($answRow = $queryResult->fetchAssociative()) {
                if ($answRow['value'] >= $currNr) {
                    $currNr = $answRow['value'] + 1;
                }
            }

            $gp = $_POST;
            // ADD result number into Answer
            /** @var Answer $answer */
            foreach ($mail->getAnswers() as $answer) {
                if ($answer->getField()->getMarker() == 'rmanr') {
                    $answer->setValue($currNr);
                }
                // use the region from "forceRegion" parameter
                if ($answer->getField()->getMarker() == 'region' && ($this->isRequestApproving() || $this->isRequestDecline())) {
                    // use from GP, not from Cookie value
                    $answer->setValue($gp['tx_powermail_pi1']['field']['region']);
                }
            }

            $this->mailConvertToArticles($mail, $pObj);
        }

        // only for Course registration form
        if ($mail->getForm()->getUid() == $this->courseRegistrationFormUid) {
            // check on max number of participants
            $courseUid = $this->getCourseAttribute($mail->getForm(), 'uid');
            $utils = GeneralUtility::makeInstance(Utils::class);
            $existing = $utils->getCourseExistingRegistrationNumber($courseUid);
//            $maxParticipants = $this->getCourseAttribute($mail->getForm(), 'maxParticipants');
            $maxParticipants = $utils->getCourseMaxParticipants($courseUid);
            if ($existing >= $maxParticipants) {
                $this->theCourseIsFull($mail, $pObj);
            }
        }


        // JUST FOR TEST !!!!!!! - do not save the answer
        if ($this->isInternationalContext() || $this->isRequestApproving() || $this->isRequestDecline()) {

            /*if ($this->isRequestApproving()) {

                echo '<h4>APPROVING</h4><br>';
                echo 'Here is a regular behaviour, like: <br><ul>
<li>The user filled the form</li>
<li>The user commited the form</li>
<li>new RAN added (new number generated)</li>
<li>regular emails sended: to the admin and to the user</li>
</ul>
<br>
<strong>Will be used this RAN number: '.$currNr.'</strong>
<br><br>
<strong>This message stops DB changing and will be removed on live page </strong>

';
                // mail will not saved in DB because it is disabled via TypoScript for the test page
//                exit; // DO NOT SAVE - testing!!!!
            }*/

            if ($this->isRequestDecline()) {

                // Clear all output
                if (ob_get_contents()) {
                    ob_end_clean();
                }
                // AND - redirect
                header("Location: https://www.laborstrauss.com/kundencenter/retourlieferung/abgelehnt");
                exit;
            }

        }

    }

    /**
     * // Hook for change emails - started before email sended
     * @param MailMessage $message
     * @param array $email
     * @param SendMailService $pObj
    */
    public function getFilesForAttach($message, $email, $pObj) {
/*
		if (@$GLOBALS['TSFE']->fe_user->user['uid'] == 4842) {

			DebuggerUtility::var_dump($message);
			DebuggerUtility::var_dump($email);
			DebuggerUtility::var_dump($pObj);

			die();
		}
*/
        // change receiver name to email
        $message->setTo([$email['receiverEmail'] => $email['receiverEmail']]);
        $ranN = null;

        // attach data.csv to RAN form
        $mail = $pObj->getMail();
        // only for RAN form and only for Admin
        if ($mail->getForm()->getUid() == $this->ranFormUid
            && $email['template'] == 'Mail/ReceiverMail')
        {            
            $missFields = array('captcha', 'articlesdata', 'datenschutz');
            // get Ran number
            $ranN = $mail->getAnswersByFieldUid()[$this->fieldRANnrUid]->getValue();
            /** @var Answer $answer */
            $articles = array();
            $csvRows = array();
            $formData = array();
            $articlesData = array();
            foreach ($mail->getAnswers() as $answer) {
                $marker = $answer->getField()->getMarker();
                if (!in_array($marker, $missFields)
                        && !in_array($marker, $this->articleFields)) {
                    $csvRows[] = $marker.': ' . $answer->getValue();
                    $formData[$marker] = $answer->getValue();
                }
                if (in_array($marker, $this->articleFields)) {
                    $fieldValues = json_decode($answer->getRawValue());
                    foreach ($fieldValues as $articleUid => $value) {
                        if (!key_exists($articleUid, $articles)) {
                            $articles[$articleUid]['answers'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage');
                            $articles[$articleUid]['title'] = 'Artikel '.$articleUid;
                        }
                        $newAnsw = clone($answer);
                        $newAnsw->setValue($value);
                        $articles[$articleUid]['answers']->attach($newAnsw);
                        $articlesData[$articleUid][$marker] = $value;
                    }
                }
            }
            if (count($articles) > 0) {
                foreach ($articles as $article) {
                    $csvRows[] = $article['title'];
                    foreach ($article['answers'] as $answer) {
                        $marker = $answer->getField()->getMarker();
                        if (!in_array($marker, $missFields)) {
                            $csvRows[] = $marker.': ' . $answer->getValue();
                        }
                    }
                }
            }
            // add CSV file
            $filename = 'rma'.$ranN.'.csv';
            $dir = \TYPO3\CMS\Core\Core\Environment::getPublicPath() . '/' .'fileadmin/RAN_data/';
            $fullPath = $dir.$filename;
//            \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($fullPath, 'PowermailHooks.php:373', 4); // !!!!!!!!!! delete it
            /*file_put_contents($fullPath, implode("\r\n", $csvRows));

            if (file_exists(GeneralUtility::getFileAbsFileName($fullPath))) {
                $message->attach(\Swift_Attachment::fromPath($fullPath));
            }*/

            // new CSV structure
            $csvRows = array();
            // general Form data
            $csvRows = array();
            $generalRow = array('K', $ranN);
            foreach ($this->ranFormFields as $ranFormField) {
                $value = '';
                if (key_exists($ranFormField, $formData))
                    $value = $formData[$ranFormField];
                $value = str_replace("\"", "\"\"", $value);
                if (!is_numeric($value) && trim($value) != '')
                    $generalRow[] = "\"".$value."\"";
                else
                    $generalRow[] = $value;
                // add selected region after Country
                if ($ranFormField == 'land2') {
                    $generalRow[] = "\"".$this->getRegionTitle(null, '', $mail)."\"";
                }
            }
            $csvRows[] = implode(";", $generalRow);
            // articles Data
            foreach ($articlesData as $artikleUid => $article) {
                $articleRow = array('P', $artikleUid);
                foreach ($this->articleFields as $field) {
                    if (isset($articlesData[$artikleUid][$field])) {
                        $value = $articlesData[$artikleUid][$field];
                        $value = str_replace("\"", "\"\"", $value);
                        if (!is_numeric($value) && trim($value) != '')
                            $articleRow[] = "\"".$value."\"";
                        else
                            $articleRow[] = $value;
                    } else {
                        $articleRow[] = '';
                    }
                }
//                $csvRows[] = $articleRow;
                $csvRows[] = implode(";", $articleRow);
            }
//\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($csvRows, 'PowermailHooks.php:418', 4); exit; // !!!!!!!!!! delete it
            // save CSV
            $file = fopen($fullPath, 'w');
            file_put_contents($fullPath, implode("\r\n", $csvRows));
            fclose($file);
            if (file_exists(GeneralUtility::getFileAbsFileName($fullPath))) {
                $message->attachFromPath($fullPath);
            }

            // remove file for TEST page (DB is also not saved)
            // !!!! probably it will break sending of the email to admin
            // !!!! I do not want to add a new hook for "after email sending"
            $pageUid = $GLOBALS['TSFE']->id;
//            if ($pageUid == $this->ranPid) {
//                unlink($fullPath);
//            }

        }

        // only for RAN form and only for User and only for International
        // must be called after admin's APPROVING
        if ($mail->getForm()->getUid() == $this->ranFormUid
            && $email['template'] == 'Mail/SenderMail'
            && ($this->isInternationalContext()  || $this->isRequestApproving())
        ) {

            $htmlContent = '';
            // prepend the data to handle it:
            list($values, $htmlVars) = $this->prependRanVarsForViewRender($mail);
            $templateRootPath = ExtensionManagementUtility::extPath('gtn_lsthooks').'Resources/Private/Templates/Emails/';

            /** @var \TYPO3\CMS\Fluid\View\StandaloneView $pdfView */
            $pdfView = GeneralUtility::makeInstance(StandaloneView::class);
            $templatePathAndFilename = $templateRootPath . 'PdfForRanInternationalUser.html';
            $pdfView->setTemplatePathAndFilename($templatePathAndFilename);

            $pdfView->assignMultiple($htmlVars); // assign Fluid variable
            $pdfBodyHtml = $pdfView->render();

            // save PDF to temp folder
            $randomFilename = 'ran_' . $this->getRandomString(8);
            $tempHtmlFilePath = Environment::getPublicPath() . '/typo3temp/' . $randomFilename. '.html';
            GeneralUtility::writeFile($tempHtmlFilePath, $pdfBodyHtml);

            // HTML content to PDF
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($pdfBodyHtml);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
//            $dompdf->stream("document.pdf", ["Attachment" => false]);
            $pdfOutput = $dompdf->output();
            $pdfLocation = Environment::getPublicPath() . '/typo3temp/' . $randomFilename . '.pdf';
            file_put_contents($pdfLocation, $pdfOutput);

            // attach to the sender email
            if (file_exists(GeneralUtility::getFileAbsFileName($pdfLocation))) {
                $message->attachFromPath($pdfLocation);
            }

        }

        // only for RAN and only if the RAN number is existing already
        if ($mail->getForm()->getUid() == $this->ranFormUid) {
            $ranN = $mail->getAnswersByFieldUid()[$this->fieldRANnrUid]->getValue();
            // add RAN number into the message subject !
            if ($ranN) {
                $currSubject = $message->getSubject();
                $message->setSubject($currSubject.' - '.$ranN);
            }
        }

        // attach daycode.csv to DayCode (Tagescode) form
        // only for DayCode form and only for Admin
        // disabled now
        if (11 == 22 && $mail->getForm()->getUid() == $this->dayCodeFormUid && $email['template'] == 'Mail/ReceiverMail') {
            $missFields = array('captcha', 'datenschutz');
            /** @var Answer $answer */
            $csvRows = array();
            $formData = array();
            $additionalData = array();
            foreach ($mail->getAnswers() as $answer) {
                $marker = $answer->getField()->getMarker();
                if (!in_array($marker, $missFields)) {
                    $csvRows[] = $marker.': ' . $answer->getValue();
                    $formData[$marker] = $answer->getValue();
                }
/*                if (in_array($marker, $this->articleFields)) {
                    $fieldValues = json_decode($answer->getRawValue());
                    foreach ($fieldValues as $articleUid => $value) {
                        if (!key_exists($articleUid, $articles)) {
                            $articles[$articleUid]['answers'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage');
                            $articles[$articleUid]['title'] = 'Artikel '.$articleUid;
                        }
                        $newAnsw = clone($answer);
                        $newAnsw->setValue($value);
                        $articles[$articleUid]['answers']->attach($newAnsw);
                        $articlesData[$articleUid][$marker] = $value;
                    }
                }*/
            }
/*            if (count($articles) > 0) {
                foreach ($articles as $article) {
                    $csvRows[] = $article['title'];
                    foreach ($article['answers'] as $answer) {
                        $marker = $answer->getField()->getMarker();
                        if (!in_array($marker, $missFields)) {
                            $csvRows[] = $marker.': ' . $answer->getValue();
                        }
                    }
                }
            }*/
            // add CSV file
            $filename = 'daycode.csv';
            $dir =  \TYPO3\CMS\Core\Core\Environment::getPublicPath() . '/' .'fileadmin/daycode_data/';
            $fullPath = $dir.$filename;

            // new CSV structure
            $csvRows = array();
            // general Form data
            $csvRows = array();
            $generalRow = array();
            foreach ($this->dayCodeFormFields as $formField) {
                $value = '';
                if (key_exists($formField, $formData))
                    $value = $formData[$formField];
                $value = str_replace("\"", "\"\"", $value);
                if (!is_numeric($value) && trim($value) != '')
                    $generalRow[] = "\"".$value."\"";
                else
                    $generalRow[] = $value;
                // add selected region after Country
                if ($formField == 'land2') {
                    $generalRow[] = "\"".$this->getRegionTitle(0, '', $mail)."\"";
                }
            }
            $csvRows[] = implode(";", $generalRow);
            // additional Data
            $additionalRow = array();
            foreach ($this->dayCodeAdditionalFields as $formField) {
                $value = '';
                if (key_exists($formField, $formData))
                    $value = $formData[$formField];
                $value = str_replace("\"", "\"\"", $value);
                if (!is_numeric($value) && trim($value) != '')
                    $additionalRow[] = "\"".$value."\"";
                else
                    $additionalRow[] = $value;
            }
            $csvRows[] = implode(";", $additionalRow);
            // save CSV
            $file = fopen($fullPath, 'w');
            file_put_contents($fullPath, implode("\r\n", $csvRows));
            fclose($file);
            if (file_exists(GeneralUtility::getFileAbsFileName($fullPath))) {
//                $message->attach(\Swift_Attachment::fromPath($fullPath));
                $message->attachFromPath($fullPath);
            }

        }

    }

    /**
     * @param int $langUid
     * @param string $defaultTitle
     * @param Mail $mail
     * @return mixed|string
     */
    public function getRegionTitle($langUid = null, $defaultTitle = '', $mail = null) {
        if ($mail instanceof Mail
            && ($mail->getForm()->getUid() == $this->dayCodeFormUid || $mail->getForm()->getUid() == $this->dayCodeFormUid)
        ) {
            // direct name for tagescode
//            return 'Internationaler Verkauf';
        }

        $utils = GeneralUtility::makeInstance(Utils::class);
        $region = $utils->getSelectedRegion();

        $categoryRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance
		(\Gtn\GtnLsthooks\Domain\Repository\CategoryRepository::class);


		$qb = GeneralUtility::makeInstance(ConnectionPool::class)
			->getQueryBuilderForTable('sys_category');
        if ($langUid !== null) { // use needed language
            /*$query = $categoryRepository->createQuery();
            $query->getQuerySettings()->setRespectSysLanguage(FALSE);
//            $query->getQuerySettings()->setLanguageUid($langUid)->setLanguageMode('strict')->setIgnoreEnableFields(true);
            $query->getQuerySettings()->setIgnoreEnableFields(true);
            $query->matching($query->logicalAnd(
                $query->equals('uid', $region),
                $query->equals('sysLanguageUid', $langUid)
            ));
            $selectedCategory = $query->execute()->getFirst();*/
			$qb->getRestrictions()->removeAll();

			$selectedCategory = $qb
				->select('*')
				->from('sys_category')
				->where(
					$qb->expr()->eq(
						'uid',
						$qb->createNamedParameter((int)$region, \PDO::PARAM_INT)
					),
					$qb->expr()->eq(
						'sys_language_uid',
						$qb->createNamedParameter((int)$langUid, \PDO::PARAM_INT)
					)
				)
				->executeQuery()
				->fetchAssociative();
        } else {
			$selectedCategory = $qb
				->select('*')
				->from('sys_category')
				->where(
					$qb->expr()->eq(
						'uid',
						$qb->createNamedParameter($region, \PDO::PARAM_INT)
					)
				)
				->executeQuery()
				->fetchAssociative();
        }
        if ($selectedCategory) {
            $regionTitle = $selectedCategory['title'];
        } else {
            $regionTitle = "";
        }
        $regionTitle = trim($regionTitle);
        // sometimes the region title is empty, simple hack for this case
        if (!$regionTitle && $defaultTitle) {
            $regionTitle = $defaultTitle;
        }
        return $regionTitle;
    }

    /**
     * @param string $email
     * @param SenderMailPropertiesService $pObj
     * @return void
     */
    public function getSenderEmail(&$email, $pObj)
    {
        // here is possible work only with page UID (not form UID) !!!!!!!
        // pid = 12148 - needed page with anonym form
        $currentPageUid = $GLOBALS['TSFE']->id;
        // "Anonym" form
        $email = 'noreply@laborstrauss.com';
        if ($currentPageUid == 12148) {
//            $email = 'szavarzin@gtn-solutions.com';
        }
    }

    /**
     * @param string $email
     * @param SenderMailPropertiesService $pObj
     * @return void
     */
    public function getSenderName(&$name, $pObj)
    {
        // here is possible work only with page UID (not form UID) !!!!!!!
        // pid = 12148 - needed page with anonym form
        $currentPageUid = $GLOBALS['TSFE']->id;
        // "Anonym" form
        if ($currentPageUid == 12148) {
            $name = 'Kein Absendername';
        }
    }

    // course registration. get course ID (DCE uid fact)
    public function getCourseAttribute($form, $attributeName)
    {
        $courseUid = 0;
        if ($form->getUid() == $this->courseRegistrationFormUid) {
            $gp = GeneralUtility::_GP('tx_powermail_pi1');
            $formData = $gp['field'];
            switch ($attributeName) {
                case 'uid':
                    $markerName = $this->courseUidMarker;
                    break;
                case 'maxParticipants':
                    // deprecated. moved in $utils->getCourseMaxParticipants($courseUid);
                    $markerName = 'mp';
                    break;
                default:
                    $markerName = $this->courseUidMarker;
            }
            foreach ($form->getPages() as $page) {
                foreach ($page->getFields() as $field) {
                    $marker = $field->getMarker();
                    if ($marker == $markerName) {
                        $courseUid = $formData[$markerName];
                    }
                }
            }
        }
        return $courseUid;
    }

    // course is full. What to do
    /**
     * @param Mail $mail
     * @param FormController $formController
     * @return void
     */
    public function theCourseIsFull($mail, $formController)
    {
        // redirect into the form. There will be shown a notification about FULL course
//        $uriBuilder = $formController->getControllerContext()->getUriBuilder();
//        $redirectUrl = $uriBuilder->reset()->setTargetPageUid(12213)->build();
//        $mail->setAdditionalData(['courseIsFullMarker' => 1]);
//        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($mail, 'PowermailHooks.php:697', 4); exit; // !!!!!!!!!! delete it
        return $formController->forward('form');
//        'tx_powermail_pi1' => ['field' => ['courseIsFullMarker' => 1]]
    }

    private function mailConvertToParticipants($mail, $pObj) {
        // Only for COURSE REGISTRATION uid = 20
        if ($mail->getForm()->getUid() == $this->courseRegistrationFormUid) {
            /** @var ObjectStorage $participants */
            $participants = array();
            // fill participant answers with ->participantField = true
            /** @var Answer $answer */
            foreach ($mail->getAnswers() as $answer) {
                $marker = $answer->getField()->getMarker();
                if (in_array($marker, $this->participantFields)) {
                    $answer->participantField = true;
                    $fieldValues = json_decode($answer->getRawValue());
                    foreach ($fieldValues as $participantUid => $value) {
                        if (!key_exists($participantUid, $participants)) {
                            $participants[$participantUid]['answers'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage');
                            $participants[$participantUid]['title'] = LocalizationUtility::translate('participant', 'gtn_lsthooks').' '.$participantUid;
                            $participants[$participantUid]['id'] = $participantUid;
                        }
                        $newAnsw = clone($answer);
                        $newAnsw->setValue($value);
                        $participants[$participantUid]['answers']->attach($newAnsw);
                    }
                }
            }
            // clean empty answers (usually it is 1th participant, so no index changed)
            foreach ($participants as $pUid => $participant) {
                // TODO: name is empty - is this enough checking?
                /** @var Answer $answ */
                $emptyVal = true;
                foreach (@$participant['answers'] as $answ) {
                    $marker = $answ->getField()->getMarker();
                    if (in_array($marker, ['vorname_01', 'nachname_01', 'email']) && $answ->getRawValue()) {
                        $emptyVal = false;
                    }
                }
                if ($emptyVal) {
                    unset($participants[$pUid]);
                }
            }

            if (count($participants) > 0) {
                $mail->participants = $participants;
            } else {
                // make participant fields empty. We got an array from POST data and it is converted into JSON. But if it is empty - we need to have EMPTY value.
                // (difference from articles - article are required. Participant is not required)
                /** @var Answer $answer */
                foreach ($mail->getAnswers() as $answer) {
                    $marker = $answer->getField()->getMarker();
                    if (in_array($marker, $this->participantFields)) {
                        $answer->setValue('');
                    }
                }
            }
        }

    }


    public function isInternationalContext()
    {
        $pageUid = $GLOBALS['TSFE']->id;
//        if ($pageUid == $this->ranPid) {
            // check only by region TITLE !!
            $regionTitle = $this->getRegionTitle();
            $titles = ['Internationaler Verkauf', 'International Sales'];
            if (in_array($regionTitle, $titles)) {
                return true;
            }
//        }
        
        return false;
    }

    /**
     * @param int $pageUid
     * @param bool  $absolute
     * @return string
     */
    public function getUrlByPageUid($pageUid, $absolute = true)
    {
        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($GLOBALS['TSFE']->getLanguage()->getLanguageId(), 'PowermailHooks.php:908', 4); exit; // !!!!!!!!!! delete it
        if (!$GLOBALS['TSFE']) {
            return 'null';
        }

        $params = [
            'forceAbsoluteUrl' => $absolute,
            'parameter' => $pageUid
        ];

        return $GLOBALS['TSFE']->cObj->typoLink_URL($params);
    }

    public function getRandomString($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * @param array $recipient recipient of the email in the format array('recipient@domain.tld' => 'Recipient Name')
     * @param array $sender sender of the email in the format array('sender@domain.tld' => 'Sender Name')
     * @param string $subject subject of the email
     * @param string $templateName template name (UpperCamelCase)
     * @param array $variables variables to be passed to the Fluid view
     * @return boolean TRUE on success, otherwise false
     */
    public function sendTemplateEmail(array $recipient, array $sender, $subject, $templateName, array $variables = array(), $attachments = null, $templateNameAsSource = false)
    {
        $templateRootPath = ExtensionManagementUtility::extPath('gtn_lsthooks').'Resources/Private/Templates/Emails/';

        /** @var \TYPO3\CMS\Fluid\View\StandaloneView $emailView */
        $emailView = GeneralUtility::makeInstance(StandaloneView::class);
        $templatePathAndFilename = $templateRootPath . $templateName . '.html';
        if ($templateNameAsSource) {
            $emailView->setTemplateSource($templateName);
        } else {
            $emailView->setTemplatePathAndFilename($templatePathAndFilename);
        }
        $emailView->assignMultiple($variables); // assign Fluid variable
        $emailBody = $emailView->render();

        /*
        DebuggerUtility::var_dump($subject);
        DebuggerUtility::var_dump($emailBody);
        DebuggerUtility::var_dump($recipient);
        DebuggerUtility::var_dump($sender);
        echo $emailBody;
        exit;/**/
        /** @var $message \TYPO3\CMS\Core\Mail\MailMessage */
//        $message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Mail\\MailMessage');
        $message = GeneralUtility::makeInstance(MailMessage::class);
        $message->setTo($recipient)
            ->setFrom($sender)
            ->setSubject($subject);

        // Possible attachments here
        if (is_countable($attachments) && count($attachments)) {
            foreach ($attachments as $file => $name) {
                if (file_exists($file)) {
                    if (trim($name)) {
                        $message->attach(\Swift_Attachment::fromPath($file)->setFilename($name));
                    } else {
                        $message->attach(Swift_Attachment::fromPath($file));
                    }
                }
            }
        }

        // Plain text example
        //$message->setBody($emailBody, 'text/plain');

        // HTML Email
        /*if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) < 10000000) {
            $message->setBody($emailBody, 'text/html');
        } else {*/
        $message->html($emailBody);
//        }

        $message->send();

        return $message->isSent();
    }

    public function getSimpleArticlesArray($data, $fieldTitles)
    {
        $articles = [];
        $arrData = [];
        foreach ($this->articleFields as $fieldName) {
            $arrData[$fieldName] = (array)json_decode($data[$fieldName]);
        }

        foreach ($arrData['artikelnummer'] as $k => $articleData) {
            $articleItem = [];
            foreach ($this->articleFields as $fieldName) {
                $articleItem[$fieldName]['value'] = @$arrData[$fieldName][$k];
                $articleItem[$fieldName]['title'] = @$fieldTitles[$fieldName];
            }
            $articles[] = $articleItem;
        }

        return $articles;
    }

    public function ranInternationalCheckRequestHash($secretString)
    {
        list($uid, $hash) = GeneralUtility::trimExplode(':', $secretString);

        $databaseConnection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($this->tempInternRanTableName);
        $result = $databaseConnection->count('uid', $this->tempInternRanTableName, ['uid' => $uid, 'hash' => $hash]);
        if ($result) {
            return true;
        }

        return false;
    }

    public function ranSetInternationalRequestStatus($secretString, $status, $reason = '')
    {
        list($uid, $hash) = GeneralUtility::trimExplode(':', $secretString);

        $databaseConnection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($this->tempInternRanTableName);
        $result = $databaseConnection->count('uid', $this->tempInternRanTableName, ['uid' => $uid, 'hash' => $hash]);
        if ($result) {
            $databaseConnection->update($this->tempInternRanTableName,
                [
                    'submit_tstamp' => time(),
                    'status' => $status,
                    'decline_reason' => $reason,
                ],
                ['uid' => $uid]);
            return true;
        }

        return false;
    }

    public function isRequestApproving()
    {
        $forceApprove = $this->ranInternationalGetRequestedSecretHash('approve');
        if ($forceApprove && $this->ranInternationalCheckRequestHash($forceApprove)) {
            return true;
        }

        return false;
    }

    public function ranInternationalGetRequestedSecretHash($forAction = '')
    {
        if (!$forAction) {
            return '';
        }
        $gp = GeneralUtility::_GET(); // GET
        $secretString = @$gp['tx_powermail_pi1']['field']['force'.ucfirst($forAction)];
        if (!$secretString) {
            $gp = GeneralUtility::_POST(); // POST
            $secretString = @$gp['tx_powermail_pi1']['field']['force'.ucfirst($forAction)];
        }

        return $secretString;
    }

    public function isRequestDecline()
    {
        $forceDecline = $this->ranInternationalGetRequestedSecretHash('decline');
        if ($forceDecline && $this->ranInternationalCheckRequestHash($forceDecline)) {
            return true;
        }

        return false;
    }


    /**
     * @param Mail $mail
     * @return void
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public function prependRanVarsForViewRender($mail)
    {
        // values for diff goals
        $values = [];
        $fieldTitles = []; // we need it for multi-languages
        /** @var Answer $answer */
        foreach ($mail->getAnswers() as $answer) {
            $fieldMareker = $answer->getField()->getMarker();
            $values[$fieldMareker] = $answer->getValue();
            $fieldTitles[$fieldMareker] = $answer->getField()->getTitle();
        }

        // add/change some data
        $values['pid'] = $this->tempInternRanDataPid;
        $values['tstamp'] = time();
        $values['crdate'] = time();
        $values['cruser_id'] = time();
        if (@$GLOBALS['TSFE']->fe_user->user['uid']) {
            $values['cruser_id'] = $GLOBALS['TSFE']->fe_user->user['uid'];
        }
        $values['deleted'] = 0;
        $values['hidden'] = 0;
        $values['sys_language_uid'] = 0;
        if (@$GLOBALS['TSFE']->getLanguage()->getLanguageId()) {
            $values['sys_language_uid'] = $GLOBALS['TSFE']->getLanguage()->getLanguageId();
        }
        $values['datenschutz'] = @reset($values['datenschutz']);
        $values['hash'] = $this->getRandomString();

        // convert $values to pairs: title, value (for diff language compatible)
        // for html render (mails, pdf, ...)
        $htmlVars = [];
        foreach ($values as $key => $value) {
            $htmlVars[$key] = [
                'value' => $value,
                'title' => $fieldTitles[$key],
            ];
        }
        $htmlVars['articles'] = $this->getSimpleArticlesArray($values, $fieldTitles);

        return [$values, $htmlVars];
    }

}



