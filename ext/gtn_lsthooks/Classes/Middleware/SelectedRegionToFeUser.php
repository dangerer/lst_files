<?php
namespace Gtn\GtnLsthooks\Middleware;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;


class SelectedRegionToFeUser implements MiddlewareInterface
{

    /** @var ResponseFactory */
//    private $responseFactory;

    /** @var RequestFactory */
//    private $requestFactory;

    /** @var ClientInterface */
//    private $client;

/*    public function __construct(
        ResponseFactoryInterface $responseFactory,
        RequestFactoryInterface $requestFactory,
        ClientInterface $client
    ) {
        $this->responseFactory = $responseFactory;
        $this->requestFactory = $requestFactory;
        $this->client = $client;
    }*/

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request);
        // disabled
        if (@$GLOBALS["TSFE"]->fe_user->user) {
            if (intval($_COOKIE['selectedRegion']) != $GLOBALS["TSFE"]->fe_user->user['lst_region']) { // user changes own region
                // change fe_user data
                $selectedRegion = $_COOKIE['selectedRegion'];
                $insertData = array('lst_region' => $selectedRegion);
                $GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', ' uid = '.intval($GLOBALS["TSFE"]->fe_user->user['uid']), $insertData);
                $GLOBALS["TSFE"]->fe_user->user["lst_region"] = $selectedRegion;
            }
        }

        return $handler->handle($request);
    }

}