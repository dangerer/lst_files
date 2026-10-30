<?php

declare(strict_types=1);

namespace Gtn\GtnLsthooks\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\HtmlResponse;

final class RegistrationHoneypotMiddleware implements MiddlewareInterface
{
	public function process(
		ServerRequestInterface $request,
		RequestHandlerInterface $handler
	): ResponseInterface {
		if (strtoupper($request->getMethod()) !== 'POST') {
			return $handler->handle($request);
		}

		$body = $request->getParsedBody();

		if (!is_array($body)) {
			return $handler->handle($request);
		}

		$pluginData = $body['tx_srfeuserregister_pi1'] ?? null;
		
		if (!is_array($pluginData)) {
			return $handler->handle($request);
		}
		
		$honeypotValue = trim(
			(string)($pluginData['company_website'] ?? '')
		);
		
		if ($honeypotValue !== '') {
			return new HtmlResponse(
				'<p>Vielen Dank. Ihre Anfrage wurde übermittelt.</p>',
				200
			);
		}

		return $handler->handle($request);
	}
}