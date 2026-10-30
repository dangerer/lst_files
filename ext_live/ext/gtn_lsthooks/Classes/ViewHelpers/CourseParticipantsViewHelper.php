<?php
namespace Gtn\GtnLsthooks\ViewHelpers;

use Gtn\GtnLsthooks\Utils\Utils;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class CourseParticipantsViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{


    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('course', 'int', 'Uid of DCE content element with the course data', true);
        $this->registerArgument('get', 'string', 'current; max - what we need to get (current by default)', false, 'current');
    }

    /**
     * @return mixed
     */
    public function render()
    {
        $courseUid = trim($this->arguments['course']);
        $get = trim($this->arguments['get']);
        $utils = GeneralUtility::makeInstance(Utils::class);
        switch ($get) {
            case 'max':
                $return = $utils->getCourseMaxParticipants($courseUid);
                break;
            case 'current':
            default:
                $return = $utils->getCourseExistingRegistrationNumber($courseUid);
        }

        return $return;
    }

}