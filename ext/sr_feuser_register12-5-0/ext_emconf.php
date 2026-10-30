<?php

/*
 * Extension Manager configuration file for ext "sr_feuser_register".
 */

$EM_CONF[$_EXTKEY] = [
	'title' => 'Front End User Registration',
	'description' => 'A self-registration variant of Kasper Skårhøj\'s Front End User Admin extension for TYPO3 CMS.',
	'category' => 'plugin',
	'state' => 'stable',
	'clearCacheOnLoad' => 1,
	'author' => 'Stanislas Rolland',
	'author_email' => 'typo3AAAA@sjbr.ca',
	'author_company' => 'SJBR',
	'version' => '12.5.0',
	'constraints' => [
		'depends' => [
			'typo3' => '12.4.0-12.4.99',
			'felogin' => '12.4.0-12.4.99',
			'static_info_tables' => '12.4.2-12.4.99'
		],
		'conflicts' => [
		],
		'suggests' => [
			'sr_freecap' => '12.4.2-12.4.99'
		]
    ],
    'autoload' => [
        'psr-4' => [
        	'SJBR\\SrFeuserRegister\\' => 'Classes'
        ]
    ]
];