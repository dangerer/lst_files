<?php
namespace Gtn\KeSearchHooks;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

define('DONOTINDEX', -3);

class ProductIndexer
{

    protected $indexerConfigurationKey = 'lstproducts';

    public $temp = 'temp';



    /**
     * Adds the custom indexer to the TCA of indexer configurations, so that
     * it's selectable in the backend as an indexer type when you create a
     * new indexer configuration.
     *
     * @param array $params
     * @param type $pObj
     */
    public function registerIndexerConfiguration(&$params, $pObj)
    {
        // Set a name and an icon for your indexer.
        $customIndexer = array(
            'LST Products Indexer (ext:gtn_lstkesearch)',
            $this->indexerConfigurationKey,
            ExtensionManagementUtility::extPath('gtn_lstkesearch') . 'customnews-indexer-icon.gif'
        );
        $params['items'][] = $customIndexer;
    }


    /**
     * Custom indexer for ke_search
     *
     * @param   array $indexerConfig Configuration from TYPO3 Backend
     * @param   array $indexerObject Reference to indexer class.
     * @return  string Message containing indexed elements
     * @author  Christian Buelter <christian.buelter@pluswerk.ag>
     */
    public function customIndexer(&$indexerConfig, &$indexerObject)
    {
//        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($indexerConfig);
        if ($indexerConfig['type'] == $this->indexerConfigurationKey) {
			$indexer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Gtn\\KeSearchHooks\\TypesLstProduct', $indexerObject);
			return $indexer->startIndexing();
		}
    }

}