<?php

declare(strict_types=1);

namespace Jigal\T3adminer\Controller;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

class AdminerController
{
    private const ADMINER_PATH = '/Resources/Public/Adminer/';
    private const ADMINER_SCRIPT = 't3adminer.php';
    // Mapping language keys for Adminer
    private const LANG_KEY_MAP = [
        'cz' => 'cs',       // Czech
        'ms' => 'id',       // Malay (Indonesian)
        'my' => 'id',       // Malay (Indonesian)
        'jp' => 'ja',       // Japanese
        'kr' => 'ko',       // Korean
        'pt_BR' => 'pt-br', // Portuguese (Brazil)
        'br' => 'pt-br',    // Portuguese (Brazil)
        'si' => 'sl',       // Slovenian
        'ua' => 'uk',       // Ukrainian
        'vn' => 'vi',       // Vietnamese
        'hk' => 'zh',       // Chinese
        'ch' => 'zh',       // Chinese
    ];
    private const LANGUAGE_PREFIX = 'LLL:EXT:t3adminer/Resources/Private/Language/locallang.xlf:';

    protected string $content;
    protected array $extensionConfiguration;
    protected ModuleTemplateFactory $moduleTemplateFactory;

    public function __construct(ModuleTemplateFactory $moduleTemplateFactory)
    {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
    }

    /**
     * @return \TYPO3\CMS\Core\Http\HtmlResponse
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function main(ServerRequestInterface $request): HtmlResponse
    {
        $this->extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get('t3adminer');

        $error = $this->checkAccessByIP();
        if ($error !== '') {
            return $this->printContent($request, $error);
        }

        $error = $this->checkIfAdminerIsPresent();
        if ($error !== '') {
            return $this->printContent($request, $error);
        }

        session_cache_limiter('');
        // Need to have cookie visible from parent directory
        session_set_cookie_params(0, '/', '', false);

        // Create signon session
        $session_name = 'tx_t3adminer';
        session_name($session_name);
        session_start();

        // Pass export directory
        $exportDirectory = GeneralUtility::getFileAbsFileName(trim($this->extensionConfiguration['exportDirectory'] ?? ''));
        if (!is_dir($exportDirectory)) {
            $exportDirectory = GeneralUtility::getFileAbsFileName($GLOBALS['TYPO3_CONF_VARS']['BE']['fileadminDir']);
        }
        $_SESSION['exportDirectory'] = $exportDirectory;
        // Detect DBMS
        $_SESSION['ADM_driver'] = 'server';
        $host = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['host'];
        if (isset($GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['driver'])) {
            switch ($GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['driver']) {
                case 'pdo_mysql':
                case 'mysqli':
                    $_SESSION['ADM_driver'] = 'server';
                    if (str_starts_with($host, 'p:')) {
                        $host = substr($host, 2);
                    }
                    break;
                case 'pdo_pgsql':
                    $_SESSION['ADM_driver'] = 'pgsql';
                    break;
                case 'pdo_sqlite':
                    $_SESSION['ADM_driver'] = 'sqlite';
                    break;
                case 'mssql':
                    $_SESSION['ADM_driver'] = 'mssql';
                    break;
                default:
                    $_SESSION['ADM_driver'] = 'server';
            }
        }

        // Store there credentials in the session
        $_SESSION['ADM_user'] = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['user'];
        $_SESSION['pwds'][$_SESSION['ADM_driver']][][$GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['user']] = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['password'];
        $_SESSION['pwds'][$_SESSION['ADM_driver']][$host . ':' . $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['port']][$GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['user']] = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['password'];
        $_SESSION['ADM_password'] = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['password'];
        $_SESSION['ADM_server'] = $host;
        $_SESSION['ADM_port'] = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['port'];
        $_SESSION['ADM_db'] = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['dbname'];

        // Configure some other parameters
        $_SESSION['ADM_extConf'] = $this->extensionConfiguration;
        $_SESSION['ADM_hideOtherDBs'] = $this->extensionConfiguration['hideOtherDBs'] ?? false;

        // Store TCA in the session to have extra information later on
        $_SESSION['ADM_tca'] = $GLOBALS['TCA'];

        // Get signon uri for redirect
        $_SESSION['ADM_SignonURL'] = PathUtility::getAbsoluteWebPath(
            GeneralUtility::getFileAbsFileName('EXT:t3adminer' . self::ADMINER_PATH . self::ADMINER_SCRIPT)
        );

        // Prepend document root if uploadDir does not start with a slash "/"
        $this->extensionConfiguration['uploadDir'] = trim($this->extensionConfiguration['uploadDir'] ?? '');
        if (!str_starts_with($this->extensionConfiguration['uploadDir'], '/')) {
            $_SESSION['ADM_uploadDir'] = GeneralUtility::getIndpEnv('TYPO3_DOCUMENT_ROOT')
                . '/' . $this->extensionConfiguration['uploadDir'];
        } else {
            $_SESSION['ADM_uploadDir'] = $this->extensionConfiguration['uploadDir'];
        }
        $id = session_id();

        // Force to set the cookie
        setcookie($session_name, $id, 0, '/', '');

        // Close that session
        session_write_close();

        // @extensionScannerIgnoreLine
        $languageKey = self::LANG_KEY_MAP[$this->getLanguageService()->lang] ?? $this->getLanguageService()->lang ?? 'en';

        // Redirect to adminer (should use absolute URL here!), setting default database
        $redirectUri = $_SESSION['ADM_SignonURL'] . '?lang=' . $languageKey . '&db='
            . rawurlencode($GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['dbname']) . '&'
            . rawurlencode($_SESSION['ADM_driver']) . '='
            . rawurlencode(
                $host
                . (
                $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['port']
                    ? ':' . $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['port']
                    : ''
                )
            ) . '&username=' . rawurlencode($GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['user']);
        if ($_SESSION['ADM_driver'] !== 'server') {
            $redirectUri .= '&driver=' . rawurlencode($_SESSION['ADM_driver']);
        }

        // Build and set cache-header header
        $headers = [
            'Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'private',
            'Location' => $redirectUri
        ];
        return new HtmlResponse('', 303, $headers);
    }

    /**
     * Prints out the module HTML or returns it in an HtmlResponse object
     *
     * @TODO Replace deprecated code
     *
     * @param string $content Content body as formatted HTML
     * @return \TYPO3\CMS\Core\Http\HtmlResponse
     */
    public function printContent(ServerRequestInterface $request, $content): HtmlResponse
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $moduleTemplate->setTitle($this->getLanguageService()->sL(self::LANGUAGE_PREFIX . 'title'));
        $moduleTemplate->setContent($content);

        return new HtmlResponse($moduleTemplate->renderContent());
    }

    private function checkAccessByIP(): string
    {
        $result = '';
        $devIPmask = trim($GLOBALS['TYPO3_CONF_VARS']['SYS']['devIPmask'] ?? '');
        $remoteAddress = GeneralUtility::getIndpEnv('REMOTE_ADDR');

        // Check for devIpMask restriction
        $useDevIpMask = (bool)($this->extensionConfiguration['applyDevIpMask'] ?? false);
        if ($useDevIpMask && $devIPmask !== '*' && !GeneralUtility::cmpIP($remoteAddress, $devIPmask)) {
            $result = sprintf($this->getLanguageService()->sL(self::LANGUAGE_PREFIX . 'mlang_notindevipmask'), $remoteAddress);
        }

        // Check for specified IP restrictions
        $allowedIps = trim($this->extensionConfiguration['IPaccess'] ?? '');
        if (!empty($allowedIps) && !GeneralUtility::cmpIP($remoteAddress, $allowedIps)) {
            $result = sprintf($this->getLanguageService()->sL(self::LANGUAGE_PREFIX . 'mlang_notinipaccess'), $remoteAddress);
        }

        return $result;
    }

    private function checkIfAdminerIsPresent(): string
    {
        $result = '';
        $lang = $this->getLanguageService();
        $absolutePath = GeneralUtility::getFileAbsFileName('EXT:t3adminer' . self::ADMINER_PATH . self::ADMINER_SCRIPT);

        if (!($absolutePath ?? '') || !@is_file($absolutePath)) {
            // No configuration set
            $result = '<h3>' . $lang->sL(self::LANGUAGE_PREFIX . 'mlang_notinstalled') . '</h3>';
            if (!@is_dir(GeneralUtility::getFileAbsFileName('EXT:t3adminer' . self::ADMINER_PATH))) {
                $result .= '<hr /><strong>'
                    . sprintf($lang->sL(self::LANGUAGE_PREFIX . 'mlang_dirnotfound'), self::ADMINER_PATH)
                    . '</strong><hr />';
            }
        }

        return $result;
    }

        private function getLanguageService(): LanguageService
        {
            return $GLOBALS['LANG'];
        }
}
