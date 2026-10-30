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
 * QuestionController
 */
class QuestionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
	/**
	 * questionRepository
	 *
	 * @var \GTN\GtnSurvey\Domain\Repository\QuestionRepository
	 */
	protected $questionRepository = null;

	/**
	 * @param \GTN\GtnSurvey\Domain\Repository\QuestionRepository $questionRepository
	 */
	public function __construct(
		\GTN\GtnSurvey\Domain\Repository\QuestionRepository $questionRepository)
	{
		$this->questionRepository = $questionRepository;
	}

    /**
     * action list
     * 
     * @return void
     */
    public function listAction()
    {
        $questions = $this->questionRepository->findAll();
        $this->view->assign('questions', $questions);
		return $this->htmlResponse();
    }

    /**
     * action show
     * 
     * @param \GTN\GtnSurvey\Domain\Model\Question $question
     * @return void
     */
    public function showAction(\GTN\GtnSurvey\Domain\Model\Question $question)
    {
        $this->view->assign('question', $question);
		return $this->htmlResponse();
    }
}
