<?php

namespace Gtn\GtnLsthooks\EventListeners;

use Gtn\GtnLsthooks\Hooks\RecordListHook;
use TYPO3\CMS\Backend\RecordList\Event\ModifyRecordListRecordActionsEvent;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ModifyRecordListRecordActionsEventListener
{

    protected IconFactory $iconFactory;
    protected UriBuilder $uriBuilder;
    protected RecordListHook $recordListHook;

    /**
     * @param IconFactory $iconFactory
     * @param UriBuilder $uriBuilder
     */
    public function __construct()
    {
        $this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $this->uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $this->recordListHook = GeneralUtility::makeInstance(RecordListHook::class);
    }

    public function modifyRecordActions(ModifyRecordListRecordActionsEvent $event): void
    {
        $table = $event->getTable();
        // only for "international_ran_requests"
        if ($table === 'international_ran_requests') {
            // clean ALL actions
            $event->setActions([]);
            $row = $event->getRecord();
//            \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($event, 'ModifyRecordListRecordActionsEventListener.php:37', 4); exit; // !!!!!!!!!! delete it
            $recordId = $row['uid'];
            // $row is not full, so get the data right from DB:
            $recordData = BackendUtility::getRecord($table, $recordId);

            if ($recordData['status']) {
                $html = $this->recordListHook->getStatusIcon($recordData);
                $event->setAction(
                    $html,
                    'ranstatus',
                    'primary',
                    'delete'
                );
            } else {
                $html = $this->recordListHook->getApproveIcon($recordData);
                $event->setAction(
                    $html,
                    'ranapprove',
                    'primary',
                    'delete'
                );
                $html = $this->recordListHook->getDeclineIcon($recordData);
                $event->setAction(
                    $html,
                    'randecline',
                    'primary',
                    'delete'
                );
            }
        }         
    }

    protected function getHtml(string $table, int $recordId): string
    {
        $lang = $this->getLanguageService();
        $url = (string)$this->uriBuilder->buildUriFromRoute('loginlink_token', [
            'table' => $table,
            'id' => $recordId,
        ]);
        $icon = $this->iconFactory->getIcon('txloginlink-loginlink', Icon::SIZE_SMALL);
        $title = $lang->sL('LLL:EXT:login_link/Resources/Private/Language/locallang.xlf:trigger.title');
        $html = '<button class="btn btn-default t3js-modal-trigger"
        data-title="' . htmlspecialchars($title) . '"
        title="' . htmlspecialchars($title) . '"
        data-bs-content=""
        data-url="' . htmlspecialchars($url) . '"
        >
            ' . $icon->render() . '
    </button>';
        return $html;
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
    
}