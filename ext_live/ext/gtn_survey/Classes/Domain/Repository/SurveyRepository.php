<?php
namespace GTN\GtnSurvey\Domain\Repository;

/***
 *
 * This file is part of the "Surveys" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019
 *
 ***/

/**
 * The repository for Surveys
 */
class SurveyRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * @param $id
     */
   /* public function getSurveyById($id)
    {
        $query = $this->createQuery();
        $query->statement('SELECT * FROM tx_gtnsurvey_domain_model_survey
            WHERE hidden = 0 AND deleted = 0 AND uid =' . $id . ' ORDER BY sorting ASC');
        return $query->execute()->toArray();
    }*/

    public function getSurveyById($id)
    {
        $query = $this->createQuery();
        $query->matching($query->equals('uid', $id));
        $query->getQuerySettings()->setRespectSysLanguage(false);


        /*$query->setOrderings(
            [
                'questionGroups.sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING
            ]
        );*/
       /* $queryParser = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance
	(\TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser::class);
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($queryParser->convertQueryToDoctrineQueryBuilder($query)
       ->getSQL());*/
        return $query->execute();
    }
}
