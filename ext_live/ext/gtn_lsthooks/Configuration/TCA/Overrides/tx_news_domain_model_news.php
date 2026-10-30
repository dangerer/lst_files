<?php
defined('TYPO3') or die();

// Show only needed categories in settings of news
//$oldColumn = $GLOBALS['TCA']['tx_news_domain_model_news']['columns']['categories'];

/* page TS config:
!!!!!! NECESSARILY for ROOT page !!!!!!

# for BE: show only needed categories. 0 - all; INT LIST - parent of tree
TCEFORM {
    tx_news_domain_model_news.categories {
    PAGE_TSCONFIG_IDLIST = 0
    }
}
*/


//$newColumn = $oldColumn;
// we need to select by not only parent uid, but also parent record for BE treeview
//$addCondition = ' AND (\'###PAGE_TSCONFIG_IDLIST###\' = \'0\' OR (\'###PAGE_TSCONFIG_IDLIST###\' != \'0\' AND (sys_category.parent IN (###PAGE_TSCONFIG_IDLIST###) OR sys_category.uid IN (###PAGE_TSCONFIG_IDLIST###)))) ';
//$newColumn['config']['foreign_table_where'] = $addCondition.' '.$newColumn['config']['foreign_table_where'];

//$GLOBALS['TCA']['tx_news_domain_model_news']['columns']['categories'] = $newColumn;

/* !!!!!!! is it news bug? !!!!!!
    categories list is empty with news dataProvider
*/
//unset($GLOBALS['TCA']['tx_news_domain_model_news']['columns']['categories']['config']['treeConfig']['dataProvider']);

// add custom fields
/**
 * Table configuration fe_users
 */
$newsColumns = [
   'lst_fullybooked' => [
        'exclude' => 1,
        'label' => 'fully booked',
        'config' => [
            'type' => 'check',
            'default' => 0,
        ]
    ],
];
$fields = 'lst_fullybooked';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_news_domain_model_news', $newsColumns, 1);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tx_news_domain_model_news',
    'lst_fullybooked',
    '',
    'after:bodytext'
);

