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
 * QuestionGroup
 */
class QuestionGroup extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * name
     *
     * @var string

     */
    protected $name = '';

    /**
     * name
     *
     * @var string

     */
    protected $engName = '';
    /**
     * groupType
     * 
     * @var \GTN\GtnSurvey\Domain\Model\GroupType
     */
    protected $groupType = null;

    /**
     * questions
     * 
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\GTN\GtnSurvey\Domain\Model\Question>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $questions = null;

    /**
     * sections
     * 
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\GTN\GtnSurvey\Domain\Model\Section>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $sections = null;

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
        $this->questions = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->sections = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Returns the name
     * 
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name
     * 
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the name
     *
     * @return string $engName
     */
    public function getEngName()
    {
        return $this->engName;
    }

    /**
     * Sets the name
     *
     * @param string $engName
     * @return void
     */
    public function setEngName($engName)
    {
        $this->engName = $engName;
    }

    /**
     * Returns the groupType
     * 
     * @return \GTN\GtnSurvey\Domain\Model\GroupType $groupType
     */
    public function getGroupType()
    {
        return $this->groupType;
    }

    /**
     * Sets the groupType
     * 
     * @param \GTN\GtnSurvey\Domain\Model\GroupType $groupType
     * @return void
     */
    public function setGroupType(\GTN\GtnSurvey\Domain\Model\GroupType $groupType)
    {
        $this->groupType = $groupType;
    }

    /**
     * Adds a Question
     * 
     * @param \GTN\GtnSurvey\Domain\Model\Question $question
     * @return void
     */
    public function addQuestion(\GTN\GtnSurvey\Domain\Model\Question $question)
    {
        $this->questions->attach($question);
    }

    /**
     * Removes a Question
     * 
     * @param \GTN\GtnSurvey\Domain\Model\Question $questionToRemove The Question to be removed
     * @return void
     */
    public function removeQuestion(\GTN\GtnSurvey\Domain\Model\Question $questionToRemove)
    {
        $this->questions->detach($questionToRemove);
    }

    /**
     * Returns the questions
     * 
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\GTN\GtnSurvey\Domain\Model\Question> $questions
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * Sets the questions
     * 
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\GTN\GtnSurvey\Domain\Model\Question> $questions
     * @return void
     */
    public function setQuestions(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $questions)
    {
        $this->questions = $questions;
    }

    /**
     * Adds a Section
     * 
     * @param \GTN\GtnSurvey\Domain\Model\Section $section
     * @return void
     */
    public function addSection(\GTN\GtnSurvey\Domain\Model\Section $section)
    {
        $this->sections->attach($section);
    }

    /**
     * Removes a Section
     * 
     * @param \GTN\GtnSurvey\Domain\Model\Section $sectionToRemove The Section to be removed
     * @return void
     */
    public function removeSection(\GTN\GtnSurvey\Domain\Model\Section $sectionToRemove)
    {
        $this->sections->detach($sectionToRemove);
    }

    /**
     * Returns the sections
     * 
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\GTN\GtnSurvey\Domain\Model\Section> $sections
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * Sets the sections
     * 
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\GTN\GtnSurvey\Domain\Model\Section> $sections
     * @return void
     */
    public function setSections(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $sections)
    {
        $this->sections = $sections;
    }
}
