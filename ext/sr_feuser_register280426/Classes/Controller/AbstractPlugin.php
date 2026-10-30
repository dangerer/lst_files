<?php

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace SJBR\SrFeuserRegister\Controller;

use Doctrine\DBAL\Result;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Database\Query\Restriction\FrontendRestrictionContainer;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Localization\Locales;
use TYPO3\CMS\Core\Localization\LocalizationFactory;
use TYPO3\CMS\Core\Page\DefaultJavaScriptAssetTrait;
use TYPO3\CMS\Core\Service\MarkerBasedTemplateService;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Old school base class of frontend plugins.
 *
 * Various legacy frontend plugins extend this "abstract".
 *
 * The class comes with a series of helper methods which assist plugins in creating lists,
 * searching, displaying menus, page-browsing (next/previous/1/2/3) and handling links.
 * Functions are all prefixed "pi_" which is reserved for this class. Those functions
 * can of course be overridden in the extension classes (that is the point...)
 *
 * @internal This class is not maintained anymore and may be removed in future versions.
 * @deprecated since TYPO3 v12.4. Will be removed in TYPO3 v13.0.
 */
class AbstractPlugin
{
    use DefaultJavaScriptAssetTrait;

    protected ?ContentObjectRenderer $cObj = null;

    /**
     * Should be same as classname of the plugin, used for CSS classes, variables
     *
     * @var string
     */
    public $prefixId;

    /**
     * Path to the plugin class script relative to extension directory, eg. 'pi1/class.tx_newfaq_pi1.php'
     *
     * @var string
     */
    public $scriptRelPath;

    /**
     * Extension key.
     *
     * @var string
     */
    public $extKey;

    /**
     * This is the incoming array by name $this->prefixId merged between POST and GET, POST taking precedence.
     * Eg. if the class name is 'tx_myext'
     * then the content of this array will be whatever comes into &tx_myext[...]=...
     *
     * @var array
     */
    public $piVars = [
        'pointer' => '',
        // Used as a pointer for lists
        'mode' => '',
        // List mode
        'sword' => '',
        // Search word
        'sort' => '',
    ];

    /**
     * Local Language content
     *
     * @var array
     */
    public $LOCAL_LANG = [];

    /**
     * Contains those LL keys, which have been set to (empty) in TypoScript.
     * This is necessary, as we cannot distinguish between a nonexisting
     * translation and a label that has been cleared by TS.
     * In both cases ['key'][0]['target'] is "".
     *
     * @var array
     */
    protected $LOCAL_LANG_UNSET = [];

    /**
     * Flag that tells if the locallang file has been fetch (or tried to
     * be fetched) already.
     *
     * @var bool
     */
    public $LOCAL_LANG_loaded = false;

    /**
     * Pointer to the language to use.
     *
     * @var string
     */
    public $LLkey = 'default';

    /**
     * Pointer to alternative fall-back language to use.
     *
     * @var string
     */
    public $altLLkey = '';

    /**
     * You can set this during development to some value that makes it
     * easy for you to spot all labels that ARe delivered by the getLL function.
     *
     * @var string
     */
    public $LLtestPrefix = '';

    /**
     * Save as LLtestPrefix, but additional prefix for the alternative value
     * in getLL() function calls
     *
     * @var string
     */
    public $LLtestPrefixAlt = '';

    /**
     * Should normally be set in the main function with the TypoScript content passed to the method.
     *
     * $conf[LOCAL_LANG][_key_] is reserved for Local Language overrides.
     * $conf[userFunc] reserved for setting up the USER / USER_INT object. See TSref
     *
     * @var array
     */
    public $conf = [];

    /**
     * Property for accessing TypoScriptFrontendController centrally
     *
     * @var TypoScriptFrontendController
     */
    protected $frontendController;

    /**
     * @var MarkerBasedTemplateService
     */
    protected $templateService;

    /**
     * Class Constructor (true constructor)
     * Initializes $this->piVars if $this->prefixId is set to any value
     * Will also set $this->LLkey based on the config.language setting.
     *
     * @param null $_ unused,
     */
    public function __construct($_ = null, TypoScriptFrontendController $frontendController = null)
    {
        $this->frontendController = $frontendController ?: $GLOBALS['TSFE'];
        $this->templateService = GeneralUtility::makeInstance(MarkerBasedTemplateService::class);
        // Setting piVars:
        if ($this->prefixId) {
            $this->piVars = self::getRequestPostOverGetParameterWithPrefix($this->prefixId);
        }
        $this->LLkey = $this->frontendController->getLanguage()->getTypo3Language();

        $locales = GeneralUtility::makeInstance(Locales::class);
        if ($locales->isValidLanguageKey($this->LLkey)) {
            $alternativeLanguageKeys = $locales->getLocaleDependencies($this->LLkey);
            $alternativeLanguageKeys = array_reverse($alternativeLanguageKeys);
            $this->altLLkey = implode(',', $alternativeLanguageKeys);
        }
    }

    /**
     * This setter is called when the plugin is called from UserContentObject (USER)
     * via ContentObjectRenderer->callUserFunction().
     */
    public function setContentObjectRenderer(ContentObjectRenderer $cObj): void
    {
        $this->cObj = $cObj;
    }

    /**
     * Recursively looks for stdWrap and executes it
     *
     * @param array $conf Current section of configuration to work on
     * @param int $level Current level being processed (currently just for tracking; no limit enforced)
     * @return array Current section of configuration after stdWrap applied
     */
    protected function applyStdWrapRecursive(array $conf, $level = 0)
    {
        foreach ($conf as $key => $confNextLevel) {
            if (str_contains($key, '.')) {
                $key = substr($key, 0, -1);

                // descend into all non-stdWrap-subelements first
                foreach ($confNextLevel as $subKey => $subConfNextLevel) {
                    if (is_array($subConfNextLevel) && str_contains($subKey, '.') && $subKey !== 'stdWrap.') {
                        $conf[$key . '.'] = $this->applyStdWrapRecursive($confNextLevel, $level + 1);
                    }
                }

                // now for stdWrap
                foreach ($confNextLevel as $subKey => $subConfNextLevel) {
                    if (is_array($subConfNextLevel) && $subKey === 'stdWrap.') {
                        $conf[$key] = $this->cObj->stdWrap($conf[$key] ?? '', $conf[$key . '.']['stdWrap.'] ?? []);
                        unset($conf[$key . '.']['stdWrap.']);
                        if (empty($conf[$key . '.'])) {
                            unset($conf[$key . '.']);
                        }
                    }
                }
            }
        }
        return $conf;
    }

    /**
     * If internal TypoScript property "_DEFAULT_PI_VARS." is set then it will merge the current $this->piVars array onto these default values.
     */
    public function pi_setPiVarDefaults()
    {
        if (isset($this->conf['_DEFAULT_PI_VARS.']) && is_array($this->conf['_DEFAULT_PI_VARS.'])) {
            $this->conf['_DEFAULT_PI_VARS.'] = $this->applyStdWrapRecursive($this->conf['_DEFAULT_PI_VARS.']);
            $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
            $tmp = $typoScriptService->convertTypoScriptArrayToPlainArray($this->conf['_DEFAULT_PI_VARS.']);
            ArrayUtility::mergeRecursiveWithOverrule($tmp, is_array($this->piVars) ? $this->piVars : []);
            $tmp = $this->removeInternalNodeValue($tmp);
            $this->piVars = $tmp;
        }
    }

    /**
     * Remove the internal array key _typoScriptNodeValue
     */
    protected function removeInternalNodeValue(array $typoscript): array
    {
        foreach ($typoscript as $key => $value) {
            if ($key === '_typoScriptNodeValue') {
                unset($typoscript[$key]);
            }
            if (is_array($value)) {
                $typoscript[$key] = $this->removeInternalNodeValue($value);
            }
        }

        return $typoscript;
    }

    /**
     * Returns the global arrays $_GET and $_POST merged with $_POST taking precedence.
     *
     * @param string $parameter Key (variable name) from GET or POST vars
     * @return array Returns the GET vars merged recursively onto the POST vars.
     */
    private static function getRequestPostOverGetParameterWithPrefix($parameter)
    {
        $postParameter = isset($_POST[$parameter]) && is_array($_POST[$parameter]) ? $_POST[$parameter] : [];
        $getParameter = isset($_GET[$parameter]) && is_array($_GET[$parameter]) ? $_GET[$parameter] : [];
        $mergedParameters = $getParameter;
        ArrayUtility::mergeRecursiveWithOverrule($mergedParameters, $postParameter);
        return $mergedParameters;
    }

    /***************************
     *
     * Localization, locallang functions
     *
     **************************/
    /**
     * Returns the localized label of the LOCAL_LANG key, $key
     * Notice that for debugging purposes prefixes for the output values can be set with the internal vars ->LLtestPrefixAlt and ->LLtestPrefix
     *
     * @param string $key The key from the LOCAL_LANG array for which to return the value.
     * @param string $alternativeLabel Alternative string to return IF no value is found set for the key, neither for the local language nor the default.
     * @return string The value from LOCAL_LANG.
     */
    public function pi_getLL($key, $alternativeLabel = '')
    {
        $word = null;
        if (!empty($this->LOCAL_LANG[$this->LLkey][$key][0]['target'])
            || isset($this->LOCAL_LANG_UNSET[$this->LLkey][$key])
        ) {
            $word = $this->LOCAL_LANG[$this->LLkey][$key][0]['target'];
        } elseif ($this->altLLkey) {
            $alternativeLanguageKeys = GeneralUtility::trimExplode(',', $this->altLLkey, true);
            foreach ($alternativeLanguageKeys as $languageKey) {
                if (!empty($this->LOCAL_LANG[$languageKey][$key][0]['target'])
                    || isset($this->LOCAL_LANG_UNSET[$languageKey][$key])
                ) {
                    // Alternative language translation for key exists
                    $word = $this->LOCAL_LANG[$languageKey][$key][0]['target'];
                    break;
                }
            }
        }
        if ($word === null) {
            if (!empty($this->LOCAL_LANG['default'][$key][0]['target'])
                || isset($this->LOCAL_LANG_UNSET['default'][$key])
            ) {
                // Get default translation (without charset conversion, english)
                $word = $this->LOCAL_LANG['default'][$key][0]['target'];
            } else {
                // Return alternative string or empty
                $word = !empty($this->LLtestPrefixAlt) ? $this->LLtestPrefixAlt . $alternativeLabel : $alternativeLabel;
            }
        }
        return !empty($this->LLtestPrefix) ? $this->LLtestPrefix . $word : $word;
    }

    /**
     * Loads local-language values from the file passed as a parameter or
     * by looking for a "locallang" file in the
     * plugin class directory ($this->scriptRelPath).
     * Also locallang values set in the TypoScript property "_LOCAL_LANG" are
     * merged onto the values found in the "locallang" file.
     * Supported file extensions xlf
     *
     * @param string $languageFilePath path to the plugin language file in format EXT:....
     */
    public function pi_loadLL($languageFilePath = '')
    {
        if ($this->LOCAL_LANG_loaded) {
            return;
        }

        if ($languageFilePath === '' && $this->scriptRelPath) {
            $languageFilePath = 'EXT:' . $this->extKey . '/' . PathUtility::dirname($this->scriptRelPath) . '/locallang.xlf';
        }
        if ($languageFilePath !== '') {
            $languageFactory = GeneralUtility::makeInstance(LocalizationFactory::class);
            $this->LOCAL_LANG = $languageFactory->getParsedData($languageFilePath, $this->LLkey);
            $alternativeLanguageKeys = GeneralUtility::trimExplode(',', $this->altLLkey, true);
            foreach ($alternativeLanguageKeys as $languageKey) {
                $tempLL = $languageFactory->getParsedData($languageFilePath, $languageKey);
                if ($this->LLkey !== 'default' && isset($tempLL[$languageKey])) {
                    $this->LOCAL_LANG[$languageKey] = $tempLL[$languageKey];
                }
            }
            // Overlaying labels from TypoScript (including fictitious language keys for non-system languages!):
            if (isset($this->conf['_LOCAL_LANG.'])) {
                // Clear the "unset memory"
                $this->LOCAL_LANG_UNSET = [];
                foreach ($this->conf['_LOCAL_LANG.'] as $languageKey => $languageArray) {
                    // Remove the dot after the language key
                    $languageKey = substr($languageKey, 0, -1);
                    // Don't process label if the language is not loaded
                    if (is_array($languageArray) && isset($this->LOCAL_LANG[$languageKey])) {
                        foreach ($languageArray as $labelKey => $labelValue) {
                            if (!is_array($labelValue)) {
                                $this->LOCAL_LANG[$languageKey][$labelKey][0]['target'] = $labelValue;
                                if ($labelValue === '') {
                                    $this->LOCAL_LANG_UNSET[$languageKey][$labelKey] = '';
                                }
                            }
                        }
                    }
                }
            }
        }
        $this->LOCAL_LANG_loaded = true;
    }

    /*******************************
     *
     * FlexForms related functions
     *
     *******************************/
    /**
     * Converts $this->cObj->data['pi_flexform'] from XML string to flexForm array.
     *
     * @param string $field Field name to convert
     */
    public function pi_initPIflexForm($field = 'pi_flexform')
    {
        // Converting flexform data into array
        $fieldData = $this->cObj->data[$field] ?? null;
        if (!is_array($fieldData) && $fieldData) {
            $this->cObj->data[$field] = GeneralUtility::xml2array((string)$fieldData);
            if (!is_array($this->cObj->data[$field])) {
                $this->cObj->data[$field] = [];
            }
        }
    }

    /**
     * Return value from somewhere inside a FlexForm structure
     *
     * @param array $T3FlexForm_array FlexForm data
     * @param string $fieldName Field name to extract. Can be given like "test/el/2/test/el/field_templateObject" where each part will dig a level deeper in the FlexForm data.
     * @param string $sheet Sheet pointer, eg. "sDEF
     * @param string $lang Language pointer, eg. "lDEF
     * @param string $value Value pointer, eg. "vDEF
     * @return string|null The content.
     */
    public function pi_getFFvalue($T3FlexForm_array, $fieldName, $sheet = 'sDEF', $lang = 'lDEF', $value = 'vDEF')
    {
        $sheetArray = $T3FlexForm_array['data'][$sheet][$lang] ?? '';
        if (is_array($sheetArray)) {
            return $this->pi_getFFvalueFromSheetArray($sheetArray, explode('/', $fieldName), $value);
        }
        return null;
    }

    /**
     * Returns part of $sheetArray pointed to by the keys in $fieldNameArray
     *
     * @param array $sheetArray Multidimensional array, typically FlexForm contents
     * @param array $fieldNameArr Array where each value points to a key in the FlexForms content - the input array will have the value returned pointed to by these keys. All integer keys will not take their integer counterparts, but rather traverse the current position in the array and return element number X (whether this is right behavior is not settled yet...)
     * @param string $value Value for outermost key, typ. "vDEF" depending on language.
     * @return mixed The value, typ. string.
     * @internal
     * @see pi_getFFvalue()
     */
    public function pi_getFFvalueFromSheetArray($sheetArray, $fieldNameArr, $value)
    {
        $tempArr = $sheetArray;
        foreach ($fieldNameArr as $k => $v) {
            if (MathUtility::canBeInterpretedAsInteger($v)) {
                if (is_array($tempArr)) {
                    $c = 0;
                    foreach ($tempArr as $values) {
                        if ($c == $v) {
                            $tempArr = $values;
                            break;
                        }
                        $c++;
                    }
                }
            } elseif (isset($tempArr[$v])) {
                $tempArr = $tempArr[$v];
            }
        }
        return $tempArr[$value] ?? '';
    }
}