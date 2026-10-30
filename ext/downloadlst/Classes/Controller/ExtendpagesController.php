<?php
namespace Hafs\Downloadlst\Controller;

/***
 *
 * This file is part of the "Download list" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2018
 *
 ***/

use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility as Debug;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Core\Context\Context;
use Hafs\Downloadlst\Domain\Repository\ExtendttcontentRepository;
use Hafs\Downloadlst\Domain\Repository\ExtendpagesRepository;

/**
 * ExtendpagesController
 */
class ExtendpagesController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

	/**
	 * @var array
	 */
	protected $pagetree = [];


	public function __construct(
		private readonly PageRepository $pageRepository,
		private readonly ExtendpagesRepository $extendpagesRepository,
		private readonly ExtendttcontentRepository $extendttcontentRepository,
	) {}

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction()
	{
		$contentObj = $this->configurationManager->getContentObject();
		$data = $contentObj->data;

		$fileRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\FileRepository');

		$feUserPID = $GLOBALS['TSFE']->fe_user->user['pid'];
		$feUserGroup = $GLOBALS['TSFE']->fe_user->user['usergroup'];

		if ($_GET["d"] == 1) {
			\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($feUserPID, "feuserPID");
			\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($feUserGroup, "feuserGroup");
		}
		if ($feUserGroup){
			$sqlSubGroup = "SELECT uid,subgroup FROM fe_groups 
                                WHERE uid IN (".$feUserGroup.")";
			$subgroupCond = '';
			$subgroupResult = $GLOBALS['TYPO3_DB']->sql_query($sqlSubGroup);
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($subgroupResult)){
				$subgroup[] = $row['subgroup'];
				$subgroupCond .= ' OR fe_group LIKE "%'.$row['subgroup'].'%"';
			}
			$usergroupCond = ' AND (fe_group LIKE "%'.$feUserGroup.'%" '.$subgroupCond.' OR fe_group = "0" OR fe_group = "")';

		}
		$categoryID = [];

		if ($feUserPID > 0){
			$sqlCategory = "SELECT uid,feuserpage FROM sys_category 
                                WHERE feuserpage = ".$feUserPID;

			$categoryResult = $GLOBALS['TYPO3_DB']->sql_query($sqlCategory);
			if ($_GET["d"] == 1) {
				\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($sqlCategory, "sqlCategory");
			}
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($categoryResult)){
				$categoryID[] = $row['uid'];
			}
		}

		if ($_GET["d"] == 1) {
			\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($categoryID, "categoryID");
		}
		$sys_categoryCond = '';
		if(count($categoryID)>0){
			$sys_categoryCond = ' AND sys_category_record_mm.uid_local IN ('.implode(',', $categoryID).')';
		}
		$pages = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query('pages.uid','sys_category','sys_category_record_mm','pages'," AND sys_category_record_mm.tablenames='pages' AND sys_category_record_mm.fieldname='categories'".$sys_categoryCond,'', 'pages.sorting');

		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($pages)){
			$pagesArray[] = $row['uid'];
		}
		if ($_GET["d"] == 1) {
			\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($pages, "pages");
		}

		$categoryCond = '';
		if(count($categoryID) == 0){
			$categoryCond = ' AND categories = 0';
		}

		if(is_array($pagesArray) && count($pagesArray) && count($categoryID) > 0){
			$categoryCond = ' AND (categories=0 OR uid IN ('.implode(',', $pagesArray).'))';
		}else if(count($categoryID) > 0){
			$categoryCond = ' AND categories = 0';
		}
		$typo3Version = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class);
		$T3_Version = VersionNumberUtility::convertVersionStringToArray($typo3Version->getVersion());

		$this->view->assign('TYPO3_VERSION',$T3_Version['version_main']);

		$languageKeys = explode(',', str_replace('EN', 'GB', $this->settings['languageKeys']) );
		$languageIds = explode(',', $this->settings['languageIds']);
		$languageArray = array_combine($languageIds, $languageKeys);

		// Debug::var_dump($languageArray);
		$currentPageid = ($data['pages']!=''?$data['pages']:$GLOBALS['TSFE']->id);
		$currentLanguageId = 0;

		if($this->settings['selectedContent']){

			$selectedContent = explode(',', $this->settings['selectedContent']);
			$selectedContentObj = $this->extendttcontentRepository->findByUids($selectedContent,$categoryID);

			foreach ($selectedContentObj as $contentskey => $contentsvalue) {
				$hasFile = 1;
				if($contentsvalue->getImage()){
					if($contentsvalue->getImage()->count() == 0){
						$hasFile = 0;
					}
				}else if(is_null($contentsvalue->getImage())){
					$hasFile = 0;
				}
				// Get content elements of other language
				$sqlOther = "SELECT uid,header,image,sys_language_uid,bodytext,rowDescription FROM tt_content 
                        WHERE pid = ".$contentsvalue->getPid()." AND deleted=0 AND hidden=0 AND (sys_language_uid != 0 AND sys_language_uid IN (" .$this->settings['languageIds']. ")) AND l18n_parent = " . $contentsvalue->getUid();

				$contentResOther = $GLOBALS['TYPO3_DB']->sql_query($sqlOther);
				$contentElementsOther = [];
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($contentResOther)){

					if($row['image']){
						$fileObjects = $fileRepository->findByRelation('tt_content', 'image', $row['uid']);
						$pictures = array();
						foreach ($fileObjects as $fileKey => $fileValue) {
							//Get reference file property
							// array_push($pictures, $fileValue->getReferenceProperties());
							array_push($pictures, $fileValue->getPublicUrl());
							// Debug::var_dump($fileValue->getPublicUrl());
						}
						$row['imageUrl'] = $pictures;
						$hasFile = 1;
					}
					$row['flag'] = $languageArray[$row['sys_language_uid']];
					$contentElementsOther[$row['sys_language_uid']] = $row;

				}
				$contentsvalue->setTranslatedRecords($contentElementsOther);
				if($hasFile==0){
					$contentsvalue->setHaveFile(0);
				}
				$context  = GeneralUtility::makeInstance(Context::class);

				$langId = $context->getPropertyFromAspect('language', 'id');

				if($langId > 0){

					/*Change header value for other than default language*/
					if($contentElementsOther[$langId]['header'] != '' ){
						$contentsvalue->setHeader($contentElementsOther[$langId]['header']);
					}
					if($contentElementsOther[$langId]['bodytext'] != ''){
						$contentsvalue->setBodytext($contentElementsOther[$langId]['bodytext']);
					}
					if($contentElementsOther[$langId]['rowDescription'] != ''){
						$contentsvalue->setRowDescription($contentElementsOther[$langId]['rowDescription']);
					}

				}
			}

			// DebugUtility::debug($selectedContentObj);

			$this->view->assign('selectedContent',1);
			$this->view->assign('selectedContentObj',$selectedContentObj);

		}else{

			$temp = array();
			$contentInSubpages = 0;
			$result = $this->addlevel($temp,$keyy,$valuee,$categoryCond,$usergroupCond,$currentLanguageId,$currentPageid,$contentInSubpages,$languageArray,$categoryID);
			$this->view->assign('subpages', $result);


		}

		$this->view->assign('currentFlag', $languageArray[$currentLanguageId]);

		return $this->htmlResponse();
	}


	function addlevel($subpages,$keyy=0,$valuee=0,$categoryCond,$usergroupCond,$currentLanguageId,$currentPageid,$contentInSubpages,$languageArray,$categoryID){

		$fileRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\FileRepository');

		unset($subpages);
		unset($key);
		unset($value);
		$subpages = $this->pageRepository->getMenu($currentPageid, "uid, title, new",'sorting',' AND pages.hidden=0 AND doktype IN (1,54)'.$categoryCond .''.$usergroupCond);

		// Get content elements of default language (1st Level page)
		foreach ($subpages as $key => $value) {

			// Get content elements of default language (1st Level page)
			$sqlContent = "SELECT uid,header,media FROM tt_content 
                    WHERE pid = ".$value['uid']." AND hidden=0 AND deleted=0 AND sys_language_uid = ".$currentLanguageId;
			$contentRes = $GLOBALS['TYPO3_DB']->sql_query($sqlContent);
			$contentElementsFirstLevel = [];
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($contentRes)){
				$contentElementsFirstLevel[] = $row['uid'];
			}
			if(count($contentElementsFirstLevel)){
				$contentsFirstLevel = $this->extendttcontentRepository->findByUids($contentElementsFirstLevel,$categoryID,$value['uid']);

				$subpages[$key]['contentElements']=$contentsFirstLevel;
				foreach ($contentsFirstLevel as $contentskey => $contentsvalue) {
					$hasFile = 1;
					if($contentsvalue->getImage()){
						if($contentsvalue->getImage()->count() == 0){
							$hasFile = 0;
						}
					}else if(is_null($contentsvalue->getImage())){
						$hasFile = 0;
					}
					// Get content elements of other language
					$sqlOther = "SELECT uid,header,image,sys_language_uid,bodytext,rowDescription FROM tt_content 
                            WHERE pid = ".$value['uid']." AND deleted=0 AND hidden=0 AND (sys_language_uid != 0 AND sys_language_uid IN (" .$this->settings['languageIds']. ")) AND l18n_parent = " . $contentsvalue->getUid();

					$contentResOther = $GLOBALS['TYPO3_DB']->sql_query($sqlOther);
					$contentElementsOther = [];
					while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($contentResOther)){

						if($row['image']){
							$fileObjects = $fileRepository->findByRelation('tt_content', 'image', $row['uid']);
							$pictures = array();
							foreach ($fileObjects as $fileKey => $fileValue) {
								//Get reference file property
								// array_push($pictures, $fileValue->getReferenceProperties());
								array_push($pictures, $fileValue->getPublicUrl());
								// Debug::var_dump($fileValue->getPublicUrl());
							}
							$row['imageUrl'] = $pictures;
							$hasFile = 1;
						}
						$row['flag'] = $languageArray[$row['sys_language_uid']];
						$contentElementsOther[$row['sys_language_uid']] = $row;

					}
					$contentsvalue->setTranslatedRecords($contentElementsOther);
					if($hasFile==0){
						$contentsvalue->setHaveFile(0);
					}else{
						$contentInSubpages = 1;
					}

					$context  = GeneralUtility::makeInstance(Context::class);
					$langId = $context->getPropertyFromAspect('language', 'id');

					if($langId > 0){

						//Change header value for other than default language
						#DebugUtility::debug($contentElementsOther[$langId]['bodytext']);

						if($contentElementsOther[$langId]['header'] != '' || $contentElementsOther[$langId]['bodytext'] != '' || $contentElementsOther[$langId]['rowDescription'] != ''){
							$contentsvalue->setHeader($contentElementsOther[$langId]['header']);
							#    DebugUtility::debug($contentsvalue);

							$contentsvalue->setBodytext($contentElementsOther[$langId]['bodytext']);
							$contentsvalue->setRowDescription($contentElementsOther[$langId]['rowDescription']);
						}

					}
				}
			}
			//if($contentInSubpages == 0){
			$subpages[$key]['hasSubContent'] = 1;
			$contentInSubpages = 1;
			//}

			$subpages[$key]['subpages'] = $this->addlevel($subpages,0,0,$categoryCond,$usergroupCond,$currentLanguageId,$value['uid'],$contentInSubpages,$languageArray,$categoryID);
		}
		return $subpages;

	}


}
