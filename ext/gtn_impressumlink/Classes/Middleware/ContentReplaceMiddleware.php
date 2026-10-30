<?php

namespace Gtn\GtnImpressumlink\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Http\StreamFactory;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

class ContentReplaceMiddleware implements MiddlewareInterface
{

        /**
     * @var UriBuilder
     */
    protected $uriBuilder;
    protected $tsSettings;

    /**
     * @param UriBuilder
     */
    public function __construct(
        UriBuilder $uriBuilder,
    ) {
        $this->uriBuilder = $uriBuilder;

        $configurationManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
        $settings = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $this->tsSettings = $settings['plugin.']['tx_gtnimpressumlink.']['settings.'];
        if ($this->tsSettings) {
            $this->tsSettings = GeneralUtility::removeDotsFromTS($this->tsSettings);
        }
    }



    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
       
        $response = $handler->handle($request);

        // Perform the string replacement
        $modifiedContent = (string) ($response->getBody());

        // Perform the string replacement
        if ($this->tsSettings['replacers'] && is_array($this->tsSettings['replacers'])) {
            foreach ($this->tsSettings['replacers'] as $replacer) {
                $ignoredPages = GeneralUtility::trimExplode(',', $replacer['ignoredPages']);
                if (in_array($GLOBALS['TSFE']->id, $ignoredPages)) {
                    continue; // ignore this page
                }
                $replacerKey = trim($replacer['replaceMarker']);

                $url = $this->uriBuilder
                    ->reset()
                    ->setTargetPageUid((int)$replacer['linkToPid'])->build();
                    $modifiedContent = str_replace(
                        $replacerKey,
                    '<a href=' . $url . ' 
                    class=' . strtok($replacer['linkTarget'], " ") . '
                                target=' . strtok($replacer['linkTarget'], " ") . ' 
                                title=' . strtok($replacer['linkTitle'], " ") . '>
                                ' . $replacer['linkString'] . '
                                </a>',
                                $modifiedContent,
                );
            }
        }
 

        $sf = GeneralUtility::makeInstance(StreamFactory::class);
        $stream = $sf->createStream($modifiedContent);
        return $response->withBody($stream);
    }

}
