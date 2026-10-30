<?php
namespace Hafs\Downloadlst\Domain\Repository;

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
use TYPO3\CMS\Extbase\Utility\DebuggerUtility as Debug;
use TYPO3\CMS\Core\Utility\GeneralUtility;
/**
 * The repository for Extendttcontent
 */
class ExtendttcontentRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * @var array
     */
    protected $defaultOrderings = [
        'sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
    ];

    public function setStoragePageId($storagePid) {
        $querySettings = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings'); 
        $querySettings->setRespectStoragePage(FALSE); 
        $querySettings->setStoragePageIds(array($storagePid)); 
        $this->setDefaultQuerySettings($querySettings);
    }
    
    /*
    * 
    */
    Public function findByUids($uids,$categoryId = array(),$pid=0){

    	$querySettings = $this->createQuery()->getQuerySettings();
        $querySettings->setRespectStoragePage(FALSE);         
        $querySettings->setRespectSysLanguage(FALSE);
        $querySettings->setLanguageUid(0);
        $this->setDefaultQuerySettings($querySettings);

    	

        // if $uids is empty then typo3 will throw an exception
        if (empty($uids)) {            
            return [];
        }
        
	    $query = $this->createQuery();
        
         
	    foreach ($uids as $key => $value) {
            $constraints[] = $query->logicalOr(
             $query->equals('uid', $value)
                
            );
        }

        // if($pid>0){
        //     $constraints[] = $query->logicalAnd(
        //         $query->equals('pid', $pid)
        //     );
        // }

        // if (count($categoryId) > 0) {
        //     $constraints[] = $query->logicalAnd(
        //         $query->in('categories.uid', $categoryId)
        //     );
        // }
        // Debug::var_dump($uids);
        //$categoryId[]=9;
        if (count($categoryId) > 0) {
        	
    	    if($pid > 0){
                $query->matching(
                    $query->logicalAnd(
                        $query->equals('pid', $pid),
                        $query->logicalOr(
                            $query->in('categories.uid', $categoryId),
                            $query->equals('categories',0)
                        ),
                        $query->logicalOr(...array_values($constraints))
                    )
                );
            }else{
                $query->matching(
                    $query->logicalAnd(
                        $query->logicalOr(
                            $query->in('categories.uid', $categoryId),
                            $query->equals('categories',0)
                        ),
                        $query->logicalOr(...array_values($constraints))
                    )
                );   
            }   
        }else{
            if($pid > 0){
                $query->matching(
                    $query->logicalAnd(
                        $query->equals('pid', $pid),
                        $query->equals('categories',0),
                        $query->logicalOr(...array_values($constraints))
                    )
                );

            }else{
                $query->matching(
                    $query->logicalAnd(
                        $query->logicalOr(...array_values($constraints))
                    )
                );                
            }
        }
        // Debug::var_dump($query);
         //$this->debugQuery($query->execute(),true);

        $res = $query->execute();
        //Debug::var_dump($res);
        return $res;

    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $queryResult
     * @param bool $explainOutput
     * @return void
     */
    public function debugQuery(
        \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $queryResult,
        $explainOutput = false
    ) {
        $GLOBALS['TYPO3_DB']->debugOuput = 2;
        if ($explainOutput) {
            $GLOBALS['TYPO3_DB']->explainOutput = true;
        }
        $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
        $queryResult->toArray();
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(
            $GLOBALS['TYPO3_DB']->debug_lastBuiltQuery
        );
        $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = false;
        $GLOBALS['TYPO3_DB']->explainOutput = false;
        $GLOBALS['TYPO3_DB']->debugOuput = false;
    }
    
}
