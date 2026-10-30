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

namespace SGalinski\Scriptmerger;

use SGalinski\Scriptmerger\Exceptions\BrokenIntegrityException;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This class contains the basic stuff required by both processors, css and javascript.
 */
abstract class ScriptmergerBase {
	/**
	 * directories for minified, compressed and merged files
	 *
	 * @var array
	 */
	protected $tempDirectories = [];

	/**
	 * @var array
	 */
	protected $configuration;

	/**
	 * @var Logger
	 */
	protected $logger;

	/**
	 * Constructor
	 */
	public function __construct() {
		$pathSite = Environment::getPublicPath() . '/';

		$this->tempDirectories = [
			'main' => $pathSite . 'typo3temp/scriptmerger/',
			'temp' => $pathSite . 'typo3temp/scriptmerger/temp/',
			'minified' => $pathSite . 'typo3temp/scriptmerger/uncompressed/',
			'compressed' => $pathSite . 'typo3temp/scriptmerger/compressed/',
			'merged' => $pathSite . 'typo3temp/scriptmerger/uncompressed/'
		];

		foreach ($this->tempDirectories as $directory) {
			if (!\is_dir($directory)) {
				GeneralUtility::mkdir($directory);
			}
		}
		$this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(self::class);
	}

	/**
	 * Injects the extension configuration
	 *
	 * @param array $configuration
	 * @return void
	 */
	public function injectExtensionConfiguration(array $configuration) {
		$this->configuration = $configuration;
	}

	/**
	 * Controller for the dedicated script type processing
	 *
	 * @param string $content
	 * @return string
	 */
	abstract public function process(string $content): string;

	/**
	 * Get a file from local or external source
	 *
	 * @param string $source The source path
	 * @param bool $returnContent Return the contents of the file
	 * @param string $integrity Optional integrity hash
	 * @return string
	 * @throws BrokenIntegrityException
	 */
	protected function getFile($source, $returnContent = FALSE, $integrity = '') {
		$baseUrl = '';
		$pathSite = Environment::getPublicPath() . '/';
		if ($GLOBALS['TYPO3_REQUEST']) {
			/** @var Site $site */
			$site = $GLOBALS['TYPO3_REQUEST']->getAttribute('site');
			if ($site) {
				$baseUrl = (string) $site->getBase();
			}
		}

		$fileExists = \file_exists($source);
		if ($fileExists) {
			$content = \file_get_contents($source);
			if ($integrity !== '' && !$this->checkIntegrity($integrity, $content)) {
				throw new BrokenIntegrityException("The file \"$source\" has failed the integrity check!");
			}
		} else {
			$sameDomainFileCheck = $pathSite . str_replace($baseUrl, '', $source);
			$fileExists = \file_exists($sameDomainFileCheck);
			if ($fileExists) {
				$content = \file_get_contents($sameDomainFileCheck);
			} else {
				$content = $this->getExternalFile($source, $returnContent, $integrity);
			}
		}

		if (!$returnContent) {
			$content = $source;
		}

		return $content;
	}

	/**
	 * Gets a file from an external resource (e.g. http://) and caches them
	 *
	 * @param string $source Source address
	 * @param boolean $returnContent
	 * @param string $integrity Optional integrity hash
	 * @return string cache file or content (depends on the parameter)
	 * @throws BrokenIntegrityException
	 */
	protected function getExternalFile($source, $returnContent = FALSE, $integrity = '') {
		$filename = \basename($source);
		$hash = \md5($source);
		$cacheFile = $this->tempDirectories['temp'] . $filename . '-' . $hash;
		$externalFileCacheLifetime = (int) $this->configuration['externalFileCacheLifetime'];
		$cacheLifetime = ($externalFileCacheLifetime > 0) ? $externalFileCacheLifetime : 3600;

		// check the age of the cache file (also fails with non-existent file)
		$content = '';
		if ((int) @\filemtime($cacheFile) <= ($GLOBALS['EXEC_TIME'] - $cacheLifetime)) {
			if (strpos($source, '//') === 0) {
				$protocol = \stripos($_SERVER['SERVER_PROTOCOL'], 'https') === TRUE ? 'https:' : 'http:';
				$source = $protocol . $source;
			}

			$content = GeneralUtility::getUrl($source);
			if ($content !== FALSE) {
				if ($integrity === '' || $this->checkIntegrity($integrity, $content)) {
					$this->writeFile($cacheFile, $content);
				} else {
					throw new BrokenIntegrityException("The file \"$source\" has failed the integrity check!");
				}
			} else {
				$cacheFile = '';
			}
		} elseif ($returnContent) {
			$content = $this->getFile($cacheFile, TRUE, $integrity);
		}

		$returnValue = $cacheFile;
		if ($returnContent) {
			$returnValue = $content;
		}

		return $returnValue;
	}

	/**
	 * Check the integrity of the given content, valid hash types are sha256, sha384 and sha512
	 *
	 * @param string $integrityHash The integrity hash to compare the content against
	 * @param string $content The content to check its integrity
	 * @return bool
	 * @see https://developer.mozilla.org/en-US/docs/Web/Security/Subresource_Integrity
	 */
	protected function checkIntegrity($integrityHash, $content) {
		[$hashType, $base64IntegrityHash] = \explode('-', $integrityHash, 2);
		$contentHash = $this->calculateIntegrity($content, $hashType);
		return $contentHash === $base64IntegrityHash;
	}

	/**
	 * Calculate the integrity hash of the given content
	 *
	 * @param string $content The content of a file
	 * @param string $hashType The type of the hashing method
	 * @return string
	 */
	protected function calculateIntegrity($content, $hashType = 'sha512') {
		$contentHash = '';
		if (\in_array($hashType, \hash_algos())) {
			$contentHash = \base64_encode(\hash($hashType, $content, TRUE));
		}
		return $contentHash;
	}

	/**
	 * Writes $content to the file $file
	 *
	 * @param string $file file path to write to
	 * @param string $content Content to write
	 * @return boolean TRUE if the file was successfully opened and written to.
	 */
	protected function writeFile($file, $content) {
		$result = GeneralUtility::writeFile($file, $content);

		// hook here for other file system operations like syncing to other servers, etc.
		$hooks = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['scriptmerger']['writeFilePostHook'] ?? FALSE;
		if (\is_array($hooks)) {
			foreach ($hooks as $classReference) {
				$hookObject = GeneralUtility::makeInstance($classReference);
				$hookObject->writeFilePostHook($file, $content, $this);
			}
		}

		return $result;
	}
}
