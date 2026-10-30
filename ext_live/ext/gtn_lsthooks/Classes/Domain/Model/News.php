<?php
namespace Gtn\GtnLsthooks\Domain\Model;

/**
 * News model for LST news
 *
 * @package TYPO3
 * @subpackage tx_news
 */
class News extends \GeorgRinger\News\Domain\Model\News
{
    /**
     * @var string
     */
    protected $lstFullybooked;

    /**
     * @return string
     */
    public function getLstFullybooked()
    {
        return $this->lstFullybooked;
    }

    /**
     * @param string $lstFullybooked
     */
    public function setLstFullybooked($lstFullybooked)
    {
        $this->lstFullybooked = $lstFullybooked;
    }



}