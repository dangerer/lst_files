<?php
namespace Gtn\GtnLsthooks\Form\Element;

use Gtn\GtnLsthooks\Utils\Utils;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RanActionButtons extends AbstractFormElement
{

    private $articleFields = array('anzahl', 'type', 'artikelnummer', 'deliverynumber', 'seriennummer', 'returnreason', 'beschreibung', 'auftragsnummer');
//    private $ranFormPid = 12435; // TEST page
    private $ranFormPid = 32;
    private $ranFormUid = 3;

    public function render()
    {
        $utils = GeneralUtility::makeInstance(Utils::class);
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $resultArray = $this->initializeResultArray();

        // Get TCA config parameters
        $tcaParameters = $this->data['parameterArray']['fieldConf']['config']['parameters'] ?? [];

        // Get record data
        $ranRecord = $this->data['databaseRow'];
        // !!! "Select" fields are not suitable because uses TCA type converters!!!
        // So, get them manually:
        $uid = $ranRecord['uid'];
        $directDBRecord = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('international_ran_requests')
            ->select(['returnreason'], 'international_ran_requests', ['uid' => $uid])->fetch();
        // ...returnreason
        $ranRecord['returnreason'] = $directDBRecord['returnreason'];

        if ($ranRecord['status']) {
            $html = '';
            switch ($ranRecord['status']) {
                case 'decline':
                    $iconHtml = $iconFactory->getIcon('ran-international-request-decline', Icon::SIZE_SMALL);
                    break;
                case 'approve':
                default:
                    $iconHtml = $iconFactory->getIcon('ran-international-request-approve', Icon::SIZE_SMALL);
            }
            $date = date('d.m.Y H.i.s', $ranRecord['submit_tstamp']);
            $html .= '<br><br>Der Status:<br>'.$iconHtml.'&nbsp;&nbsp;'.$ranRecord['status'].'<br>'.$date.($ranRecord['decline_reason'] ? "<br><br>Grund für die Ablehnung: <br>".$ranRecord['decline_reason'] : '');

        } else {
            $html = '';
            $html .= '<br><span>Sie können diesen Datensatz annehmen oder ablehnen:</span><br><br>';
            // approve:
            $title = 'Annehmen';
            $iconHtml = $iconFactory->getIcon('ran-international-request-approve', Icon::SIZE_SMALL);
            $link = $utils->getRanApproveDeclineUrl($ranRecord, $this->articleFields, $this->ranFormUid, $this->ranFormPid, 'approve');
            $html .= '<a title="' . $title . '" href="' . $link . '" target="_blank" class="btn btn-default">' . $iconHtml. '&nbsp;&nbsp;&nbsp;' . $title . '</a><br><br>';
            // decline:
            $title = 'Ablehnen';
            $iconHtml = $iconFactory->getIcon('ran-international-request-decline', Icon::SIZE_SMALL);
            $link = $utils->getRanApproveDeclineUrl($ranRecord, $this->articleFields, $this->ranFormUid, $this->ranFormPid, 'decline');
            $html .= '<a title="' . $title . '" href="' . $link . '" target="_blank" class="btn btn-default">' . $iconHtml. '&nbsp;&nbsp;&nbsp;' . $title . '</a>';

        }

        // Create custom buttons with different labels
        $resultArray['html'] = $html;

        return $resultArray;
    }
}
