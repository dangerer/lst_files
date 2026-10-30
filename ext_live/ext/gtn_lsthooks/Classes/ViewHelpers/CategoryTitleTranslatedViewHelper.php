<?php
namespace Gtn\GtnLsthooks\ViewHelpers;

use Gtn\GtnLsthooks\Utils\Utils;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class CategoryTitleTranslatedViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{


    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('categoryUid', 'int', 'Catgeory uid', true);
    }

    /**
     * @return mixed
     */
    public function render()
    {
        $categoryUid = trim($this->arguments['categoryUid']);
        $utils = GeneralUtility::makeInstance(Utils::class);
        $return = $utils->getCategoryTitle($categoryUid);

        return $return;
    }

}