<?php
declare(strict_types = 1);
namespace Nng\Nnhelpers\ViewHelpers\Pagination;

use Closure;
use TYPO3\CMS\Core\Pagination\ArrayPaginator;
use TYPO3\CMS\Core\Pagination\PaginationInterface;
use TYPO3\CMS\Core\Pagination\PaginatorInterface;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Service\ExtensionService;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;


/**
 * Ersatz für den `f:widget.paginate` ViewHelper, der in TYPO3 12 entfernt wurde. 
 * DICKES DANKE an https://www.in2code.de/ für den Blog-Beitrag!
 * 
 * 1. Partial für den Paginator in eigene Extension einbinden
 * ```
 * plugin.tx_myext_plugin {
 *  partialRootPaths {
 *      10 = EXT:nnhelpers/Resources/Private/Partials/
 *  }
 * }
 * ```
 *
 * 2. Im Fluid-Template:
 * ```
 * <nnt3:pagination.paginate objects="{allItemsFromQuery}" as="items" paginator="paginator" itemsPerPage="{settings.numItemsPerPage}">
 *  <!-- Items render -->
 *  <f:for each="{items}" as="item">
 *      ...
 *  </f:for>
 *  <!-- Pagination rendern aus nnhelpers -->
 *  <f:render partial="Pagination" arguments="{paginator:paginator}" />
 * </nnt3>
 * ```
 * @return string
 */
class PaginateViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('objects', 'mixed', 'array or queryresult', true);
        $this->registerArgument('as', 'string', 'new variable name', true);
        $this->registerArgument('paginator', 'string', 'variable name for the paginator', true);
        $this->registerArgument('itemsPerPage', 'int', 'items per page', false, 10);
        $this->registerArgument('name', 'string', 'unique identification - will take "as" as fallback', false, '');
    }

    /**
     * @param array $arguments
     * @param Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(
        array $arguments,
        Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        if ($arguments['objects'] === null) {
            return $renderChildrenClosure();
        }
        $templateVariableContainer = $renderingContext->getVariableProvider();
        
        $paginator = self::getPaginator($arguments, $renderingContext);
        $pagination = self::getPagination($arguments, $renderingContext);
        $templateVariableContainer->add($arguments['as'], $paginator->getPaginatedItems());
;
        if ($paginatorVarName = $arguments['paginator']) {
            $templateVariableContainer->add($paginatorVarName, [
                'pagination' => $pagination,
                'paginator' => $paginator,
                'name' => self::getName($arguments)
            ]);
        }

        $output = $renderChildrenClosure();
        $templateVariableContainer->remove($arguments['as']);
        return $output;
    }

    /**
     * @param array $arguments
     * @param RenderingContextInterface $renderingContext
     * @return PaginationInterface
     */
    protected static function getPagination(
        array $arguments,
        RenderingContextInterface $renderingContext
    ): PaginationInterface {
        $paginator = self::getPaginator($arguments, $renderingContext);
        return GeneralUtility::makeInstance(SimplePagination::class, $paginator);
    }

    /**
     * @param array $arguments
     * @param RenderingContextInterface $renderingContext
     * @return PaginatorInterface
     */
    protected static function getPaginator(
        array $arguments,
        RenderingContextInterface $renderingContext
    ): PaginatorInterface {
        if (is_array($arguments['objects'])) {
            $paginatorClass = ArrayPaginator::class;
        } elseif (is_a($arguments['objects'], QueryResultInterface::class)) {
            $paginatorClass = QueryResultPaginator::class;
        } else {
            throw new Exception('Given object is not supported for pagination', 1634132847);
        }
        return GeneralUtility::makeInstance(
            $paginatorClass,
            $arguments['objects'],
            self::getPageNumber($arguments, $renderingContext),
            (int) $arguments['itemsPerPage']
        );
    }

    /**
     * @param array $arguments
     * @param RenderingContextInterface $renderingContext
     * @return int
     */
    protected static function getPageNumber(array $arguments, RenderingContextInterface $renderingContext): int
    {
        $extensionName = $renderingContext->getRequest()->getControllerExtensionName();
        $pluginName = $renderingContext->getRequest()->getPluginName();
        $extensionService = GeneralUtility::makeInstance(ExtensionService::class);
        $pluginNamespace = $extensionService->getPluginNamespace($extensionName, $pluginName);
        $variables = GeneralUtility::_GP($pluginNamespace);
        if ($variables !== null) {
            if (!empty($variables[self::getName($arguments)]['currentPage'])) {
                return (int)$variables[self::getName($arguments)]['currentPage'];
            }
        }
        return 1;
    }

    /**
     * @param array $arguments
     * @return string
     */
    protected static function getName(array $arguments): string
    {
        return $arguments['name'] ?: $arguments['as'];
    }
}