<?php

declare(strict_types=1);

namespace Gtn\GtnLsthooks\Domain\Repository;

use Doctrine\DBAL\Connection;
use Gtn\GtnLsthooks\Service\CourseRegistrationSummary;
use TYPO3\CMS\Core\Database\ConnectionPool;

final class CourseRegistrationRepository
{
    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly CourseRegistrationSummary $summary
    ) {
    }

    public function findOverview(): array
    {
        $query = $this->connectionPool->getQueryBuilderForTable('tx_powermail_domain_model_mail');
        // Retain TYPO3's default record restrictions (including deleted records).
        $result = $query->select('m.uid', 'f.marker', 'a.value')
            ->from('tx_powermail_domain_model_mail', 'm')
            ->join('m', 'tx_powermail_domain_model_answer', 'a', $query->expr()->eq('a.mail', $query->quoteIdentifier('m.uid')))
            ->join('a', 'tx_powermail_domain_model_field', 'f', $query->expr()->eq('a.field', $query->quoteIdentifier('f.uid')))
            ->where(
                $query->expr()->in('m.form', $query->createNamedParameter([20, 21], Connection::PARAM_INT_ARRAY)),
                $query->expr()->in('f.marker', $query->createNamedParameter([
                    'kursuid', 'kurstitel', 'termin', 'courseid',
                    'selbstfuerkursanmelden', 'vorname_01',
                ], Connection::PARAM_STR_ARRAY))
            )
            ->orderBy('m.uid', 'ASC')
            ->addOrderBy('a.uid', 'ASC')
            ->executeQuery();

        // Stream answer rows; only one mail and the resulting groups stay in memory.
        return $this->summary->summarize($result->iterateAssociative());
    }
}
