<?php
namespace Gtn\GtnSso\Middleware;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\LinkHandling\LinkService;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Redirects\Service\RedirectCacheService;
use TYPO3\CMS\Redirects\Service\RedirectService;

class GtnSsoRedirectMiddleware implements MiddlewareInterface
{

    /**
     * @var RedirectService
     */
    protected $redirectService;

    public function __construct(RedirectService $redirectService = null)
    {
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        $this->redirectService = $redirectService ?? GeneralUtility::makeInstance(
                RedirectService::class,
                GeneralUtility::makeInstance(RedirectCacheService::class),
                GeneralUtility::makeInstance(LinkService::class),
                $siteFinder
            );
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $cookieParams = $request->getCookieParams();
        if (((isset($queryParams['gtnSsoReferer']) && $queryParams['gtnSsoReferer'])
                || (isset($cookieParams['gtnSsoReferer']) && $cookieParams['gtnSsoReferer'])
            )
//            && ($request->getAttributes()['frontend.user']->user) // TODO: needed - only if autorized?
        ) {
            // TODO: add some checking?
            $redirectUri = $queryParams['gtnSsoReferer'] ?? $cookieParams['gtnSsoReferer'];
            setcookie('gtnSsoReferer', null); // NECESSARY clean cookie
            return new RedirectResponse($redirectUri);

        }
        setcookie('gtnSsoReferer', null); // for clean redundant value
        return $handler->handle($request);
    }


}