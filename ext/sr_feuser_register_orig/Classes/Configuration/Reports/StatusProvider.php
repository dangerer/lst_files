<?php
namespace SJBR\SrFeuserRegister\Configuration\Reports;

/*
 *  Copyright notice
 *
 *  (c) 2012-2023 Stanislas Rolland <typo3AAAA(arobas)sjbr.ca>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
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
 */

use SJBR\SrFeuserRegister\Configuration\ConfigurationCheck;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extensionmanager\Utility\EmConfUtility;
use TYPO3\CMS\Reports\Status;
use TYPO3\CMS\Reports\StatusProviderInterface;

/**
 * Hook into the backend module "Reports" checking the configuration required for sr_feuser_register
 */
class StatusProvider implements StatusProviderInterface
{
	/**
	 * @var string Extension name
	 */
	protected $extensionName = 'SrFeuserRegister';

	/**
	 * Compiles a collection of system status checks as a status report.
	 *
	 * @return array List of statuses
	 */
    public function getStatus(): array
	{
		$reports = [
			'requiredExtensionsAreInstalled' => $this->checkIfRequiredExtensionsAreInstalled(),
			'noConflictingExtensionIsInstalled' => $this->checkIfNoConflictingExtensionIsInstalled()
		];
		return $reports;
	}

    /**
     * Return label of this status
     */
    public function getLabel(): string
    {
    	return LocalizationUtility::translate('LLL:EXT:sr_feuser_register/Resources/Private/Language/locallang_db.xlf:tt_content.list_type', $this->extensionName) . ' (sr_feuser_register)';
    }

	/**
	 * Check whether any required extension is not installed
	 *
	 * @return	Status
	 */
	protected function checkIfRequiredExtensionsAreInstalled()
	{
		$title = LocalizationUtility::translate('LLL:EXT:sr_feuser_register/Resources/Private/Language/locallang_statusreport.xlf:Required_extensions_not_installed', $this->extensionName);
		$missingExtensions = [];
		$constraints = ConfigurationCheck::getConstraints($this->extensionName);
		if (
			isset($constraints['depends'])
			&& is_array($constraints['depends'])
		) {
			$requiredExtensions = array_diff(array_keys($constraints['depends']), array('php', 'typo3'));
			foreach ($requiredExtensions as $extensionKey) {
				if (!ExtensionManagementUtility::isLoaded($extensionKey)) {
					$missingExtensions[] = $extensionKey;
				}
			}
		}
		if (count($missingExtensions)) {
			$value = LocalizationUtility::translate('LLL:EXT:sr_feuser_register/Resources/Private/Language/locallang_statusreport.xlf:keys', $this->extensionName) . ' ' . implode(', ', $missingExtensions);
			$message = LocalizationUtility::translate('LLL:EXT:sr_feuser_register/Resources/Private/Language/locallang_statusreport.xlf:install', $this->extensionName);
			$status = ContextualFeedbackSeverity::ERROR;
		} else {
			$value = LocalizationUtility::translate('LLL:EXT:sr_feuser_register/Resources/Private/Language/locallang_statusreport.xlf:none', $this->extensionName);
			$message = '';
			$status = ContextualFeedbackSeverity::OK;
		}
		return GeneralUtility::makeInstance(Status::class, $title, $value, $message, $status);
	}

	/**
	 * Check whether any conflicting extension has been installed
	 *
	 * @return	Status
	 */
	protected function checkIfNoConflictingExtensionIsInstalled()
	{
		$title = LocalizationUtility::translate('LLL:EXT:sr_feuser_register/Resources/Private/Language/locallang_statusreport.xlf:Conflicting_extensions_installed', $this->extensionName);
		$conflictingExtensions = [];
		$constraints = ConfigurationCheck::getConstraints($this->extensionName);
		if (
			isset($constraints['conflicts'])
			&& is_array($constraints['conflicts'])
        ) {
			foreach ($constraints['conflicts'] as $extensionKey => $version) {
				if (ExtensionManagementUtility::isLoaded($extensionKey)) {
					$conflictingExtensions[] = $extensionKey;
				}
			}
		}
		if (count($conflictingExtensions)) {
			$value = LocalizationUtility::translate('LLL:EXT:sr_feuser_register/Resources/Private/Language/locallang_statusreport.xlf:keys', $this->extensionName) . ' ' . implode(', ', $conflictingExtensions);
			$message = LocalizationUtility::translate('LLL:EXT:sr_feuser_register/Resources/Private/Language/locallang_statusreport.xlf:uninstall', $this->extensionName);
			$status = ContextualFeedbackSeverity::ERROR;
		} else {
			$value = LocalizationUtility::translate('LLL:EXT:sr_feuser_register/Resources/Private/Language/locallang_statusreport.xlf:none', $this->extensionName);
			$message = '';
			$status = ContextualFeedbackSeverity::OK;
		}
		return GeneralUtility::makeInstance(Status::class, $title, $value, $message, $status);
	}

	/**
	 * Get extension constraints
	 *
	 * @return	constraints
	 */
	protected function getConstraints($extensionName)
	{
		$extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored($extensionName);
        $emConfUtility = GeneralUtility::makeInstance(EmConfUtility::class);
		$emConf = $emConfUtility->includeEmConf($extensionKey, ExtensionManagementUtility::extPath($extensionKey));
        return $emConf['constraints'] ?? [];
    }
}