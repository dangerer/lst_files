<?php

namespace Gtn\GtnLsthooks\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Hook
 *
 * @package TYPO3
 * @subpackage gtn_lsthooks
 */
class Content {

    private $tsSettingsSrRegistration = null;

    function __construct() {
      
        /** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager */
        $configurationManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
        $settings = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $this->tsSettingsSrRegistration = $settings['plugin.']['tx_srfeuserregister_pi1.'];
    }

	/**
	 * Hook
	 */
	//public function modifyDBRow(&$row, $table) {
	//	DebuggerUtility::var_dump($row);
	//	$row['header'] = 'HEADER2';
	//}

    /**
     * @param array $params
     * @param mixed $obj
     * @deprecated Look gtn_impressumlink extension
     */
    public function strReplacer(&$params, &$obj) {
        $agbPid = $this->tsSettingsSrRegistration['lstSettings.']['agbPid'];
        $cObj = new ContentObjectRenderer();
        $linkToAGB = $cObj->typoLink_URL(array('parameter'=>$agbPid, 'forceAbsoluteUrl' => 1));

        $replacements = array(
            '###Datenschutz-linkImpressum###' => '<a href='.$linkToAGB.' target=_blank>Datenschutz</a>',
            '###Datenschutzerklärung-linkImpressum###' => '<a href='.$linkToAGB.' target=_blank>Datenschutzerklärung</a>',
            '###Datenschutzerklärung-linkImpressumCE1798###' => '<a href='.$linkToAGB.'#c1798 target=_blank>Datenschutzerklärung</a>',
            '###privacy-linkImpressum###' => '<a href='.$linkToAGB.' target=_blank>Privacy Policy</a>',
            '###privacy-linkImpressumCE1798###' => '<a href='.$linkToAGB.'#c1798 target=_blank>Privacy Policy</a>'
        );

        foreach ($replacements as $search => $replace) {
            $params['search'] = array_merge($params['search'], array($search));
            $params['replace'] = array_merge($params['replace'], array($replace));
        }
    }

}