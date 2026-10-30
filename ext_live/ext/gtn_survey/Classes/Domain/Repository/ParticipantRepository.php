<?php

namespace GTN\GtnSurvey\Domain\Repository;


class ParticipantRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * @var array
     */
    protected $defaultOrderings = [
        'sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
    ];
	
	 public function getByTime($fromTimestamp = NULL, $toTimestamp = NULL) {
        $query = $this->createQuery();
		$queryAndConds = [];
		if(isset($fromTimestamp) && isset($toTimestamp)) {
			$queryAndConds[] = $query->greaterThanOrEqual('crdate', $fromTimestamp);
			$queryAndConds[] = $query->lessThanOrEqual('crdate', $toTimestamp);
			$query->matching($query->logicalAnd(...array_values($queryAndConds)));
		}		
		
        return $query->execute();
    }

    public function findLatestEntry() {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectSysLanguage(FALSE);
        return $query
            ->setOrderings(array('uid' =>\TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING))

            ->setLimit(1)
            ->execute();
    }
}