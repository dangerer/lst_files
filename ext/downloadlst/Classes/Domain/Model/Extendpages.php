<?php

namespace Hafs\Downloadlst\Domain\Model;


/***
 *
 * This file is part of the "Download list" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2018
 *
 ***/

/**
 * Extendpages
 */
class Extendpages extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * uid
     * @var int
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $uid;
    /**
     * pid
     * @var int
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $pid;
    /**
     * sorting
     * @var int
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $sorting;
    /**
     * title
     * @var string
     *
     */
    protected $title;
    /**
     * subtitle
     * @var string
     *
     */
    protected $subtitle;

    /**
     * sets the uid
     *
     * @param int $uid
     * @return void
     */
    public function setUid($uid) {
        $this->uid = $uid;
    }

    /**
     * returns the uid
     *
     * @return int|null $uid
     */
    public function getUid(): ?int {
        return $this->uid;
    }

    /**
     * Returns the pid
     *
     * @return int $pid
     */
    public function getPid(): ?int {
        return $this->pid;
    }
    /**
     * Returns the sorting
     *
     * @return int $sorting
     */
    public function getSorting() {
        return $this->sorting;
    }
    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle() {
        return $this->title;
    }
    /**
     * Returns the subtitle
     *
     * @return string $subtitle
     */
    public function getSubtitle() {
        return $this->subtitle;
    }
    
    /**
     * new
     *
     * @var bool
     */
    protected $new = false;

    /**
     * Returns the new
     *
     * @return bool $new
     */
    public function getNew()
    {
        return $this->new;
    }

    /**
     * Sets the new
     *
     * @param bool $new
     * @return void
     */
    public function setNew($new)
    {
        $this->new = $new;
    }

    /**
     * Returns the boolean state of new
     *
     * @return bool
     */
    public function isNew()
    {
        return $this->new;
    }
}
