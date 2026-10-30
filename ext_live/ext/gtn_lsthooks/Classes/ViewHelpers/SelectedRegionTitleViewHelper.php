<?php
namespace Gtn\GtnLsthooks\ViewHelpers;

use Gtn\GtnLsthooks\Utils\Utils;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class SelectedRegionTitleViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper {

    public function initializeArguments()
    {
        $this->registerArgument('region', 'integer', 'Region Uid', true);
    }
	/**
	 * @param integer $region
	 * @return string
	 */
	public function render() {

	    $region = $this->arguments['region'];

		if (!$region) {
            $utils = GeneralUtility::makeInstance(Utils::class);
            $region = $utils->getSelectedRegion();
		}
		// get region title from categories (by needed language)
      //  $categoryRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Gtn\GtnLsthooks\Domain\Repository\CategoryRepository::class);

		/** @var \TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository $categoryRepository */
		//$categoryRepository = ObjectManager::get(\TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository::class);
		/** @var \TYPO3\CMS\Extbase\Domain\Model\Category $selectedCategory */
		//echo $GLOBALS['TSFE']->sys_language_uid.'+';
		//echo $GLOBALS['TSFE']->config['config']['language'].'=';
		//echo $GLOBALS['TSFE']->lang.'+';
/*		if ($GLOBALS['TSFE']->sys_language_uid > 0) {
			$query = $categoryRepository->createQuery();
			$query->getQuerySettings()->setRespectStoragePage(false)->setRespectSysLanguage(false)->setLanguageUid($GLOBALS['TSFE']->sys_language_uid)->setLanguageMode('strict');
			$query->matching(
				$query->logicalAnd(
					$query->equals('t3_origuid', $region),
					$query->in('sys_language_uid', array($GLOBALS['TSFE']->sys_language_uid))
				)
			);
			$selectedCategory = $query->execute()->getFirst();
		} else {*/

        $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
        $langUid = $languageAspect->getId();
        if ($langUid > 0) {
//            SELECT * FROM sys_category where l10n_parent = 15 AND sys_language_uid = 1
            $selectedCategory = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
                '*',
                'sys_category',
                'l10n_parent = ' . (int)$region . ' AND sys_language_uid = '.$langUid
            );
        } else {
			$selectedCategory = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
				'*',
				'sys_category',
				'uid = ' . (int)$region
			);
		}
		if ($selectedCategory) {
            return $selectedCategory['title'];
        }
		return '';
	}

}