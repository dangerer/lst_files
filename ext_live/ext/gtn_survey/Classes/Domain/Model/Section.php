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
 * Section
 */
class Section extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
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
}
