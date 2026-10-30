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
 * UserAnswer
 */
class UserAnswer extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * feUser
     *
     * @var int

     */
    protected $feUser = null;



    /**
     * questionId
     *
     * @var int

     */
    protected $questionId = 0;


    /**
     * sectionId
     *
     * @var int $sectionId

     */
    protected $sectionId = 0;

    /**
     * value
     *
     * @var string $value

     */
    protected $value = '';

    /**
     * __construct
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    protected function initStorageObjects()
    {
        $this->feUser = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Returns the feuserId
     *
     * @return int $feUser
     */
    public function getFeUser()
    {
        return $this->feUser;
    }

    /**
     * Sets the feuserId
     *
     * @param int $feUser
     */
    public function setFeUser($feUser)
    {
        $this->feUser = $feUser;
    }

    /**
     * Returns the feuserId
     *
     * @return int $questionId
     */
    public function getQuestionId()
    {
        return $this->questionId;
    }

    /**
     * Sets the feuserId
     *
     * @param int $questionId
     * @return void
     */
    public function setQuestionId($questionId)
    {
        $this->questionId = $questionId;
    }

    /**
     * Returns the feuserId
     *
     * @return int $sectionId
     */
    public function getSectionId()
    {
        return $this->sectionId;
    }

    /**
     * Sets the feuserId
     *
     * @param int $questionId
     * @return void
     */
    public function setSectionId($sectionId)
    {
        $this->sectionId = $sectionId;
    }
    /**
     * Returns the feuserId
     *
     * @return string $value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the feuserId
     *
     * @param string $value
     * @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
    }


}
