<?php
namespace SJBR\SrFeuserRegister\Configuration;

/*
 *  Copyright notice
 *
 *  (c) 2013-2025 Stanislas Rolland <typo3AAAA(arobas)sjbr.ca>
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
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

use SJBR\SrFeuserRegister\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class providing configuration help for extension SrFeuserRegister
 */
class ConfigurationHelper
{
	/**
	 * Extension key
	 *
	 * @var string
	 */
	public $extKey = 'sr_feuser_register';

	/**
	 * Renders a select element that allows to choose the encryption algoritm to be used by the extension
	 *
	 * @param array $params: Field information to be rendered
	 * @return string The HTML select field
	 */
	public function buildEncryptionAlgorithmSelector(array $params)
	{
		$extensionName = GeneralUtility::underscoredToUpperCamelCase($this->extKey);
		if (in_array('openssl', get_loaded_extensions())) {
			$encryptionAlgorithms = openssl_get_cipher_methods(true);
			if (!empty($encryptionAlgorithms)) {
				$field = '<br /><select id="sr-feuser-register-' . $params['fieldName'] . '" name="' . $params['fieldName'] . '" >' . LF;
				foreach ($encryptionAlgorithms as $encryptionAlgorithm) {
					$selected = $params['fieldValue'] == $encryptionAlgorithm ? 'selected="selected"' : '';
					$field .= '<option name="' . $encryptionAlgorithm . '" value="' . $encryptionAlgorithm . '" ' . $selected . '>' . $encryptionAlgorithm . '</option>' . LF;
				}
				$field .= '</select><br />' . LF;
			} else {
				$field = '<br />Available encryption algorithms could not be found. Algorithm AES-256-CBC will be used.<br />';
			}
		} else {
			$message = LocalizationUtility::translate('internal_openssl_not_installed', $extensionName);
			$field = '<br />' . $message . '<br />';
		}
		return $field;
	}
}