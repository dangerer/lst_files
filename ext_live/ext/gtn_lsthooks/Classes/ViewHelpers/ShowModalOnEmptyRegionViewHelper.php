<?php
namespace Gtn\GtnLsthooks\ViewHelpers;


use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class ShowModalOnEmptyRegionViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper {

    public function initializeArguments()
    {
    }

	/**
	* @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	* @TYPO3\CMS\Extbase\Annotation\Inject
	*/
	protected $configurationManager;

	/**
	 * @return string
	 */
	public function render() {
		$configurationManager = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::class);
		$ts = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		$settings = $ts['plugin.']['lstHooks.'];
		if ($settings['pagesForShowSelectRegionModalIfNoRegionSelected'] != '') {
			$pids = GeneralUtility::trimExplode(',', $settings['pagesForShowSelectRegionModalIfNoRegionSelected']);
			$currPid = $GLOBALS['TSFE']->id;
			if (in_array($currPid, $pids))
				return 'true';
		}
		return 'false';
	}

}