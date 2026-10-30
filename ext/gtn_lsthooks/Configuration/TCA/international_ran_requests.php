<?php

defined('TYPO3') or die();

return [
    'ctrl' => [
        'title' => 'RAN requests for admin approving',
        'label' => 'kundennummer',
        'label_userFunc' => 'Gtn\\GtnLsthooks\\Hooks\\RecordListHook->getRecordLabel',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'languageField' => 'sys_language_uid',
        'default_sortby' => 'ORDER BY tstamp DESC',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'typeicon_classes' => [
            'default' => 'ran-international-request-item'
        ],
        'searchFields' => 'uid,kundennummer,kundenreferenz,vorname,nachname,telefon,firma',
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ],
                ],
                'default' => 0,
            ]
        ],
        'pid' => [
            'label' => 'pid',
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'crdate' => [
            'label' => 'crdate',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime',
            ]
        ],
        'tstamp' => [
            'label' => 'tstamp',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime',
            ]
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => 0
            ]
        ],
        'rmanr' => [
            'exclude' => false,
            'label' => 'rmanr',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'readOnly' => true,
            ]
        ],
        'kundennummer' => [
            'exclude' => false,
            'label' => 'kundennummer',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'readOnly' => true,
            ]
        ],
        'kundenreferenz' => [
            'exclude' => false,
            'label' => 'kundenreferenz',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'readOnly' => true,
            ]
        ],
        'anrede' => [
            'exclude' => false,
            'label' => 'anrede',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'readOnly' => true,
            ]
        ],
        'vorname' => [
            'exclude' => false,
            'label' => 'vorname',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'readOnly' => true,
            ]
        ],
        'nachname' => [
            'exclude' => false,
            'label' => 'nachname',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'readOnly' => true,
            ]
        ],
        'email' => [
            'exclude' => false,
            'label' => 'email',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'readOnly' => true,
            ]
        ],
        'telefon' => [
            'exclude' => false,
            'label' => 'telefon',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'readOnly' => true,
            ]
        ],
        'firma' => [
            'exclude' => false,
            'label' => 'firma',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'readOnly' => true,
            ]
        ],
        'plz' => [
            'exclude' => false,
            'label' => 'plz',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'readOnly' => true,
            ]
        ],
        'marker' => [
            'exclude' => false,
            'label' => 'city',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'readOnly' => true,
            ]
        ],
        'marker_02' => [
            'exclude' => false,
            'label' => 'address',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'readOnly' => true,
            ]
        ],
        'land2' => [
            'exclude' => false,
            'label' => 'land2',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'readOnly' => true,
            ]
        ],
        'region' => [
            'exclude' => false,
            'label' => 'region',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'readOnly' => true,
            ]
        ],
        'articlesdata' => [
            'exclude' => false,
            'label' => 'articlesdata',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'readOnly' => true,
            ]
        ],
        'anzahl' => [
            'exclude' => false,
            'label' => 'anzahl',
            'config' => [
                'type' => 'text',
                'cols' => 20,
                'rows' => 2,
                'readOnly' => true,
            ],
        ],
        'type' => [
            'exclude' => false,
            'label' => 'type',
            'config' => [
                'type' => 'text',
                'cols' => 20,
                'rows' => 2,
                'readOnly' => true,
            ],
        ],
        'artikelnummer' => [
            'exclude' => false,
            'label' => 'artikelnummer',
            'config' => [
                'type' => 'text',
                'cols' => 20,
                'rows' => 2,
                'readOnly' => true,
            ],
        ],
        'auftragsnummer' => [
            'exclude' => false,
            'label' => 'auftragsnummer',
            'config' => [
                'type' => 'text',
                'cols' => 20,
                'rows' => 2,
                'readOnly' => true,
            ],
        ],
        'deliverynumber' => [
            'exclude' => false,
            'label' => 'deliverynumber',
            'config' => [
                'type' => 'text',
                'cols' => 20,
                'rows' => 2,
                'readOnly' => true,
            ],
        ],
        'seriennummer' => [
            'exclude' => false,
            'label' => 'seriennummer',
            'config' => [
                'type' => 'text',
                'cols' => 20,
                'rows' => 2,
                'readOnly' => true,
            ],
        ],
        'returnreason' => [
            'exclude' => false,
            'label' => 'returnreason',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['Rücksendegrund:', ''],
                    ['Falschlieferung (Ware entspricht nicht der Bestellung)', 'Falschlieferung (Ware entspricht nicht der Bestellung)'],
                    ['Überlieferung (Gelieferte Menge ist größer als bestellte Menge)', 'Überlieferung (Gelieferte Menge ist größer als bestellte Menge)'],
                    ['Irrtümliche Lieferung (Warenlieferung ohne vorliegende Bestellung)', 'Irrtümliche Lieferung (Warenlieferung ohne vorliegende Bestellung)'],
                    ['Offener Transportschaden (Verpackung beschädigt / Inhalt beschädigt)', 'Offener Transportschaden (Verpackung beschädigt / Inhalt beschädigt)'],
                    ['Verdeckter Transportschaden (Verpackung einwandfrei / Inhalt beschädigt)', 'Verdeckter Transportschaden (Verpackung einwandfrei / Inhalt beschädigt)'],
                    ['Mangelhafte Funktion / Defekt (innerhalb Gewährleistungsfrist)', 'Mangelhafte Funktion / Defekt (innerhalb Gewährleistungsfrist)'],
                    ['Mangelhafte Funktion / Defekt (kostenpflichtige Reparatur)', 'Mangelhafte Funktion / Defekt (kostenpflichtige Reparatur)'],
                    ['Verspätete Lieferung (Ware wird nach Lieferverzug nicht mehr benötigt)', 'Verspätete Lieferung (Ware wird nach Lieferverzug nicht mehr benötigt)'],
                    ['Warenrücknahme im Rahmen der Kulanz (Zusage liegt vor)', 'Warenrücknahme im Rahmen der Kulanz (Zusage liegt vor)'],
                    ['Retoure aus laufendem Projekt (LST/MEP-intern)', 'Retoure aus laufendem Projekt (LST/MEP-intern)'],
                    ['Material zur Gutschrift retour (LST/MEP-intern)', 'Material zur Gutschrift retour (LST/MEP-intern)'],
                    ['Sonstiges', 'Sonstiges'],
                ],
                'size' => 1,
                'maxitems' => 1,
                'eval' => '',
                'readOnly' => true,
            ]
        ],
        'beschreibung' => [
            'exclude' => false,
            'label' => 'beschreibung',
            'config' => [
                'type' => 'text',
                'cols' => 20,
                'rows' => 2,
                'readOnly' => true,
            ],
        ],
        'datenschutz' => [
            'exclude' => false,
            'label' => 'datenschutz',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'readOnly' => true,
            ]
        ],
        'hash' => [
            'exclude' => false,
            'label' => 'hash',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'readOnly' => true,
            ]
        ],
        'submit_tstamp' => [
            'label' => 'tstamp of submitting',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime',
                'readOnly' => true,
            ]
        ],
        'status' => [
            'label' => 'Status',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'readOnly' => true,
            ]
        ],
        'decline_reason' => [
            'label' => 'Ablehnungsgrund',
            'config' => [
                'type' => 'text',
                'cols' => 20,
                'rows' => 4,
                'readOnly' => true,
            ],
        ],
        'approve_decline_buttons' => [
            'exclude' => true,
            'label' => 'Genehmigen / Ablehnen',
            'description' => 'Sie können diesen Datensatz genehmigen oder ablehnen',
            'config' => [
                'type' => 'user',
                'renderType' => 'ranActionButtons',
//                'parameters' => [
//                    'action' => 'approve',
//                ],
            ],
        ],
       /* 'decline_button' => [
            'exclude' => true,
            'label' => 'Decline',
            'description' => 'Decline this request',
            'config' => [
                'type' => 'user',
                'renderType' => 'ranActionButton',
                'parameters' => [
                    'action' => 'decline',
                ],
            ],
        ],*/

    ],
    'types' => [
        0 => [
            'showitem' => 'kundennummer, kundenreferenz,
            --div--;personenbezogene Daten, anrede, vorname, nachname, email, telefon, firma, plz, marker, marker_02, land2, region,
            --div--;Artikeldaten, anzahl, type, artikelnummer, auftragsnummer, deliverynumber, seriennummer, returnreason, beschreibung,
            --div--;Status, status, submit_tstamp, decline_reason,
            --div--;Genehmigen / Ablehnen, approve_decline_buttons
            '
        ],
    ],
    'palettes' => [
        /*'paletteCore' => [
            'showitem' => 'hidden,',
        ],
        'paletteLanguage' => [
            'showitem' => '
                sys_language_uid;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:sys_language_uid_formlabel,l10n_parent, l10n_diffsource,
            ',
        ],*/
    ]
];
