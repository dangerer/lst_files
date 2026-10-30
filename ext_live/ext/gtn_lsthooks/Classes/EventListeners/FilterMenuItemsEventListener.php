<?php

namespace Gtn\GtnLsthooks\EventListeners;


use TYPO3\CMS\Frontend\Event\FilterMenuItemsEvent;
use Gtn\GtnLsthooks\Hooks\UtilsForHooks;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class FilterMenuItemsEventListener
{

        /** @var UtilsForHooks UtilsForHooks|null  */
        private $utils = null;

        function __construct() {
            /** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager */
            $this->utils = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(UtilsForHooks::class);
        }

	public function onFilterMenuItemsEvent(FilterMenuItemsEvent $event): void
	{
        $utils = $this->utils;
        $menuItems = $event->getFilteredMenuItems();
        $newMenuItems = [];
        // filter menu by Region categories:
        foreach ($menuItems as $mItem) {
            if ($this->utils::checkPageCategory($mItem['uid'])) {
                $newMenuItems[] = $mItem;
            }
        }
        $event->setFilteredMenuItems($newMenuItems);

//        $pageUid = $event->getCurrentPage()['uid'];
//        $result = $utils::checkPageCategory($pageUid);


        //AbstractMenuContentObject::processFilter
    }
}