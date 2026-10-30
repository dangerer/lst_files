<?php
/*
 * Extension Manager configuration file for ext "sr_email_subscribe".
 *
 */
$EM_CONF[$_EXTKEY] = [
	'title' => 'Email Address Subscription',
	'description' => 'Email address subscription for TYPO3 CMS.',
	'category' => 'plugin',
	'version' => '12.4.4',
	'state' => 'stable',
	'clearcacheonload' => 1,
	'author' => 'Stanislas Rolland',
	'author_email' => 'typo3AAAA@sjbr.ca',
	'author_company' => 'SJBR',
	'constraints' => [
		'depends' => [
			'typo3' => '12.4.0-12.4.99',
			'sr_feuser_register' => '12.4.3-12.4.99',
			'tt_address' => '8.0.0-8.0.99',
			'static_info_tables' => '12.4.2-12.4.99'
		],
		'suggests' => [
			'sr_freecap' => '12.4.2-12.4.99'
		]
    ],
    'autoload' => [
        'psr-4' => [
        	'SJBR\\SrEmailSubscribe\\' => 'Classes'
        ]
    ]
];