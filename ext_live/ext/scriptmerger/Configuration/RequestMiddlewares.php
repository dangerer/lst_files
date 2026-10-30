<?php

use SGalinski\Scriptmerger\Middlewares\ContentPostProcessMiddleware;

return [
	'frontend' => [
		'sgalinski/scriptmerger' => [
			'target' => ContentPostProcessMiddleware::class,
			'description' => '',
			'before' => [
				'typo3/cms-frontend/content-length-headers'
			],
			'after' => [
				'typo3/cms-frontend/prepare-tsfe-rendering'
			]
		]
	]
];
