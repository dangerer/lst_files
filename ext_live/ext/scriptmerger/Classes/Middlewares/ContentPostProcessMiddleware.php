<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) Stefan Galinski <stefan@sgalinski.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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

namespace SGalinski\Scriptmerger\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SGalinski\Scriptmerger\Service\FrontendProcessingService;
use TYPO3\CMS\Core\Http\StreamFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ContentPostProcessMiddleware
 *
 * @package SGalinski\Scriptmerger\Middlewares
 */
class ContentPostProcessMiddleware implements MiddlewareInterface {
	/**
	 * @inheritDoc
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
		$queryParams = $request->getQueryParams();
		$response = $handler->handle($request);
		// we only process via middleware, if the request has INT inclusions. The
		// AfterCacheableContentIsGeneratedEventListener handles the processing of cached pages and adds the result to
		// the TYPO3 page cache. Unfortunately, JS and CSS inclusions are handled as INT inclusions, if a frontend user
		// is logged in for example, and will be inserted after the page cache has already been written. Therefore, we
		// need to make sure to catch all scripts that came in via the INT scripts without the ability to cache the
		// results.
		$tsfe = $request->getAttribute('frontend.controller');
		if (isset($queryParams['disableScriptmerger']) || !$tsfe->isINTincScript()) {
			return $response;
		}

		$content = (string) $response->getBody();
		$processingService = GeneralUtility::makeInstance(FrontendProcessingService::class);
		$content = $processingService->process($content);

		$streamFactory = GeneralUtility::makeInstance(StreamFactory::class);
		$stream = $streamFactory->createStream($content);

		$response = $response->withBody($stream);
		if ($response->hasHeader('Content-Length')) {
			// Update the Content-Length Header because contrary to the belief that it is calculated after this middleware,
			// the cms-frontend has calculated it in $handler->handle() and it is no longer accurate after processing the
			// scripts
			$response = $response->withHeader('Content-Length', (string) $response->getBody()->getSize());
		}

		return $response;
	}
}
