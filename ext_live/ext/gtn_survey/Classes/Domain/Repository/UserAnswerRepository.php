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
 * The repository for UserAnswers
 */
class UserAnswerRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    public function getByQuestionId($questionId, $fromTimestamp = NULL, $toTimestamp = NULL) {
        $query = $this->createQuery();
		$queryAndConds = [];
		if(isset($fromTimestamp) && isset($toTimestamp)) {
			$queryAndConds[] = $query->greaterThanOrEqual('crdate', $fromTimestamp);
			$queryAndConds[] = $query->lessThanOrEqual('crdate', $toTimestamp);
		
		}
		$queryAndConds[] = $query->equals('question_id', $questionId);
		
		$query->matching($query->logicalAnd(...array_values($queryAndConds)));
        return $query->execute();
    }

    public function getByQuestionAndSectionId($questionId, $sectionId, $fromTimestamp = NULL, $toTimestamp = NULL) {
        $query = $this->createQuery();
        $queryAndConds = [];
		if(isset($fromTimestamp) && isset($toTimestamp)) {
			$queryAndConds[] = $query->greaterThanOrEqual('crdate', $fromTimestamp);
			$queryAndConds[] = $query->lessThanOrEqual('crdate', $toTimestamp);
		
		}
		$queryAndConds[] =  $query->equals('question_id', $questionId);
		$queryAndConds[] =  $query->equals('section_id', $sectionId);
		
        $query->matching($query->logicalAnd(...array_values($queryAndConds)));
		/*$queryParser = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance
		(\TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser::class);
		$doctrineQueryBuilder = $queryParser->convertQueryToDoctrineQueryBuilder($query);
		$doctrineQueryBuilderSQL = $doctrineQueryBuilder->getSQL();
		$doctrineQueryBuilderParameters = $doctrineQueryBuilder->getParameters();
		\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($doctrineQueryBuilderSQL);
		\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($doctrineQueryBuilderParameters);*/

		return $query->execute();
    }

    public function getDistinctByQuestionId($questionId) {
        $query = $this->createQuery();
        $query->statement('SELECT distinct(value) FROM tx_gtnsurvey_domain_model_useranswer
            WHERE hidden = 0 AND deleted = 0 AND question_id = ' . $questionId);
        return $query->execute()->toArray();
    }
}
