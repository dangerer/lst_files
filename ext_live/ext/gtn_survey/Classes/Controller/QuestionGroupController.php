<?php
namespace GTN\GtnSurvey\Controller;

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
 * QuestionGroupController
 */
class QuestionGroupController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

	/**
	 * questionRepository
	 *
	 * @var \GTN\GtnSurvey\Domain\Repository\QuestionGroupRepository
	 */
	protected $questionGroupRepository = null;

	/**
	 * @param \GTN\GtnSurvey\Domain\Repository\QuestionGroupRepository $questionGroupRepository
	 */
	public function __construct(
		\GTN\GtnSurvey\Domain\Repository\QuestionGroupRepository $questionGroupRepository)
	{
		$this->questionGroupRepository = $questionGroupRepository;
	}

    /**
     * action list
     * 
     * @return void
     */
    public function listAction()
    {
        $questionGroups = $this->questionGroupRepository->findAll();
        $this->view->assign('questionGroups', $questionGroups);
		return $this->htmlResponse();
    }

    /**
     * action show
     * 
     * @param \GTN\GtnSurvey\Domain\Model\QuestionGroup $questionGroup
     * @return void
     */
    public function showAction(\GTN\GtnSurvey\Domain\Model\QuestionGroup $questionGroup)
    {
        $this->view->assign('questionGroup', $questionGroup);
		return $this->htmlResponse();
    }
}
