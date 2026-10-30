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
 * Answer
 */
class Answer extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * text
     * 
     * @var string
     */
    protected $text = '';

    /**
    * text
    *
    * @var string
    */
    protected $engText = '';


    /**
     * sectionName
     * 
     * @var string
     */
    protected $sectionName = '';


    /**
     * Returns the text
     *
     * @return string $engText
     */
    public function getEngText()
    {
        return $this->engText;
    }

    /**
     * Sets the text
     *
     * @param string $engText
     * @return void
     */
    public function setEngText($engText)
    {
        $this->engText = $engText;
    }

    /**
     * Returns the text
     * 
     * @return string $text
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Sets the text
     * 
     * @param string $text
     * @return void
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * Returns the sectionName
     * 
     * @return string $sectionName
     */
    public function getSectionName()
    {
        return $this->sectionName;
    }

    /**
     * Sets the sectionName
     * 
     * @param string $sectionName
     * @return void
     */
    public function setSectionName($sectionName)
    {
        $this->sectionName = $sectionName;
    }
}
