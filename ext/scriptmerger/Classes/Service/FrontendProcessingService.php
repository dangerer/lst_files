<?php

/*
 * Copyright notice
 *
 * (c) sgalinski Internet Services (https://www.sgalinski.de)
 *
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

namespace SGalinski\Scriptmerger\Service;

use SGalinski\Scriptmerger\ScriptmergerConditionalCommentPreserver;
use SGalinski\Scriptmerger\ScriptmergerCss;
use SGalinski\Scriptmerger\ScriptmergerJavascript;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FrontendProcessingService
 */
class FrontendProcessingService implements SingletonInterface {
	/**
	 * holds the extension configuration
	 *
	 * @var array
	 */
	protected $extensionConfiguration = [];

	/**
	 * FrontendProcessingService constructor.
	 */
	public function __construct() {
		$this->prepareExtensionConfiguration();
	}

	/**
	 * This method fetches and prepares the extension configuration.
	 *
	 * @return void
	 */
	protected function prepareExtensionConfiguration(): void {
		$this->extensionConfiguration = [
			'css.' => [
				'enable' => 0,
				'addContentInDocument' => 0,
				'doNotRemoveInDoc' => 0,
				'mergedFilePosition' => '',
				'integrityCalculation' => 0,
				'minify.' => [
					'enable' => 0,
					'ignore' => '',
				],
				'compress.' => [
					'enable' => 0,
					'ignore' => '',
				],
				'merge.' => [
					'enable' => 0,
					'ignore' => '',
				],
				'uniqueCharset.' => [
					'enable' => 0,
					'value' => '@charset "UTF-8";',
				],
				'postUrlProcessing.' => [
					'pattern' => '',
					'replacement' => '',
				],
			],
			'javascript.' => [
				'enable' => 0,
				'parseBody' => 0,
				'doNotRemoveInDocInBody' => 0,
				'doNotRemoveInDocInHead' => 0,
				'mergedHeadFilePosition' => '</head>',
				'mergedBodyFilePosition' => '</body>',
				'addContentInDocument' => 0,
				'asyncLoading' => 0,
				'deferLoadingInHead' => 0,
				'integrityCalculation' => 0,
				'scriptType' => 0,
				'minify.' => [
					'enable' => 0,
					'useJSMinPlus' => 0,
					'useJShrink' => 0,
					'ignore' => '',
				],
				'compress.' => [
					'enable' => 0,
					'ignore' => '',
				],
				'merge.' => [
					'enable' => 0,
					'ignore' => '',
				],
			],
			'urlRegularExpressions.' => [],
			'externalFileCacheLifetime' => '3600',
		];

		if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['scriptmerger'])) {
			$this->extensionConfiguration = array_merge(
				$this->extensionConfiguration,
				$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['scriptmerger']
			);
		}

		$tsSetup = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_scriptmerger.'] ?? [];
		foreach ($tsSetup as $key => $value) {
			if (is_array($value)) {
				$this->extensionConfiguration[$key] = array_merge($this->extensionConfiguration[$key], $value);
			} else {
				$this->extensionConfiguration[$key] = $value;
			}
		}

		// no compression allowed if content should be added inside the document
		if ($this->extensionConfiguration['css.']['addContentInDocument'] === '1') {
			$this->extensionConfiguration['css.']['compress.']['enable'] = '0';
		}

		if ($this->extensionConfiguration['javascript.']['addContentInDocument'] === '1') {
			$this->extensionConfiguration['javascript.']['compress.']['enable'] = '0';
		}

		// prepare ignore expressions
		if ($this->extensionConfiguration['css.']['ignore'] !== '') {
			$this->extensionConfiguration['css.']['ignore'] = '/.*(' .
				str_replace(',', '|', $this->extensionConfiguration['css.']['ignore']) . ').*/isU';
		}

		if ($this->extensionConfiguration['css.']['minify.']['ignore'] !== '') {
			$this->extensionConfiguration['css.']['minify.']['ignore'] = '/.*(' .
				str_replace(',', '|', $this->extensionConfiguration['css.']['minify.']['ignore']) .
				').*/isU';
		}

		if ($this->extensionConfiguration['css.']['compress.']['ignore'] !== '') {
			$this->extensionConfiguration['css.']['compress.']['ignore'] = '/.*(' .
				str_replace(',', '|', $this->extensionConfiguration['css.']['compress.']['ignore']) .
				').*/isU';
		}

		if ($this->extensionConfiguration['css.']['merge.']['ignore'] !== '') {
			$this->extensionConfiguration['css.']['merge.']['ignore'] = '/.*(' .
				str_replace(',', '|', $this->extensionConfiguration['css.']['merge.']['ignore']) .
				').*/isU';
		}

		if ($this->extensionConfiguration['javascript.']['ignore'] !== '') {
			$this->extensionConfiguration['javascript.']['ignore'] = '/.*(' .
				str_replace(',', '|', $this->extensionConfiguration['javascript.']['ignore']) . ').*/isU';
		}

		if ($this->extensionConfiguration['javascript.']['minify.']['ignore'] !== '') {
			$this->extensionConfiguration['javascript.']['minify.']['ignore'] = '/.*(' .
				str_replace(',', '|', $this->extensionConfiguration['javascript.']['minify.']['ignore']) .
				').*/isU';
		}

		if ($this->extensionConfiguration['javascript.']['compress.']['ignore'] !== '') {
			$this->extensionConfiguration['javascript.']['compress.']['ignore'] = '/.*(' .
				str_replace(',', '|', $this->extensionConfiguration['javascript.']['compress.']['ignore']) .
				').*/isU';
		}

		if ($this->extensionConfiguration['javascript.']['merge.']['ignore'] !== '') {
			$this->extensionConfiguration['javascript.']['merge.']['ignore'] = '/.*(' .
				str_replace(',', '|', $this->extensionConfiguration['javascript.']['merge.']['ignore']) .
				').*/isU';
		}
	}

	/**
	 * @param string $content
	 * @return string
	 */
	public function process(string $content): string {
		$javascriptEnabled = $this->extensionConfiguration['javascript.']['enable'] === '1';
		$cssEnabled = $this->extensionConfiguration['css.']['enable'] === '1';
		if ($cssEnabled || $javascriptEnabled) {
			$conditionalCommentPreserver = GeneralUtility::makeInstance(
				ScriptmergerConditionalCommentPreserver::class
			);
			$content = $conditionalCommentPreserver->read($content);
			if ($cssEnabled) {
				$cssProcessor = GeneralUtility::makeInstance(ScriptmergerCss::class);
				$cssProcessor->injectExtensionConfiguration($this->extensionConfiguration);
				$content = $cssProcessor->process($content);
			}

			if ($javascriptEnabled) {
				$javascriptProcessor = GeneralUtility::makeInstance(ScriptmergerJavascript::class);
				$javascriptProcessor->injectExtensionConfiguration($this->extensionConfiguration);
				$content = $javascriptProcessor->process($content);
			}

			$content = $conditionalCommentPreserver->writeBack($content);
		}

		if (is_array($this->extensionConfiguration['urlRegularExpressions.']) &&
			!empty($this->extensionConfiguration['urlRegularExpressions.'])
		) {
			$content = $this->executeUserDefinedRegularExpressionsOnContent(
				$this->extensionConfiguration['urlRegularExpressions.'],
				$content
			);
		}

		return $content;
	}

	/**
	 * Executes user defined regular expressions on the href/src urls for e.g. use a cookie-less asset domain.
	 *
	 * @param array $expressions
	 * @param string $content
	 * @return string
	 */
	protected function executeUserDefinedRegularExpressionsOnContent(array $expressions, string $content): string {
		foreach ($expressions as $index => $expression) {
			if (!isset($expressions[$index . '.']['replacement']) || strpos($index, '.') !== FALSE) {
				continue;
			}

			$replacement = trim($expressions[$index . '.']['replacement']);
			if ($replacement === '') {
				continue;
			}

			if ($expressions[$index . '.']['useWholeContent'] === '1') {
				// split head from body, to be able to run the preg_replace on the body only
				if ($expressions[$index . '.']['ignoreHeadContent'] === '1') {
					// the body tag might have a class, so we look for the tag without the closing >
					$headPos = strpos($content, '<body');
					$head = substr($content, 0, $headPos);
					$body = substr($content, $headPos);

					$processedBody = preg_replace($expression, $replacement, $body);
					if ($processedBody !== NULL) {
						$content = $head . $processedBody;
					}
				} else {
					// This function will return NULL on an error. So in some cases, the whole content can be just deleted.
					$processedContent = preg_replace($expression, $replacement, $content);
					if ($processedContent !== NULL) {
						$content = (string) $processedContent;
					}
				}
			} else {
				$userExpression = trim(str_replace('/', '\/', $expression));
				$expression = '/<(?:img|link|style|script|meta|input)' .
					'(?=[^>]+?(?:content|href|src)="(' . $userExpression . ')")[^>]*?>/iU';
				preg_match_all($expression, $content, $matches);
				if (is_array($matches[1])) {
					foreach ($matches[1] as $match) {
						if (trim($match) === '') {
							continue;
						}

						$changedUrl = preg_replace('/' . $userExpression . '/is', $replacement, $match);
						$content = str_replace($match, $changedUrl, $content);
					}
				}
			}
		}

		return $content;
	}
}
