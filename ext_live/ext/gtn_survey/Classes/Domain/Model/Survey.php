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
 * Survey
 */
class Survey extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
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
    protected $description = '';

    /**
     * name
     *
     * @var string

     */
    protected $engDescription = '';

    /**
     * questionGroups
     * 
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\GTN\GtnSurvey\Domain\Model\QuestionGroup>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $questionGroups = null;

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
        $this->questionGroups = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
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
     * @return string $engDescription
     */
    public function getEngDescription()
    {
        return $this->engDescription;
    }

    /**
     * Sets the name
     *
     * @param string $engDescription
     * @return void
     */
    public function setEngDescription($engDescription)
    {
        $this->engDescription = $engDescription;
    }

    /**
     * Returns the name
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the name
     *
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Adds a QuestionGroup
     * 
     * @param \GTN\GtnSurvey\Domain\Model\QuestionGroup $questionGroup
     * @return void
     */
    public function addQuestionGroup(\GTN\GtnSurvey\Domain\Model\QuestionGroup $questionGroup)
    {
        $this->questionGroups->attach($questionGroup);
    }

    /**
     * Removes a QuestionGroup
     * 
     * @param \GTN\GtnSurvey\Domain\Model\QuestionGroup $questionGroupToRemove The QuestionGroup to be removed
     * @return void
     */
    public function removeQuestionGroup(\GTN\GtnSurvey\Domain\Model\QuestionGroup $questionGroupToRemove)
    {
        $this->questionGroups->detach($questionGroupToRemove);
    }

    /**
     * Returns the questionGroups
     * 
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\GTN\GtnSurvey\Domain\Model\QuestionGroup> $questionGroups
     */
    public function getQuestionGroups()
    {
        return $this->questionGroups;
    }

    /**
     * Sets the questionGroups
     * 
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\GTN\GtnSurvey\Domain\Model\QuestionGroup> $questionGroups
     * @return void
     */
    public function setQuestionGroups(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $questionGroups)
    {
        $this->questionGroups = $questionGroups;
    }
}
