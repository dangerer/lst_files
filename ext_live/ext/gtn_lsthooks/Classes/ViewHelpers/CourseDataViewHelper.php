<?php
namespace Gtn\GtnLsthooks\ViewHelpers;

use Gtn\GtnLsthooks\Utils\Utils;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class CourseDataViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{


    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('course', 'int', 'Uid of DCE content element with the course data', true);
    }

    /**
     * @return mixed
     */
    public function render()
    {
        $courseUid = trim($this->arguments['course']);
        $utils = GeneralUtility::makeInstance(Utils::class);
        $return = $utils->getCourseData($courseUid);

        return $return;
    }

}