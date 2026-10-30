<?php
namespace Gtn\KeSearchHooks;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

require_once (ExtensionManagementUtility::extPath('ke_search').'Classes/indexer/types/class.tx_kesearch_indexer_types_page.php');

class TypesLstProduct extends \tx_kesearch_indexer_types_page {

	public function getPagelist($startingPointsRecursive = '', $singlePages = '')
	{
		$pageUidArray = parent::getPagelist($startingPointsRecursive, $singlePages);
		// delete parent pages from result list. So, we will have only SUB-pages of $startingPointsRecursive
		$pidsRecursiveRoots = GeneralUtility::trimExplode(',', $startingPointsRecursive, true);
		$pageUidArray = array_diff($pageUidArray, $pidsRecursiveRoots);
		return $pageUidArray;
	}

}