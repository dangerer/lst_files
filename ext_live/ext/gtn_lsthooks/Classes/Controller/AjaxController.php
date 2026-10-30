<?php
namespace Gtn\GtnLsthooks\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 *
 */
class AjaxController extends ActionController {

	/**
	 * @return void
	 */
	public function searchRegionsByZipAction() {
		$regions = array();
		$zip = trim(GeneralUtility::_GP('zip'));
		$mainRegion = trim(GeneralUtility::_GP('mainregion'));
        $links = [];
		if ($zip != '') {
		    if ($withLinks = trim(GeneralUtility::_GP('withlinks'))) {
		        $links = [
                    'default' => $this->uriBuilder->reset()->setTargetPageUid(75)->setCreateAbsoluteUri(true)->build(), // Wien
		            'Wien' => $this->uriBuilder->reset()->setTargetPageUid(75)->setCreateAbsoluteUri(true)->build(),
                    'Graz' => $this->uriBuilder->reset()->setTargetPageUid(76)->setCreateAbsoluteUri(true)->build(),
                    'Innsbruck' => $this->uriBuilder->reset()->setTargetPageUid(77)->setCreateAbsoluteUri(true)->build(),
                    'Klagenfurt' => $this->uriBuilder->reset()->setTargetPageUid(105)->setCreateAbsoluteUri(true)->build(),
                    'Bregenz' => $this->uriBuilder->reset()->setTargetPageUid(107)->setCreateAbsoluteUri(true)->build(),
                    'Salzburg' => $this->uriBuilder->reset()->setTargetPageUid(106)->setCreateAbsoluteUri(true)->build(),
                    'Linz' => $this->uriBuilder->reset()->setTargetPageUid(104)->setCreateAbsoluteUri(true)->build(),
                    'Pockau-Lengefeld' => $this->uriBuilder->reset()->setTargetPageUid(74)->setCreateAbsoluteUri(true)->build(),
                    'MEP Erfurt' => $this->uriBuilder->reset()->setTargetPageUid(108)->setCreateAbsoluteUri(true)->build(),
                    'LST Hamburg' => $this->uriBuilder->reset()->setTargetPageUid(79)->setCreateAbsoluteUri(true)->build(),
                    'LST Mönchengladbach' => $this->uriBuilder->reset()->setTargetPageUid(78)->setCreateAbsoluteUri(true)->build(),
                    'Augsburg' => $this->uriBuilder->reset()->setTargetPageUid(12055)->setCreateAbsoluteUri(true)->build(),
                ];
            }

//			$zip = intval($zip); // delete first zero
            if (!is_numeric($zip)) {
                $zip = intval($zip);
            }
            $minMaxCondition = '';
			// get first couple from zip
			$couple = substr($zip, 0, 2);
			switch ($mainRegion) {
                case 'inter':
                    $countryCode = ''; // no search at all!
                    return '';
                    break;
                case 'de':
                    $countryCode = 'DEU';
                    if (strlen($zip) == 3) {
                        $minMaxCondition .= ' AND (min <= '.$zip.'99 OR min = \'\') 
                                      AND (max >= '.$zip.'00 OR max = \'\') ';
                    } elseif (strlen($zip) == 4) {
                        $minMaxCondition .= ' AND (min <= '.$zip.'9 OR min = \'\') 
                                      AND (max >= '.$zip.'0 OR max = \'\') ';
                    } elseif (strlen($zip) == 5) {
                        $minMaxCondition .= ' AND (min <= '.$zip.' OR min = \'\') 
                                      AND (max >= '.$zip.' OR max = \'\') ';
                    }
                    $field_forlink = 'region_title';
                    break;
                case 'au':
                    if (strlen($zip) == 3) {
                        $minMaxCondition .= ' AND (min <= '.$zip.'9 OR min = \'\') 
                                      AND (max >= '.$zip.'0 OR max = \'\') ';
                    } elseif (strlen($zip) == 4) {
                        $minMaxCondition .= ' AND (min <= '.$zip.' OR min = \'\') 
                                      AND (max >= '.$zip.' OR max = \'\') ';
                    }
                    $countryCode = 'AUT';
                    $field_forlink = 'region_city';
                    break;
                default:
                    $countryCode = 'AUT';
                    $field_forlink = 'region_city';
            }

			// search in DB
			$sql = 'SELECT DISTINCT country_code, region_title, category_uid, '.$field_forlink.' as title_for_link 
                            FROM lst_zip_category_mm 
                            WHERE zip_firstcouple LIKE \''.$couple.'%\' '.
                                ($countryCode ? ' AND country_code = \''.$countryCode.'\' ' : '')
                                .$minMaxCondition.' ';
//			echo $sql; exit;
			$res = $GLOBALS['TYPO3_DB']->sql_query($sql);
			/** @var \SJBR\StaticInfoTables\Domain\Repository\CountryRepository $countryRep */
			$countryRep = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\SJBR\StaticInfoTables\Domain\Repository\CountryRepository::class);
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
//			    print_r($row);
				if (strlen($zip) >= 5 && trim($row['country_code']) == 'AUT') {
//				    echo 'cont';
                    continue;
                }
                $regTitle = trim($row['region_title']);
                $title_forLink = trim($row['title_for_link']);
                if (!$title_forLink) {
                    $title_forLink = $regTitle;
                }
                if ($withLinks) {
                    $title = $title_forLink;
                } else {
                    $title = $regTitle;
                    if ($title != '' && trim($row['country_code']) != '') {
                        /** @var \SJBR\StaticInfoTables\Domain\Model\Country $countryName */
                        $countryName = $countryRep->findByIsoCodeA3($row['country_code'])->getFirst()->getNameLocalized();
                        $title .= ' <span class="text-muted">(' . $countryName . ')</span>';
                    }
                }
				$region = array('id' => $row['category_uid'], 'name' => $title, 'zip' => $zip);
                if ($withLinks) {
                    if (array_key_exists($regTitle, $links)) {
                        $region['link'] = $links[$title_forLink];
                    } else {
                        $region['link'] = $links['default'];
                    }
                }
				$regions[] = $region;
			}
		}
        $regions = array_unique($regions);
		// add Internationaler Kontakt to all results
		//$regions[] = array('id' => 15, 'name' => 'Internationaler Kontakt');
		echo json_encode($regions); exit;
//		return json_encode($regions);
	}

}
