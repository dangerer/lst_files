<?php
declare(strict_types=1);

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
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
/**
 * Extendttcontent
 */
class Extendttcontent extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * translatedRecords
     */
    protected $translatedRecords = null;

    /**
     * haveFile
     */
    protected $haveFile = 1;

    /**
     * new
     *
     * @var bool
     */
    protected $new = false;

    /**
     * forAllLanguage
     *
     * @var bool
     */
    protected $forAllLanguage = false;

    /**
     * uid
     * @var int
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $uid;

    /**
     * page
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \Hafs\Downloadlst\Domain\Model\Extendpages
     */
    protected $page;

    /**
     * Categories
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\Category>
     */
    protected $categories;

    /**
     * media
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $media;

    /**
     * sorting
     * @var int
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $sorting;

    /**
     * ctype
     * @var string
     *
     */
    protected $ctype;

    /**
     * date
     * @var \DateTime
     *
     */
    protected $date;

    /**
     * tstamp
     * @var \DateTime
     *
     */
    protected $tstamp;

    /**
     * header
     * @var string
     *
     */
    protected $header;

    /**
     * headerLink
     * @var string
     *
     */
    protected $headerLink;

    /**
     * headerLayout
     * @var string
     *
     */
    protected $headerLayout;

    /**
     * short
     * @var string
     *
     */
    protected $short;

    /**
     * bodytext
     * @var string
     *
     */
    protected $bodytext;

    /**
     * image
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     *
     */
    protected $image;
    
    /**
     * colPos
     * @var int
     *
     */
    protected $colPos;

    /**
     * rowDescription
     * @var string
     *
     */
    protected $rowDescription = '';
   
    /**
     * __construct
     *
     */
    public function __construct() {
        $this->initStorageObjects();
    }

    /**
     * Initializes all \TYPO3\CMS\Extbase\Persistence\ObjectStorage properties.
     *
     * @return void
     */
    protected function initStorageObjects() {

        $this->categories = new ObjectStorage();
        $this->media = new ObjectStorage();
        $this->image = new ObjectStorage();
       
    }

    /**
     * Returns the translatedRecords
     *
     * @return bool $translatedRecords
     */
    public function getTranslatedRecords()
    {
        return $this->translatedRecords;
    }

    /**
     * Sets the translatedRecords
     *
     * @param bool $translatedRecords
     * @return void
     */
    public function setTranslatedRecords($translatedRecords)
    {
        $this->translatedRecords = $translatedRecords;
    }

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

    /**
     * Returns the forAllLanguage
     *
     * @return bool $forAllLanguage
     */
    public function getForAllLanguage()
    {
        return $this->forAllLanguage;
    }

    /**
     * Sets the forAllLanguage
     *
     * @param bool $forAllLanguage
     * @return void
     */
    public function setForAllLanguage($forAllLanguage)
    {
        $this->forAllLanguage = $forAllLanguage;
    }

    /**
     * Returns the boolean state of forAllLanguage
     *
     * @return bool
     */
    public function isForAllLanguage()
    {
        return $this->forAllLanguage;
    }

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
     * Getter for uid.
     *
     * @return int|null The uid or NULL if none set yet.
     */
    public function getUid(): ?int
    {
        if ($this->uid !== null) {
            return (int)$this->uid;
        }
        return null;
    }

    /**
     * sets the haveFile
     *
     * @param int $haveFile
     * @return void
     */
    public function setHaveFile($haveFile) {
        $this->haveFile = $haveFile;
    }

    /**
     * returns the haveFile
     *
     * @return int $haveFile
     */
    public function getHaveFile() {
        return $this->haveFile;
    }

    /**
     * sets a page
     *
     * @param \Hafs\Downloadlst\Domain\Model\Extendpages $page
     * @return void
     */
    public function setPage(\Hafs\Downloadlst\Domain\Model\Extendpages $page) {
        $this->page = $page;
    }

    /**
     * Returns the categories
     *
     * @return \Hafs\Downloadlst\Domain\Model\Extendpages $page
     */
    public function getPage() {
        return $this->page;
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
     * sets the date
     *
     * @param \DateTime $date
     * @return void
     */
    public function setDate($date) {
        $this->date = $date;
    }

    /**
     * returns the date
     *
     * @return \DateTime $date
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * sets the tstamp
     *
     * @param \DateTime $tstamp
     * @return void
     */
    public function setTstamp($tstamp) {
        $this->tstamp = $tstamp;
    }

    /**
     * returns the tstamp
     *
     * @return \DateTime $tstamp
     */
    public function getTstamp() {
        return $this->tstamp;
    }

    /**
     * Returns the header
     *
     * @return string $header
     */
    public function getHeader() {
        return $this->header;
    }

    /**
     * sets the header
     *
     * @param string $header
     * @return void
     */
    public function setHeader($header) {
        $this->header = $header;
    }

    /**
     * returns the headerLayout
     *
     * @return string $headerLayout
     */
    public function getHeaderLayout() {
        return $this->headerLayout;
    }

    /**
     * sets the headerLayout
     *
     * @param string $headerLayout
     * @return void
     */
    public function setHeaderLayout($headerLayout) {
        $this->headerLayout = $headerLayout;
    }

    /**
     * returns the short
     *
     * @return string $short
     */
    public function getShort() {
        return $this->short;
    }

    /**
     * sets the short
     *
     * @param string $short
     * @return void
     */
    public function setShort($short) {
        $this->short = $short;
    }


    /**
     * Returns the bodytext
     *
     * @return string $bodytext
     */
    public function getBodytext() {
        return $this->bodytext;
    }

  
    /**
     * Returns the colPos
     *
     * @return int $colPos
     */
    public function getColPos() {
        return $this->colPos;
    }

    /**
     * sets the ctype
     *
     * @param string $ctype
     * @return void
     */
    public function setCtype($ctype) {
        $this->ctype = $ctype;
    }

    /**
     * returns the ctype
     *
     * @return string $ctype
     */
    public function getCtype() {
        return $this->ctype;
    }


    /**
     * Adds a Category
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\Category $category
     * @return void
     */
    public function addCategory(\TYPO3\CMS\Extbase\Domain\Model\Category $category) {
        $this->categories->attach($category);
    }

    /**
     * Removes a Category
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\Category $categoryToRemove The Category to be removed
     * @return void
     */
    public function removeCategory(\TYPO3\CMS\Extbase\Domain\Model\Category $categoryToRemove) {
        $this->categories->detach($categoryToRemove);
    }

    /**
     * Returns the categories
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\Category> $categories
     */
    public function getCategories() {
        return $this->categories;
    }

    /**
     * Sets the categories
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\Category> $categories
     */
    public function setCategories(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $categories) {
        $this->categories = $categories;
    }

    /**
     * Adds a Media
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $media
     * @return void
     */
    public function addMedia(\TYPO3\CMS\Extbase\Domain\Model\FileReference $media) {
        $this->media->attach($media);
    }

    /**
     * Removes a Media
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $mediaToRemove The Category to be removed
     * @return void
     */
    public function removeMedia(\TYPO3\CMS\Extbase\Domain\Model\FileReference $mediaToRemove) {
        $this->media->detach($mediaToRemove);
    }

    /**
     * Returns the Media
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $media
     */
    public function getMedia() {
        return $this->media;
    }

    /**
     * Sets the Media
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $media
     */
    public function setMedia(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $media) {
        $this->media = $media;
    }

    /**
     * Adds a image
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
     * @return void
     */
    public function addImage(\TYPO3\CMS\Extbase\Domain\Model\FileReference $image) {
        $this->image->attach($image);
    }

    /**
     * Removes a image
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $imageToRemove The Category to be removed
     * @return void
     */
    public function removeImage(\TYPO3\CMS\Extbase\Domain\Model\FileReference $imageToRemove) {
        $this->image->detach($imageToRemove);
    }

    /**
     * Returns the image
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $image
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * Sets the image
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $image
     */
    public function setImage(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $image) {
        $this->image = $image;
    }

    /**
     * Returns the rowDescription
     *
     * @return string $rowDescription
     */
    public function getRowDescription() {
        return $this->rowDescription;
    }   

    /**
     * sets the  bodytext
     *
     * @param string $bodytext
     * @return void 
     */
    public function setBodytext($bodytext) {
        $this->bodytext = $bodytext;
    }

    /**
     * sets the rowDescription
     *
     * @param string $rowDescription
     * @return void
     */
    public function setRowDescription($rowDescription) {
        $this->rowDescription = $rowDescription;
    }
}
