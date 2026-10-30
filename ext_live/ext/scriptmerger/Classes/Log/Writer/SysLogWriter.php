<?php

/**
 *
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
 * This copyright notice MUST APPEAR in all copies of the script!
 */

namespace SGalinski\Scriptmerger\Log\Writer;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogRecord;
use TYPO3\CMS\Core\Log\Writer\AbstractWriter;
use TYPO3\CMS\Core\Log\Writer\WriterInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Log writer for the sys_log table
 * Because the DatabaseWriter is not able to write to the sys_log table in a way that is understood by the Log-Module
 * in the TYPO3 backend
 *
 * @package SGalinski\Scriptmerger\Log\Writer
 * @author Kevin Ditscheid <kevin.ditscheid@sgalinski.de>
 */
class SysLogWriter extends AbstractWriter {
	/**
	 * @var string
	 */
	protected $tableName = 'sys_log';

	/**
  * Writes the log record
  *
  * @param LogRecord $record Log record
  * @return WriterInterface $this
  * @throws \JsonException
  */
	public function writeLog(LogRecord $record) {
		$data = '';
		$recordData = $record->getData();
		if (!empty($recordData)) {
			// According to PSR3 the exception-key may hold an \Exception
			// Since json_encode() does not encode an exception, we run the _toString() here
			if (isset($recordData['exception']) && $recordData['exception'] instanceof \Exception) {
				$recordData['exception'] = (string) $recordData['exception'];
			}
			$data = '- ' . \json_encode($recordData, JSON_THROW_ON_ERROR);
		}

		$fieldValues = [
			'details' => $record->getMessage(),
			'tstamp' => $GLOBALS['EXEC_TIME'],
			'type' => 4,
			'error' => $this->mapLogLevelToError($record->getLevel()),
			'log_data' => \serialize($record->getData()),
			'request_id' => $record->getRequestId(),
			'time_micro' => $record->getCreated(),
			'component' => $record->getComponent(),
			'level' => $record->getLevel(),
			'message' => $record->getMessage(),
			'data' => $data
		];

		GeneralUtility::makeInstance(ConnectionPool::class)
			->getConnectionForTable($this->tableName)
			->insert($this->tableName, $fieldValues);

		return $this;
	}

	/**
	 * Map the given log level to an error
	 *
	 * @param int $logLevel
	 * @return int
	 * @see \TYPO3\CMS\Core\Authentication\BackendUserAuthentication->writelog
	 */
	protected function mapLogLevelToError($logLevel): int {
		$error = 0;
		switch ($logLevel) {
			case LogLevel::WARNING:
			case LogLevel::ERROR:
				$error = 1;
				break;
			case LogLevel::ALERT:
			case LogLevel::CRITICAL:
			case LogLevel::EMERGENCY:
				$error = 2;
				break;
			case LogLevel::INFO:
			case LogLevel::NOTICE:
			case LogLevel::DEBUG:
				$error = 0;
				break;
		}
		return $error;
	}
}
