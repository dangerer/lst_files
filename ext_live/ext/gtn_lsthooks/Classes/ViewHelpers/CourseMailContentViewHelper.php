<?php
namespace Gtn\GtnLsthooks\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class CourseMailContentViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        parent::initializeArguments();
		$this->registerArgument('data', 'mixed', 'Data to return', false, '');
    }

	/**
	 * @return mixed
	 */
	public function render()
	{	
		$translFilePrefix = 'LLL:fileadmin/Lst2017/Resources/Private/Language/locallang.xlf:';
		$recomPrefix = 'courses.mail.recommendations.';
		$confirmPrefix = 'courses.mail.confirmation_info.';

		$cmd = $this->arguments['data']['cmd'] ?? '';
		switch($cmd) {
			case 'after_form':
				$kursorganizer = $this->arguments['data']['kursorganizer'] ?? '';
				switch($kursorganizer) {
					case 'LST-Firmenzentrale Pockau':
						return LocalizationUtility::translate(
									$translFilePrefix . $recomPrefix . 'lst_firmenzentrale_pockau', null
								);
						break;
					case 'LST-Verkaufsbüro Erfurt':
						return LocalizationUtility::translate(
									$translFilePrefix . $recomPrefix . 'lst_verkaufsbuero_erfurt', null
								);
						break;
					case 'LST-Verkaufsbüro Mönchengladbach':
						return LocalizationUtility::translate(
									$translFilePrefix . $recomPrefix . 'lst_verkaufsbuero_moenchengladbach', null
								);
						break;
					case 'LST-Verkaufsbüro Augsburg':
						return LocalizationUtility::translate(
									$translFilePrefix . $recomPrefix . 'lst_verkaufsbuero_augsburg', null
								);
						break;
					case 'LST-Verkaufsbüro Hamburg':
						return LocalizationUtility::translate(
									$translFilePrefix . $recomPrefix . 'lst_verkaufsbuero_hamburg', null
								);
						break;
					default:
						return '';
				}
			case 'before_form':
				$kursorganizer = $this->arguments['data']['kursorganizer'] ?? '';
				switch($kursorganizer) {
					case 'LST-Firmenzentrale Pockau':
					case 'LST-Verkaufsbüro Erfurt':
					case 'LST-Verkaufsbüro Mönchengladbach':
					case 'LST-Verkaufsbüro Augsburg':
					case 'LST-Verkaufsbüro Hamburg':
						return LocalizationUtility::translate(
							$translFilePrefix . $confirmPrefix . 'ger_locations', null
						);
						break;
					case 'LST-Firmenzentrale Wien':
					case 'LST-Niederlassung Graz':
					case 'LST-Niederlassung Innsbruck':
					case 'LST-Niederlassung Internationaler Verkauf':
						return LocalizationUtility::translate(
							$translFilePrefix . $confirmPrefix . 'aut_locations', null
						);
						break;
					default:
						return LocalizationUtility::translate(
							$translFilePrefix . $confirmPrefix . 'default', null
						);
				}
			default:
				break;
		}

		return '';
	}
}
