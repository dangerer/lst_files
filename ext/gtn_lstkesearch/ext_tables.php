<?php
if (!defined('TYPO3')) {
	die('Access denied.');
}

// enable:
$GLOBALS['TCA']['tx_kesearch_indexerconfig']['columns']['startingpoints_recursive']['displayCond'] .= ',lstproducts';
// disable:
$GLOBALS['TCA']['tx_kesearch_indexerconfig']['columns']['targetpid']['displayCond'] .= ',lstproducts';
