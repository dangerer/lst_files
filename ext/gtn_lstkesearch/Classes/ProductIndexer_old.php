<?php
namespace Gtn\KeSearchHooks;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

define('DONOTINDEX', -3);

class ProductIndexer_old
{

    protected $indexerConfigurationKey = 'lstproducts';

	/* @var $fileRepository \TYPO3\CMS\Core\Resource\FileRepository */
	public $fileRepository;

	public $fileext = 'pdf,ppt,doc,xls,docx,xlsx,pptx';

	public $pObj = null;

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
        if ($indexerConfig['type'] == $this->indexerConfigurationKey) {
			// make file repository
			$this->fileRepository = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\FileRepository');
			$content = '';
			$this->pObj = $indexerObject;

            // get pages for indexing: sublevel of selected pages
			$pagesWithProducts = array();
			if ($indexerConfig['sysfolder'] == '')
				return '';
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'pages', ' pid IN ('.$indexerConfig['sysfolder'].') AND hidden = 0 AND deleted = 0 ');
			$pagesCount = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
			if ($pagesCount) {
				$counter = 0;
				while (($page = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
					$pagesWithProducts[] = $page['uid'];
				}

			}
			$counter = 0;
			if (count($pagesWithProducts) > 0) {
				foreach ($pagesWithProducts as $pid) {
					$this->getPageContent($pid);
				}

				// get all the entries to index
				// don't index hidden or deleted elements, but
				// get the elements with frontend user group access restrictions
				// or time (start / stop) restrictions, in order to copy those restrictions to the index.
				$fields = '*';
				$table = 'tt_content';
				$where = 'pid IN (' . implode(',', $pagesWithProducts) . ') AND hidden = 0 AND deleted = 0';
				$groupBy = '';
				$orderBy = '';
				$limit = '';
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $table, $where, $groupBy, $orderBy, $limit);
				$resCount = $GLOBALS['TYPO3_DB']->sql_num_rows($res);

				// Loop through the records and write them to the index.
				if ($resCount) {
					while (($record = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
						// compile the information which should go into the index
						// the field names depend on the table you want to index!
						$title = strip_tags($record['header']);
						$abstract = strip_tags($record['subheader']);
						$content = strip_tags($record['bodytext']);

						$fullContent = $title."\n".$abstract."\n".$content;

						// Link to detail view
						$params = '';

						// Tags
						// If you youse Sphinx, use "_" instead of "#" (configurable in the extension manager)
						$tags = '#product';

						// Additional information
						$additionalFields = array(
							'orig_uid' => $record['uid'],
							'orig_pid' => $record['pid'],
							'sortdate' => $record['title'],
						);

						// add something to the title, just to identify the entries
						// in the frontend
						$title = '[LST PRODUCT] '.$title;

						// ... and store the information in the index
						$indexerObject->storeInIndex(
							$indexerConfig['storagepid'],   // storage PID
							$title,                         // record title
							$this->indexerConfigurationKey, // content type
							$record['pid'], //$indexerConfig['targetpid'],    // target PID: where is the single view?
							$fullContent,                   // indexed content, includes the title (linebreak after title)
							$tags,                          // tags for faceted search
							$params,                        // typolink params for singleview
							$abstract,                      // abstract; shown in result list if not empty
							$record['sys_language_uid'],    // language uid
							$record['starttime'],           // starttime
							$record['endtime'],             // endtime
							$record['fe_group'],            // fe_group
							false,                          // debug only?
							$additionalFields               // additionalFields
						);
						$counter++;
					}
				}
            }
			$content =
				'<p><b>'. $indexerConfig['title'] . ': ' . $counter
				. ' Elements have been indexed.</b></p>';

            return $content;
        }
    }

    // get content from pages
	public function getPageContent($uid)
	{
		// get content elements for this page
		$fields = 'uid, pid, header, bodytext, CType, sys_language_uid, header_layout, fe_group';
		$fileCTypes = array('uploads');

		$whereClauseForCType = GeneralUtility::trimExplode(',', 'text,textmedia,textpic,bullets,table,html,header,uploads');

		$table = 'tt_content';
		$where = 'pid = ' . intval($uid);
		$where .= ' AND (' . $whereClauseForCType . ')';

		$where .= BackendUtility::BEenableFields($table);
		$where .= BackendUtility::deleteClause($table);
		$pageAccessRestrictions = array();

		// add ke_search tags current page
		$tags = '#product';

		// add system categories as tags: TODO: may be need?
		//tx_kesearch_helper::makeSystemCategoryTags($tags, $uid, $table);

		// Compile content for this page from individual content elements with
		// respect to the language.
		// While doing so, fetch also content from attached files and write
		// their content directly to the index.
		$ttContentRows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows($fields, $table, $where);
		$pageContent = array();
		if (count($ttContentRows)) {
			foreach ($ttContentRows as $ttContentRow) {
				$content = '';

				// index header
				// add header only if not set to "hidden"
				if ($ttContentRow['header_layout'] != 100) {
					$content .= strip_tags($ttContentRow['header']) . "\n";
				}

				// index content of this content element and find attached or linked files.
				// Attached files are saved as file references, the RTE links directly to
				// a file, thus we get file objects.
				// Files go into the index no matter if "index_content_with_restrictions" is set
				// or not, that means even if protected content elements do not go into the index,
				// files do. Since each file gets it's own index entry with correct access
				// restrictons, that's no problem from a access permission perspective (in fact, it's a feature).
				if (in_array($ttContentRow['CType'], $fileCTypes)) {
					$fileObjects = $this->findAttachedFiles($ttContentRow);
				} else {
					$fileObjects = $this->findLinkedFilesInRte($ttContentRow);
					$content .= $this->getContentFromContentElement($ttContentRow) . "\n";
				}

				// index the files found
				//if (!$pageAccessRestrictions['hidden']) {
				$this->indexFiles($fileObjects, $ttContentRow, 0, $tags);
				//}

				// add content from this content element to page content
				// ONLY if this content element is not access protected
				// or protected content elements should go into the index
				// by configuration.
				//if ($this->indexerConfig['index_content_with_restrictions'] == 'yes'
				//	|| $ttContentRow['fe_group'] == ''
				//	|| $ttContentRow['fe_group'] == '0'
				//) {
				//	$pageContent[$ttContentRow['sys_language_uid']] .= $content;
				//}
			}
		} else {
			//$this->counterWithoutContent++;
			return;
		}


		// store record in index table
		if (count($pageContent)) {
			foreach ($pageContent as $language_uid => $content) {
				if (!$pageAccessRestrictions['hidden'] && $this->checkIfpageShouldBeIndexed($uid, $language_uid)) {
					// overwrite access restrictions with language overlay values
					$accessRestrictionsLanguageOverlay = $pageAccessRestrictions;
					$pageAccessRestrictions['fe_group'] = $indexEntryDefaultValues['feGroupsPages'];
					if ($language_uid > 0) {
						if ($this->cachedPageRecords[$language_uid][$uid]['fe_group']) {
							$accessRestrictionsLanguageOverlay['fe_group'] =
								$this->cachedPageRecords[$language_uid][$uid]['fe_group'];
						}
						if ($this->cachedPageRecords[$language_uid][$uid]['starttime']) {
							$accessRestrictionsLanguageOverlay['starttime'] =
								$this->cachedPageRecords[$language_uid][$uid]['starttime'];
						}
						if ($this->cachedPageRecords[$language_uid][$uid]['endtime']) {
							$accessRestrictionsLanguageOverlay['endtime'] =
								$this->cachedPageRecords[$language_uid][$uid]['endtime'];
						}
					}

					$this->pObj->storeInIndex(
						$indexerConfig['storagepid'],                               // storage PID
						$this->cachedPageRecords[$language_uid][$uid]['title'],     // page title
						$indexEntryDefaultValues['type'],                           // content type
						$indexEntryDefaultValues['uid'],                            // target PID / single view
						$content,                        // indexed content, includes the title (linebreak after title)
						$tags,                                                      // tags
						$indexEntryDefaultValues['params'],                         // typolink params for singleview
						$this->cachedPageRecords[$language_uid][$uid]['abstract'],  // abstract
						$language_uid,                                              // language uid
						$accessRestrictionsLanguageOverlay['starttime'],            // starttime
						$accessRestrictionsLanguageOverlay['endtime'],              // endtime
						$accessRestrictionsLanguageOverlay['fe_group'],             // fe_group
						$indexEntryDefaultValues['debugOnly'],                      // debug only?
						$additionalFields                                           // additional fields added by hooks
					);
					$this->counter++;
				}
			}
		}

		return;
	}

	public function findAttachedFiles($ttContentRow)
	{
		// get files attached to the content element
		$fileReferenceObjects = $this->fileRepository->findByRelation('tt_content', 'media', $ttContentRow['uid']);
		return $fileReferenceObjects;
	}

	public function findLinkedFilesInRte($ttContentRow)
	{
		$fileObjects = array();
		// check if there are links to files in the rte text
		/* @var $rteHtmlParser \TYPO3\CMS\Core\Html\RteHtmlParser */
		$rteHtmlParser = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Html\\RteHtmlParser');

		// only TYPO3 8.7 and  >
		/** @var \TYPO3\CMS\Core\LinkHandling\LinkService $linkService */
		$linkService = GeneralUtility::makeInstance(\TYPO3\CMS\Core\LinkHandling\LinkService::class);
		$blockSplit = $rteHtmlParser->splitIntoBlock('A', $ttContentRow['bodytext'], 1);
		foreach ($blockSplit as $k => $v) {
			list($attributes) = $rteHtmlParser->get_tag_attributes($rteHtmlParser->getFirstTag($v), true);
			if (!empty($attributes['href'])) {
				$hrefInformation = $linkService->resolve($attributes['href']);
				if ($hrefInformation['type'] === \TYPO3\CMS\Core\LinkHandling\LinkService::TYPE_FILE) {
					$fileObjects[] = $hrefInformation['file'];
				}
			}
		}
		return $fileObjects;
	}

	public function getContentFromContentElement($ttContentRow)
	{
		// bodytext
		$bodytext = $ttContentRow['bodytext'];

		// following lines prevents having words one after the other like: HelloAllTogether
		$bodytext = str_replace('<td', ' <td', $bodytext);
		$bodytext = str_replace('<br', ' <br', $bodytext);
		$bodytext = str_replace('<p', ' <p', $bodytext);
		$bodytext = str_replace('<li', ' <li', $bodytext);

		if ($ttContentRow['CType'] == 'table') {
			// replace table dividers with whitespace
			$bodytext = str_replace('|', ' ', $bodytext);
		}
		$bodytext = strip_tags($bodytext);

		return $bodytext;
	}

	public function indexFiles($fileObjects, $ttContentRow, $feGroupsPages, $tags)
	{
		// combine group access restrictons from page(s) and content element
		//$feGroups = $this->getCombinedFeGroupsForContentElement($feGroupsPages, $ttContentRow['fe_group']);
		$feGroups = 0;

		if (count($fileObjects) && $feGroups != DONOTINDEX) {
			// loop through files
			foreach ($fileObjects as $fileObject) {
				$isInList = \TYPO3\CMS\Core\Utility\GeneralUtility::inList(
					$this->fileext,
					$fileObject->getExtension()
				);

				// check if the file extension fits in the list of extensions
				// to index defined in the indexer configuration
				if ($isInList) {
					// get file path and URI
					$filePath = $fileObject->getForLocalProcessing(false);

					/* @var $fileIndexerObject tx_kesearch_indexer_types_file */
					$fileIndexerObject = GeneralUtility::makeInstance(
						'tx_kesearch_indexer_types_file',
						$this->pObj
					);

					// add tags from linking page to this index record?
					//if (!$this->indexerConfig['index_use_page_tags_for_files']) {
					//	$tags = '';
					//}

					// add tag to identify this index record as file
					tx_kesearch_helper::makeTags($tags, array('file'));

					// get file information and  file content (using external tools)
					// write file data to the index as a seperate index entry
					// count indexed files, add it to the indexer output
					if (!file_exists($filePath)) {
						$this->addError('Could not index file ' . $filePath . ' (file does not exist).');
					} else {
						if ($fileIndexerObject->fileInfo->setFile($fileObject)) {
							if (($content = $fileIndexerObject->getFileContent($filePath))) {
								$this->storeFileContentToIndex(
									$fileObject,
									$content,
									$fileIndexerObject,
									$feGroups,
									$tags,
									$ttContentRow
								);
								$this->fileCounter++;
							} else {
								$this->addError($fileIndexerObject->getErrors());
								$this->addError('Could not index file ' . $filePath . '.');
							}
						}
					}
				}
			}
		}
	}




}