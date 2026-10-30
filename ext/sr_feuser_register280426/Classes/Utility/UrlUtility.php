<?php
namespace SJBR\SrFeuserRegister\Utility;

/*
 *  Copyright notice
 *
 *  (c) 2007-2024 Stanislas Rolland <typo3AAAA(arobas)sjbr.ca>
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
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Generates a typolinks and URL's
 */
class UrlUtility
{
	/**
	 * Content object
	 *
	 * @var ContentObjectRenderer
	 */
	 static protected $cObj = null;

	/**
	 * Generates a pibase-compliant typolink
	 *
	 * @param string $prefixId: prefix id of variables
	 * @param string $tag: string to include within <a>-tags; if empty, only the url is returned
	 * @param string $id: page id (could of the form id,type )
	 * @param array $vars: extension variables to add to the url ($key, $value)
	 * @param array $unsetVars: extension variables (piVars to unset)
	 * @param boolean $usePiVars: if set, input vars and incoming piVars arrays are merge
	 * @param ServerRequestInterface|null $request
	 * @return string generated link or url
	 */
	static public function get($prefixId, $tag, $id, array $vars = [], $unsetVars = [], $usePiVars = true, $request = null)
	{
		self::initializeUrlUtility();
		$urlParameters = $vars;
		if ($usePiVars && $request instanceof ServerRequestInterface) {
			// vars override pivars
			$piVars = $request->getQueryParams()[$prefixId] ?? [];
			ArrayUtility::mergeRecursiveWithOverrule($piVars, $request->getParsedBody()[$prefixId] ?? []);
			if (isset($piVars) && is_array($piVars)) {
				$urlParameters = array_merge($piVars, $vars);
				foreach ($unsetVars as $key) {
					if (isset($urlParameters[$key])) {
						// unsetVars override anything
						unset($urlParameters[$key]);
					}
				}
			}
		}
		$linkConf = [
			'parameter' => $id
		];
        if (!empty($urlParameters)) {
			 $linkConf['additionalParams'] = GeneralUtility::implodeArrayForUrl($prefixId, $urlParameters);
		}
		$url = $tag ? self::$cObj->createLink($tag, $linkConf) : self::$cObj->createUrl($linkConf);
		return $url;
	}

	/**
	 * Returns the URL of a "typolink" create from the input parameter string, url-parameters and target
	 *
	 * @param string Link parameter; eg. "123" for page id, "kasperYYYY@typo3.com" for email address, "http://...." for URL, "fileadmin/blabla.txt" for file.
	 * @param array An array with key/value pairs representing URL parameters to set. Values NOT URL-encoded yet.
	 * @param string Specific target set, if any. (Default is using the current)
	 * @param array Configuration
	 * @return string The URL
	 */
	static public function getTypoLink_URL($param, array $urlParameters = [], $target = '', $conf = [])
	{
		self::initializeUrlUtility();
		$linkConf = $conf;
		$linkConf['parameter'] = $param;
        if (!empty($urlParameters)) {
			 $linkConf['additionalParams'] = ($conf['additionalParams'] ?? '') . GeneralUtility::implodeArrayForUrl('', $urlParameters);
		}
		if ($target) {
		    if (!isset($conf['target'])) {
			    $linkConf['target'] = $target;
		    }
		    if (!isset($conf['extTarget'])) {
			    $linkConf['extTarget'] = $target;
		    }
		}
		return self::$cObj->createUrl($linkConf);
    }

	/**
	 * Returns a linked string made from typoLink parameters.
	 *
	 * This function takes $label as a string, wraps it in a link-tag based on the $params string, which should contain data like that you would normally pass to the popular <LINK>-tag in the TSFE.
	 * Optionally you can supply $urlParameters which is an array with key/value pairs that are rawurlencoded and appended to the resulting url.
	 *
	 * @param string Text string being wrapped by the link.
	 * @param string Link parameter; eg. "123" for page id, "kasperYYYY@typo3.com" for email address, "http://...." for URL, "fileadmin/blabla.txt" for file.
	 * @param array An array with key/value pairs representing URL parameters to set. Values NOT URL-encoded yet.
	 * @param string Specific target set, if any. (Default is using the current)
	 * @param array Configuration
	 * @return string The wrapped $label-text string
	 */
	static public function getTypoLink($label, $param, array $urlParameters = [], $target = '', $conf = [])
    {
        self::initializeUrlUtility();
		$linkConf = $conf;
        $linkConf['parameter'] = $param;
		if ($target) {
		    if (!isset($conf['target'])) {
			    $linkConf['target'] = $target;
		    }
		    if (!isset($conf['extTarget'])) {
			    $linkConf['extTarget'] = $target;
		    }
		}
        if (!empty($urlParameters)) {
			 $linkConf['additionalParams'] = ($conf['additionalParams'] ?? '') . GeneralUtility::implodeArrayForUrl('', $urlParameters);
		}
	 	return self::$cObj->createLink($label, $linkConf);
    }

	/**
	 * Initializes variables
	 *
	 * @return void
	 */
	static protected function initializeUrlUtility()
	{
		if (self::$cObj === null) {
			self::$cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
		}
	}

	/**
	 * Get the site URL
	 *
	 * @param ServerRequestInterface $request
	 * @return string the site URL
	 */
	static public function getSiteUrl(ServerRequestInterface $request)
	{
		$normalizedParams = $request->getAttribute('normalizedParams');
		$siteUrl = $normalizedParams->getSiteUrl();
		if ($GLOBALS['TSFE']->absRefPrefix) {
			if (strpos($GLOBALS['TSFE']->absRefPrefix, 'http://') === 0 || strpos($GLOBALS['TSFE']->absRefPrefix, 'https://') === 0) {
				$siteUrl = $GLOBALS['TSFE']->absRefPrefix;
			} else {
				$siteUrl .= ltrim($GLOBALS['TSFE']->absRefPrefix, '/');
			}
		}
		return $siteUrl;
	}
}