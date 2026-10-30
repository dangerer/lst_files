<?php

namespace Gtn\GtnLsthooks\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use \GeorgRinger\News\Domain\Repository\NewsRepository;

/**
 * Hook
 *
 * @package TYPO3
 * @subpackage gtn_lsthooks
 */
class News {

    public $vacanciesPid = 115; // pid for vacancies records
    public $vacanciesListPid = 34; // pid for vacancies list (FE page)

    private $objectManager = null;
    private $tsNews = null;

    function __construct() {
        /** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager */
        $configurationManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
        $settings = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $this->tsNews = $settings['plugin.']['tx_news.'];
    }

    public function modifyDemand(array $params, NewsRepository $newsRepository) {
        if ($GLOBALS['TSFE']->id == $this->vacanciesListPid) { // only for page with jobs list
            if (isset($params['constraints']['categories'])) { // if selected at least one category
                if ($params['constraints']['categories'] instanceof \TYPO3\CMS\Extbase\Persistence\Generic\Qom\LogicalOr) { // selected TWO or more cateories
                    $jobThemeCatUids = GeneralUtility::trimExplode(',', $this->tsNews['lst_settings.']['jobThemeCategories']);
                    $gp = GeneralUtility::_GP('tx_news_pi1');
                    $gp = $gp['overwriteDemand']['categories'];
                    if ($gp && count($gp) > 1) {
                        $group1 = $group2 = array();
                        foreach ($gp as $catUid) {
                            if (in_array($catUid, $jobThemeCatUids)) {
                                $group2[] = $catUid; // job theme category
                            } else {
                                $group1[] = $catUid; // region category
                            }
                        }
                        if (count($group1) > 0 && count($group2) > 0) {
                            $query = $newsRepository->createQuery();
                            $grcond1 = $grcond2 = array();
                            foreach ($group1 as $g1) {
                                $grcond1[] = $query->contains('categories', $g1);
                            }
                            foreach ($group2 as $g2) {
                                $grcond2[] = $query->contains('categories', $g2);
                            }
                            $and = $query->logicalAnd(
                                $query->logicalOr(...$grcond1),
                                $query->logicalOr(...$grcond2)
                            );
                            $params['constraints']['categories'] = $and;
//                            DebuggerUtility::var_dump($params);
                        }
                    }

                }
            }
//        $this->updateConstraints($params['demand'], $params['respectEnableFields'], $params['query'], $params['constraints']);
        }
    }

}