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
 * The repository for GroupTypes
 */
class GroupTypeRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    public function getGroupTypeByQuestion($question)
    {
        $query = $this->createQuery();
        $query->matching($query->contains('questions', $question));
		$query->getQuerySettings()->setRespectSysLanguage(false);

		return $query->execute();
    }
}
