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
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * This class contains the parsing and replacing functionality for css files
 */
class ScriptmergerCss extends ScriptmergerBase {
	/**
	 * holds the javascript code
	 *
	 * Structure:
	 * - $relation (rel attribute)
	 *   - $media (media attribute)
	 *     - $file
	 *       |-content => string
	 *       |-basename => string (base name of $file without file prefix)
	 *       |-minify-ignore => bool
	 *       |-merge-ignore => bool
	 *
	 * @var array
	 */
	protected $css = [];

	/**
	 * @var string
	 */
	protected $content = '';

	/**
	 * Injects the extension configuration
	 *
	 * @param array $configuration
	 * @return void
	 */
	public function injectExtensionConfiguration(array $configuration) {
		parent::injectExtensionConfiguration($configuration);

		if (!class_exists('Minify_ImportProcessor', FALSE)) {
			require_once(ExtensionManagementUtility::extPath('scriptmerger') . 'Resources/Minify/ImportProcessor.php');
		}
		\Minify_ImportProcessor::$extensionConfiguration = $this->configuration;
	}

	/**
	 * Controller for the css parsing and replacement
	 *
	 * @param string $content
	 * @return string
	 */
	public function process(string $content): string {
		$this->content = $content;
		// fetch all remaining css contents
		$this->getFiles();

		// minify, compress and merging
		foreach ($this->css as $relation => $cssByRelation) {
			foreach ($cssByRelation as $media => $cssByMedia) {
				$mergedContent = '';
				$positionOfMergedFile = NULL;
				foreach ($cssByMedia as $index => $cssProperties) {
					$newFile = '';

					// file should be minified
					if ($this->configuration['css.']['minify.']['enable'] === '1' &&
						!$cssProperties['minify-ignore']
					) {
						$newFile = $this->minifyFile($cssProperties);
					}

					// file should be merged
					if ($this->configuration['css.']['merge.']['enable'] === '1' &&
						!$cssProperties['merge-ignore']
					) {
						if ($positionOfMergedFile === NULL) {
							$positionOfMergedFile = $cssProperties['position-key'];
						}

						$mergedContent .= $cssProperties['content'] . LF;
						unset($this->css[$relation][$media][$index]);
						continue;
					}

					// file should be compressed instead?
					if ($this->configuration['css.']['compress.']['enable'] === '1' &&
						function_exists('gzcompress') && !$cssProperties['compress-ignore']
					) {
						$newFile = $this->compressFile($cssProperties);
					}

					// minification or compression was used
					if ($newFile !== '') {
						$this->css[$relation][$media][$index]['file'] = $newFile;
						$this->css[$relation][$media][$index]['content'] =
							$cssProperties['content'];
						$this->css[$relation][$media][$index]['basename'] =
							$cssProperties['basename'];
						$this->css[$relation][$media][$index]['asAttribute'] =
							$cssProperties['asAttribute'];
					}
				}

				// save merged content inside a new file
				if ($this->configuration['css.']['merge.']['enable'] === '1' && $mergedContent !== '') {
					if ($this->configuration['css.']['uniqueCharset.']['enable'] === '1') {
						$mergedContent = $this->uniqueCharset($mergedContent);
					}

					// create a property array
					$properties = [
						'content' => $mergedContent,
						'basename' => 'head-' . md5($mergedContent) . '.merged'
					];

					// write merged file in any case
					$newFile = $this->tempDirectories['merged'] . $properties['basename'] . '.css';
					if (!file_exists($newFile)) {
						$this->writeFile($newFile, $properties['content']);
					}

					// file should be compressed
					if ($this->configuration['css.']['compress.']['enable'] === '1' &&
						function_exists('gzcompress')
					) {
						$newFile = $this->compressFile($properties);
					}

					$integrity = '';
					if ($this->configuration['css.']['integrityCalculation'] !== '0') {
						$integrityType = 'sha512';
						$integrity = $integrityType . '-' . $this->calculateIntegrity($properties['content']);
					}

					// add new entry
					$this->css[$relation][$media][] = [
						'file' => $newFile,
						'content' => $properties['content'],
						'basename' => $properties['basename'],
						'asAttribute' => ($relation === 'preload' ? 'style' : ''),
						'position-key' => $positionOfMergedFile,
						'integrity' => $integrity
					];
				}
			}
		}

		// write the conditional comments and possibly merged css files back to the document
		$this->writeToDocument();
		return $this->content;
	}

	/**
	 * Some browser fail on parsing merged CSS files if multiple charset definitions are found.
	 * Therefor we replace all charset definitions with an empty string and add a single charset
	 * definition to the beginning of the content. At least Webkit engines fail badly.
	 *
	 * @param string $content
	 * @return string
	 */
	protected function uniqueCharset($content) {
		if (!empty($this->configuration['css.']['uniqueCharset.']['value'])) {
			$content = preg_replace('/@charset[^;]+;/', '', $content);
			$content = $this->configuration['css.']['uniqueCharset.']['value'] . $content;
		}
		return $content;
	}

	/**
	 * This method parses the output content and saves any found css files or inline code
	 * into the "css" class property. The output content is cleaned up of the found results.
	 *
	 * @return void
	 */
	protected function getFiles() {
		// filter pattern for the inDoc styles (fetches the content)
		$filterInDocumentPattern = '/' .
			'<style.*?>' . // This expression removes the opening style tag
			'(?:.*?\/\*<!\[CDATA\[\*\/)?' . // and the optionally prefixed CDATA string.
			'\s*(.*?)' . // We save the pure css content,
			'(?:\s*\/\*\]\]>\*\/)?' . // remove the possible closing CDATA string
			'\s*<\/style>' . // and closing style tag
			'/is';

		// parse all available css code inside link and style tags
		$cssTags = [];
		$pattern = '/' .
			'<(link|sty)' . // Parse any link and style tags.
			'(?=.+?(?:media="(.*?)"|>))' . // Fetch the media attribute
			'(?=.+?(?:href="(.*?)"|>))' . // and the href attribute
			'(?=.+?(?:rel="(.*?)"|>))' . // and the rel attribute
			'(?=.+?(?:title="(.*?)"|>))' . // and the title attribute of the tag.
			'(?=.+?(?:data-ignore=["\'](.*?)["\']|>))' . // and the data-ignore attribute of the tag.
			'(?=.+?(?:integrity="(.*?)"|>))' . // and the integrity attribute
			'(?=.+?(?:as="(.*?)"|>))' . // and the as attribute of the tag.
			'(?:[^>]+?\.css[^>]+?\/?>' . // Continue parsing from \1 to the closing tag.
			'|le[^>]*?>[^>]+?<\/style>)\s*' .
			'/is';

		preg_match_all($pattern, $this->content, $cssTags);
		$amountOfResults = is_countable($cssTags[0]) ? count($cssTags[0]) : 0;
		if (!$amountOfResults) {
			return;
		}

		$function = function () {
			static $i = 0;
			return '###MERGER' . $i++ . 'MERGER###';
		};
		$this->content = preg_replace_callback(
			$pattern,
			$function,
			$this->content,
			$amountOfResults
		);

		for ($i = 0; $i < $amountOfResults; ++$i) {
			$content = '';

			$media = (trim($cssTags[2][$i]) === '') ? 'all' : $cssTags[2][$i];
			$media = implode(',', array_map('trim', explode(',', $media)));

			$relation = (trim($cssTags[4][$i]) === '') ? 'stylesheet' : $cssTags[4][$i];
			$source = $cssTags[3][$i];
			$title = trim($cssTags[5][$i]);
			$ignoreDataFlagSet = (int) $cssTags[6][$i];
			$asAttribute = $cssTags[8][$i];

			// add basic entry
			$this->css[$relation][$media][$i]['minify-ignore'] = FALSE;
			$this->css[$relation][$media][$i]['compress-ignore'] = FALSE;
			$this->css[$relation][$media][$i]['merge-ignore'] = FALSE;
			$this->css[$relation][$media][$i]['addInDocument'] = FALSE;
			$this->css[$relation][$media][$i]['useOriginalCodeLine'] = FALSE;
			$this->css[$relation][$media][$i]['file'] = $source;
			$this->css[$relation][$media][$i]['content'] = '';
			$this->css[$relation][$media][$i]['basename'] = '';
			$this->css[$relation][$media][$i]['title'] = $title;
			$this->css[$relation][$media][$i]['asAttribute'] = $asAttribute;
			$this->css[$relation][$media][$i]['position-key'] = $i;
			$this->css[$relation][$media][$i]['original'] = $cssTags[0][$i];

			// Ignore this file if ignore is set to 1
			if ($ignoreDataFlagSet) {
				$this->css[$relation][$media][$i]['minify-ignore'] = TRUE;
				$this->css[$relation][$media][$i]['compress-ignore'] = TRUE;
				$this->css[$relation][$media][$i]['merge-ignore'] = TRUE;
				$this->css[$relation][$media][$i]['useOriginalCodeLine'] = TRUE;
				continue;
			}

			// styles which are added inside the document must be parsed again to fetch the pure css code
			$cssTags[1][$i] = (strtolower($cssTags[1][$i]) === 'sty' ? 'style' : strtolower($cssTags[1][$i]));
			if ($cssTags[1][$i] === 'style') {
				$cssContent = [];
				preg_match_all($filterInDocumentPattern, $cssTags[0][$i], $cssContent);

				// we don't need to continue if it was an empty style tag
				if ($cssContent[1][0] === '') {
					unset($this->css[$relation][$media][$i]);
					continue;
				}

				// ignore this file if the content could not be fetched
				$doNotRemoveInDoc = $this->configuration['css.']['doNotRemoveInDoc'] === '1';
				if ($doNotRemoveInDoc) {
					$this->css[$relation][$media][$i]['minify-ignore'] = TRUE;
					$this->css[$relation][$media][$i]['compress-ignore'] = TRUE;
					$this->css[$relation][$media][$i]['merge-ignore'] = TRUE;
					$this->css[$relation][$media][$i]['useOriginalCodeLine'] = TRUE;
					continue;
				}

				// save the content into a temporary file
				$hash = md5($cssContent[1][0]);
				$source = $this->tempDirectories['temp'] . 'inDocument-' . $hash;
				$tempFile = $source . '.css';
				if (!file_exists($source . '.css')) {
					$this->writeFile($tempFile, $cssContent[1][0]);
				}

				// try to resolve any @import occurrences
				$content = \Minify_ImportProcessor::process($tempFile);
				$this->css[$relation][$media][$i]['file'] = $tempFile;
				$this->css[$relation][$media][$i]['content'] = $content;
				$this->css[$relation][$media][$i]['basename'] = basename($source);
			} elseif ($source !== '') {
				if ($this->configuration['css.']['ignore'] !== '' &&
					preg_match($this->configuration['css.']['ignore'], $source)
				) {
					$this->css[$relation][$media][$i]['minify-ignore'] = TRUE;
					$this->css[$relation][$media][$i]['compress-ignore'] = TRUE;
					$this->css[$relation][$media][$i]['merge-ignore'] = TRUE;
					$this->css[$relation][$media][$i]['useOriginalCodeLine'] = TRUE;
					continue;
				}

				// check if the file should be ignored for some processes
				$ignoreAmount = 0;
				if (($this->configuration['css.']['minify.']['ignore'] !== '') && preg_match(
					$this->configuration['css.']['minify.']['ignore'],
					$source
				)
				) {
					$this->css[$relation][$media][$i]['minify-ignore'] = TRUE;
					++$ignoreAmount;
				}

				if (($this->configuration['css.']['compress.']['ignore'] !== '') && preg_match(
					$this->configuration['css.']['compress.']['ignore'],
					$source
				)
				) {
					$this->css[$relation][$media][$i]['compress-ignore'] = TRUE;
					++$ignoreAmount;
				}

				if (($this->configuration['css.']['merge.']['ignore'] !== '') && preg_match(
					$this->configuration['css.']['merge.']['ignore'],
					$source
				)
				) {
					$this->css[$relation][$media][$i]['merge-ignore'] = TRUE;
					++$ignoreAmount;
				}

				if ($ignoreAmount === 3) {
					$this->css[$relation][$media][$i]['useOriginalCodeLine'] = TRUE;
				}

				$localFile = $source;
				if ($GLOBALS['TSFE']->absRefPrefix !== '' &&
					\strpos($localFile, (string) $GLOBALS['TSFE']->absRefPrefix) === 0
				) {
					$localFile = \substr($localFile, \strlen($GLOBALS['TSFE']->absRefPrefix) - 1);
				}

				[$localFile,] = explode('?', $localFile);

				$pathSite = Environment::getPublicPath() . '/';
				$localFile = $pathSite . ltrim($localFile, '/');
				$file = \file_exists($localFile) ? $localFile : $source;
				$integrity = $cssTags[7][$i];
				try {
					$tempFile = $this->getFile($file, FALSE, $integrity);
					$content = \Minify_ImportProcessor::process($tempFile);
				} catch (BrokenIntegrityException $exception) {
					// The file integrity is broken, this could mean that the script target got hacked and is not
					// safe anymore. We need to abort merging of this script and report the issue
					$this->logger->warning($exception->getMessage(), [
						'file' => $file,
						'integrity' => $integrity,
						'tag' => $cssTags[0][$i]
					]);
				}

				// ignore this file if the content could not be fetched
				if ($content === '') {
					$this->css[$relation][$media][$i]['minify-ignore'] = TRUE;
					$this->css[$relation][$media][$i]['compress-ignore'] = TRUE;
					$this->css[$relation][$media][$i]['merge-ignore'] = TRUE;
					$this->css[$relation][$media][$i]['useOriginalCodeLine'] = TRUE;
					continue;
				}

				// set the css file with its content
				$this->css[$relation][$media][$i]['content'] = $content;
			}

			// get base name for later usage
			// base name without a file prefix and prefixed hash of the content
			$filename = basename($source);
			$hash = md5($content);
			$this->css[$relation][$media][$i]['basename'] =
				substr($filename, 0, strrpos($filename, '.')) . '-' . $hash;
		}
	}

	/**
	 * This method minifies a css file. It's based upon the Minify_CSS class
	 * of the project minify.
	 *
	 * @param array $properties properties of an entry (copy-by-reference is used!)
	 * @return string new filename
	 */
	protected function minifyFile(&$properties) {
		// get new filename
		$newFile = $this->tempDirectories['minified'] .
			$properties['basename'] . '.min.css';

		// stop further processing if the file already exists
		if (file_exists($newFile)) {
			$properties['basename'] .= '.min';
			$properties['content'] = file_get_contents($newFile);
			return $newFile;
		}

		// minify content
		if (!class_exists('Minify_CSS', FALSE)) {
			require_once(ExtensionManagementUtility::extPath('scriptmerger') . 'Resources/Minify/CSS.php');
		}

		$properties['content'] = \Minify_CSS::minify($properties['content']);

		// save content inside the new file
		$this->writeFile($newFile, $properties['content']);

		// save new part of the base name
		$properties['basename'] .= '.min';

		return $newFile;
	}

	/**
	 * This method compresses a css file.
	 *
	 * @param array $properties properties of an entry (copy-by-reference is used!)
	 * @return string new filename
	 */
	protected function compressFile(&$properties) {
		$newFile = $this->tempDirectories['compressed'] . $properties['basename'] . '.gz.css';
		if (file_exists($newFile)) {
			return $newFile;
		}

		$this->writeFile($newFile, gzencode($properties['content'], 5));

		return $newFile;
	}

	/**
	 * This method writes the css back to the document.
	 *
	 * @return void
	 */
	protected function writeToDocument() {
		$pattern = '';
		if (trim($this->configuration['css.']['mergedFilePosition']) !== '') {
			$pattern = '/' . preg_quote($this->configuration['css.']['mergedFilePosition'], '/') . '/i';
		}

		$contentShouldBeAddedInline = $this->configuration['css.']['addContentInDocument'] === '1';
		$integrity = (bool) $this->configuration['css.']['integrityCalculation'];
		foreach ($this->css as $relation => $cssByRelation) {
			$cssByRelation = array_reverse($cssByRelation);
			foreach ($cssByRelation as $media => $cssByMedia) {
				$cssByMedia = array_reverse($cssByMedia);
				foreach ($cssByMedia as $cssProperties) {
					if (array_key_exists('useOriginalCodeLine', $cssProperties) &&
						$cssProperties['useOriginalCodeLine']
					) {
						$content = $cssProperties['original'];
					} elseif ((array_key_exists('addInDocument', $cssProperties) &&
							$cssProperties['addInDocument']) || $contentShouldBeAddedInline
					) {
						$content = LF . "\t" .
							'<style media="' . $media . '">' . LF .
							"\t" . '/* <![CDATA[ */' . LF .
							"\t" . $cssProperties['content'] . LF .
							"\t" . '/* ]]> */' . LF .
							"\t" . '</style>' . LF;
					} else {
						$file = $cssProperties['file'];
						if (file_exists($file)) {
							$pathSite = Environment::getPublicPath() . '/';
							$file = $GLOBALS['TSFE']->absRefPrefix .
								($pathSite === '/' ? $file : str_replace($pathSite, '', $file));
						}

						$title = '';
						if (array_key_exists('title', $cssProperties)) {
							$title = (trim($cssProperties['title']) !== '' ?
								'title="' . $cssProperties['title'] . '" ' : '');
						}

						$asAttribute = '';
						if (array_key_exists('asAttribute', $cssProperties)) {
							$asAttribute = (trim($cssProperties['asAttribute']) !== '' ?
								'as="' . $cssProperties['asAttribute'] . '" ' : '');
						}

						$content = "\t" . '<link rel="' . $relation . '" '
							. 'type="text/css" '
							. 'media="' . $media . '" ' . $title . $asAttribute
							. 'href="' . $file . '" '
							. ($integrity ? 'integrity="' . $cssProperties['integrity'] . '" ' : '')
							. 'crossorigin="anonymous" />' . LF;
					}

					$mergeIgnore = $cssProperties['merge-ignore'] ?? FALSE;
					if ($pattern === '' || $mergeIgnore) {
						$this->content = str_replace(
							'###MERGER' . $cssProperties['position-key'] . 'MERGER###',
							$content,
							$this->content
						);
						continue;
					}

					$this->content = preg_replace(
						$pattern,
						$content . '\0',
						$this->content,
						1
					);
				}
			}
		}

		// remove any empty markers
		$pattern = '/###MERGER[0-9]*?MERGER###/is';
		$this->content = preg_replace($pattern, '', (string) $this->content);
	}
}
