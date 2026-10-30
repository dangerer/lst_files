<?php
/**
 * Created by PhpStorm.
 * User: Ras
 * Date: 12.07.2018
 * Time: 10:32
 */

namespace Gtn\GtnLsthooks\Hooks;


use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class DirectMailHooks
{

    /**
     * @param array $idLists
     * @param \DirectMailTeam\DirectMail\Module\RecipientList|\DirectMailTeam\DirectMail\Module\Dmail $pObj
     * @param array $mailGroup
     */
    public function cmd_compileMailGroup_postProcess($idLists, $pObj, $mailGroup) {
        // only for Technical pids:
//        DebuggerUtility::var_dump($pObj);
        if (in_array($pObj->id, [390, 391] )) {
//            return $idLists;
            if (is_array($idLists['fe_users'])) {
                $userRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(FrontendUserRepository::class);
                $newList = array();
                foreach ($idLists['fe_users'] as $userUid) {
                    /** @var FrontendUser $user */
                    $user = $userRepository->findByUid($userUid);
                    if ($user instanceof FrontendUser) {
                        $userGroups = $user->getUsergroup();
                        /** @var FrontendUserGroup $group */
                        foreach ($userGroups as $group) {
                            // leave only Stufe 2 (uid:3), Stufe 3 (uid:4)
                            if (in_array($group->getUid(), [3, 4])) {
                                $newList[] = $userUid;
                                break;
                            }
                        }
                    }
                }
                $idLists['fe_users'] = $newList;
            }
        }
        return $idLists;
    }

}