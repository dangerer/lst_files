<?php

defined('TYPO3') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_address',
    array(
        'terms_acknowledged' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:sr_feuser_register/Resources/Private/Language/locallang_db.xlf:fe_users.terms_acknowledged',
            'config' => array(
                'type' => 'check',
                'default' => '0',
                'readOnly' => '1'
            )
        ),
    )
);

// change gender values from tt_address m/f => 0/1
// needed for correct using 'gender' for the table tt_address in the same way as fe_user
$GLOBALS['TCA']['tt_address']['columns']['gender']['config']['default'] = '0';
$GLOBALS['TCA']['tt_address']['columns']['gender']['config']['items'] = array(
    array('LLL:EXT:tt_address/locallang_tca.xml:tt_address.gender.m', '0'),
    array('LLL:EXT:tt_address/locallang_tca.xml:tt_address.gender.f', '1')
);




