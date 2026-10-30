<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) sgalinski Internet Services (https://www.sgalinski.de)
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace SGalinski\Scriptmerger\EventListeners;

use SGalinski\Scriptmerger\Service\FrontendProcessingService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Event\AfterCacheableContentIsGeneratedEvent;

/**
 * This event listener handles the processing of cached pages. The handling of uncached pages, or pages with elements
 * that can not be cached, is done in the ContentPostProcessMiddleware.
 * This is the replacement to the contentPostProc-all hook, that got removed in TYPO3 12
 */
class AfterCacheableContentIsGeneratedEventListener {
	public function __invoke(AfterCacheableContentIsGeneratedEvent $event) {
		$requestParams = $event->getRequest()->getQueryParams();
		if (isset($requestParams['disableScriptmerger'])) {
			return;
		}

		// We skip the processing if the current request contains INT scripts and has therefore uncached
		// assets that need to be resolved, before scriptmerger run. Unfortunately, the CSS and JS resources are handled
		// as INT inclusions, if, for example, a frontend user has logged in, and therefore we can not cache the
		// scriptmerger result for that request. However, handling INT script processing is done in the
		// ContentPostProcessMiddleware and only INT handling, so the processing isn't executed twice
		if ($event->getController()->isINTincScript()) {
			return;
		}

		$processingService = GeneralUtility::makeInstance(FrontendProcessingService::class);
		$event->getController()->content = $processingService->process($event->getController()->content);
	}
}
