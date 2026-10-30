<?php
namespace Gtn\GtnLsthooks\ViewHelpers;

use TYPO3\CMS\Extbase\Object\ObjectManager;

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
            // region from user's data is MAIN
			/*if ($GLOBALS["TSFE"]->fe_user->user["uid"] > 0) {
                $region = $GLOBALS["TSFE"]->fe_user->user['lst_region'];
            } else {
                $region = ($_COOKIE['selectedRegion'] ? $_COOKIE['selectedRegion'] : 0);
            }*/
            // region frm Cookie is MAIN
            $region = ($_COOKIE['selectedRegion'] ? $_COOKIE['selectedRegion'] : 0);
            if (!$region && $GLOBALS["TSFE"]->fe_user->user["uid"] > 0) {
                $region = $GLOBALS["TSFE"]->fe_user->user['lst_region'];
            }
            if ($region === 'null') // it is possible from JS
                $region = 0;
		}
		// get region title from categories (by needed language)
        $categoryRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class)
            ->get(\TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository::class);

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
			$selectedCategory = $categoryRepository->findByUid($region);
		//}
		if ($selectedCategory)
			return $selectedCategory->getTitle();
		return '';
	}

}