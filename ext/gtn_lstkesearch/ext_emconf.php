<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Faceted Search For LST Products',
    'description' => 'Faceted Search For LST Products',
    'category' => 'backend',
    'version' => '1.0.0',
    'dependencies' => 'ke_search',
    'state' => 'stable',
    'author' => 'GTN solutions',
    'author_email' => 'szavarzin@gtn-solutions.com',
    'author_company' => 'GTN Solutions',
    'constraints' => array(
        'depends' => array(
            'typo3' => '8.7.0-10.7.99',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
);
