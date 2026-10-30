<?php
namespace Gtn\GtnLsthooks\PowermailFinisher;

use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Finisher\AbstractFinisher;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PowermailAnonymFinisher extends AbstractFinisher
{
    public function anonymFinisher(): void
    {
        $formUid = 18;

        $mail = $this->getMail(); // kommt aus AbstractFinisher
        if ($mail && $mail->getForm()->getUid() === $formUid) {
            $connection = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable('tx_powermail_domain_model_mail');

            $connection->update(
                'tx_powermail_domain_model_mail',
                ['feuser' => 0],
                ['uid' => (int)$mail->getUid()]
            );
        }
    }
}