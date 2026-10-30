<?php
namespace Gtn\GtnLsthooks\ViewHelpers;

use Gtn\GtnLsthooks\Utils\Utils;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class ExplodeViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{


    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('uids', 'string', '"delimiter" separated string', true);
        $this->registerArgument('delimiter', 'string', 'delimiter (comma by default)', false, ',');
    }

    /**
     * @return mixed
     */
    public function render()
    {
        $uids = $this->arguments['uids'];
        $delimiter = $this->arguments['delimiter'];

        return GeneralUtility::trimExplode($delimiter, $uids, true);
    }

}