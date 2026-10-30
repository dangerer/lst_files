<?php
namespace Gtn\ElcEtapas3\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use Typoheads\Formhandler\Session\TYPO3;

class DatamapHook {

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	private $objectManager;

	/**
	 * @var array
	 */
	private $beUser;

	/**
	 * @var array
	 */
	private $tsSettings;

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
	 */
	protected $persistenceManager = null;

	function __construct() {
		$this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$configurationManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
		$extbaseFrameworkConfiguration = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		$this->tsSettings = $extbaseFrameworkConfiguration['plugin.']['tx_elcetapas3_elcetapas.']['settings.'];
		$this->persistenceManager = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class);
	}

    //public function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, &$pObj) {
    public function processDatamap_afterDatabaseOperations($status, $table, $id, &$fieldArray, &$pObj) {
    	//DebuggerUtility::var_dump($status);
        if ($status == 'update' && $table == 'elc_etapas') {
        	$this->beUser = $pObj->BE_USER->user;
        	$oldEtapa = $pObj->checkValue_currentRecord;
        	$oldCat = $oldEtapa['category'];
			$etapasRepository = $this->objectManager->get(\Gtn\ElcEtapas3\Domain\Repository\EtapasRepository::class);
			/** @var \Gtn\ElcEtapas3\Domain\Model\Etapas $newEtapas */
			$newEtapas = $etapasRepository->findByUidWithHidden($id);
			// category was changed - Send email to owner
        	if (isset($fieldArray['category']) && $fieldArray['category'] != $oldCat) {
				$this->sendEmailAfterEtapasStatusUpdated($newEtapas);
				// if the category is for publish - publish, no - hide
				$publishCategories = GeneralUtility::trimExplode(',', $this->tsSettings['etapas.']['categoriesForBePublished']);
				if (in_array($fieldArray['category'], $publishCategories))
					$newEtapas->setHidden(0);
				else
					$newEtapas->setHidden(1);
			}

			// add comment
			$newCommentText = trim(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('addComment'));
			if ($newCommentText != '') {
				$newComment = new \Gtn\ElcEtapas3\Domain\Model\Comment();
				$newComment->setMessage($newCommentText);
				$beUserRepository = $this->objectManager->get(\TYPO3\CMS\Extbase\Domain\Repository\BackendUserRepository::class);
				$beUser = $beUserRepository->findByUid($this->beUser['uid']);
				$newComment->setBeUser($beUser);
				//$newComment->setPid($this->tsSettings['']);
				$newComment->setTstamp(new \DateTime());
				$newEtapas->addComment($newComment);
				$this->sendEmailAfterCommentAdded($newEtapas, $newComment);
			}
			$etapasRepository->update($newEtapas);
			$this->persistenceManager->persistAll();
        }
    }

	/**
	 * @param \Gtn\ElcEtapas3\Domain\Model\Etapas $etapas
	 */
	public function sendEmailAfterEtapasStatusUpdated($etapas) {
		/* @var \Gtn\ElcEtapas3\Utilites\MailService $mailService */
		$mailService = $this->objectManager->get('Gtn\\ElcEtapas3\\Utilites\\MailService');

		// owner or etapas email?
		//$feUserRepository = $this->objectManager->get(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class);
		$feUser = $etapas->getFeUser();
		if ($feUser instanceof \TYPO3\CMS\Extbase\Domain\Model\FrontendUser) // email from owner
			$recipient = array($feUser->getEmail() => $feUser->getFirstName().' '.$feUser->getLastName());
		else // email from etapas
			$recipient = array($etapas->getEmail() => $etapas->getName());

		// sender from BE user of default from TS
		if ($this->beUser['email'] && $this->beUser['email'] != '')
			$sender = array($this->beUser['email'] => $this->beUser['realName']);
		else
			$sender = array($this->tsSettings['adminEmail'] => $this->tsSettings['adminName']);

		$variables = array(
			'etapas' => $etapas,
			'feUser' => $feUser
		);

		$mailService->sendTemplateEmail(
			$recipient,
			$sender,
			\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('elc_etapas.emails.afterUpdatingCategory.subject', 'elc_etapas3'),
			'EtapasStatusUpdatedNotification',
			$variables
		);
	}

	/**
	 * @param \Gtn\ElcEtapas3\Domain\Model\Etapas $etapas
	 * @param \Gtn\ElcEtapas3\Domain\Model\Comment $comment
	 * @return boolean
	 */
	public function sendEmailAfterCommentAdded($etapas, $comment) {
		/* @var \Gtn\ElcEtapas3\Utilites\MailService $mailService */
		$mailService = $this->objectManager->get('Gtn\\ElcEtapas3\\Utilites\\MailService');
		// owner or etapas email?
		//$feUserRepository = $this->objectManager->get(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class);
		$feUser = $etapas->getFeUser();
		if ($feUser instanceof \TYPO3\CMS\Extbase\Domain\Model\FrontendUser) // email from owner
			$recipient = array($feUser->getEmail() => $feUser->getFirstName().' '.$feUser->getLastName());
		else // email from etapas
			$recipient = array($etapas->getEmail() => $etapas->getName());

		// sender from BE user of default from TS
		if ($this->beUser['email'] && $this->beUser['email'] != '')
			$sender = array($this->beUser['email'] => $this->beUser['realName']);
		else
			$sender = array($this->tsSettings['adminEmail'] => $this->tsSettings['adminName']);

		$variables = array(
			'etapas' => $etapas,
			'comment' => $comment,
			'feUser' => $feUser
		);

		$mailService->sendTemplateEmail(
			$recipient,
			$sender,
			\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('elc_etapas.emails.afterReviewerCommentAdded.subject', 'elc_etapas3'),
			'EtapasReviewerCommentAddedNotification',
			$variables
		);
		return true;
	}


}