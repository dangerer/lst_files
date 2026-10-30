<?php
namespace GTN\GtnSurvey\Domain\Model;

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
 * Question
 */
class Question extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * title
     * 
     * @var string

     */
    protected $title = '';

    /**
     * title
     *
     * @var string

     */
    protected $engTitle = '';

    /**
     * questionType
     * 
     * @var \GTN\GtnSurvey\Domain\Model\QuestionType
     */
    protected $questionType = null;

	/**
	 * questionType
	 *
	 * @var \GTN\GtnSurvey\Domain\Model\QuestionGroup
	 */
	protected $questiongroup = null;

    /**
     * answers
     * 
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\GTN\GtnSurvey\Domain\Model\Answer>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $answers = null;

    /**
     * __construct
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     * 
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->answers = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Returns the title
     * 
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     * 
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the title
     *
     * @return string $engTitle
     */
    public function getEngTitle()
    {
        return $this->engTitle;
    }

    /**
     * Sets the title
     *
     * @param string $engTitle
     * @return void
     */
    public function setEngTitle($engTitle)
    {
        $this->engTitle = $engTitle;
    }

    /**
     * Returns the questionType
     * 
     * @return \GTN\GtnSurvey\Domain\Model\QuestionType $questionType
     */
    public function getQuestionType()
    {
        return $this->questionType;
    }

    /**
     * Sets the questionType
     * 
     * @param \GTN\GtnSurvey\Domain\Model\QuestionType $questionType
     * @return void
     */
    public function setQuestionType(\GTN\GtnSurvey\Domain\Model\QuestionType $questionType)
    {
        $this->questionType = $questionType;
    }

	/**
	 * Returns the questionType
	 *
	 * @return \GTN\GtnSurvey\Domain\Model\QuestionType $questionType
	 */
	public function getQuestiongroup()
	{
		return $this->questiongroup;
	}

	/**
	 * Sets the questionType
	 *
	 * @param \GTN\GtnSurvey\Domain\Model\QuestionType $questionType
	 * @return void
	 */
	public function setQuestiongroup(\GTN\GtnSurvey\Domain\Model\QuestionGroup $questiongroup)
	{
		$this->questiongroup = $questiongroup;
	}

    /**
     * Adds a Answer
     * 
     * @param \GTN\GtnSurvey\Domain\Model\Answer $answer
     * @return void
     */
    public function addAnswer(\GTN\GtnSurvey\Domain\Model\Answer $answer)
    {
        $this->answers->attach($answer);
    }

    /**
     * Removes a Answer
     * 
     * @param \GTN\GtnSurvey\Domain\Model\Answer $answerToRemove The Answer to be removed
     * @return void
     */
    public function removeAnswer(\GTN\GtnSurvey\Domain\Model\Answer $answerToRemove)
    {
        $this->answers->detach($answerToRemove);
    }

    /**
     * Returns the answers
     * 
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\GTN\GtnSurvey\Domain\Model\Answer> $answers
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Sets the answers
     * 
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\GTN\GtnSurvey\Domain\Model\Answer> $answers
     * @return void
     */
    public function setAnswers(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $answers)
    {
        $this->answers = $answers;
    }
}
