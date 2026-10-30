<?php
namespace Gtn\GtnLsthooks\ViewHelpers;

use Gtn\GtnLsthooks\Utils\Utils;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class IfCourseIsFullViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper
{


    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('course', 'int', 'Uid of DCE content element with the course data', false);
    }

    /**
     * @param array|null $arguments
     * @return bool
     */
    protected static function evaluateCondition($arguments = null)
    {
        $courseUid = trim($arguments['course']);

        $utils = GeneralUtility::makeInstance(Utils::class);
        $existing = $utils->getCourseExistingRegistrationNumber($courseUid);
        $max = $utils->getCourseMaxParticipants($courseUid);

        if ($existing >= $max) {
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function render()
    {
        if (static::evaluateCondition($this->arguments)) {
            return $this->renderThenChild();
        }
        return $this->renderElseChild();
    }

}