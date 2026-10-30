<?php
return [
    \In2code\Femanager\Domain\Model\User::class => [
        'subclasses' => [
            \Gtn\GtnLsthooks\Domain\Model\LstUser::class
        ],
        'properties' => [
            'country' => [
                'fieldName' => 'static_info_country'
            ],
            'feLanguage' => [
                'fieldName' => 'fe_language'
            ],
            'customerNumber' => [
                'fieldName' => 'customer_number'
            ],
        ],
    ],
    \Gtn\GtnLsthooks\Domain\Model\LstUser::class => [
        'tableName' => 'fe_users',
        'recordType' => 0,
        'properties' => [
            'country' => [
                'fieldName' => 'static_info_country'
            ],
            'feLanguage' => [
                'fieldName' => 'fe_language'
            ],
            'customerNumber' => [
                'fieldName' => 'customer_number'
            ],
        ],
    ],
    \Gtn\GtnLsthooks\Domain\Model\Category::class => [
        'tableName' => 'sys_category',
        'properties' => [
            'parentcategory' => [
                'fieldName' => 'parent',
            ],
        ],
    ],
];