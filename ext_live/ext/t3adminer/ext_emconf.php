<?php
$EM_CONF[$_EXTKEY] = [
	'title' => 'Adminer',
	'description' => 'Database administration tool \'Adminer\'',
	'category' => 'module',
	'author' => 'Jigal van Hemert',
	'author_email' => 'jigal.van.hemert@typo3.org',
	'author_company' => '',
	'state' => 'stable',
	'version' => '12.0.1',
    'autoload' => [
        'psr-4' => [
            'Jigal\T3adminer\\' => 'Classes'
        ],
    ],
	'constraints' => [
		'depends' => [
			'typo3' => '11.5.0-12.9.999',
            'php' => '7.4.0-8.2.999',
        ],
		'conflicts' => [],
		'suggests' => [],
    ],
];
