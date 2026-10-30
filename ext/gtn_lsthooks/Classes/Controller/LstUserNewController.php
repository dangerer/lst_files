<?php
namespace Gtn\GtnLsthooks\Controller;

use In2code\Femanager\Domain\Model\User;
use Psr\Http\Message\ResponseInterface;

class LstUserNewController extends \In2code\Femanager\Controller\NewController {

    /**
     * Initialize create action for setting the right custom data type for the user.
     */
    public function initializeCreateAction() {
        if ($this->arguments->hasArgument('user')) {
            // Workaround to avoid php7 warnings of wrong type hint.
            /** @var \Gtn\GtnLsthooks\Domain\Model\LstUser $user */
            $user = $this->arguments['user'];
            $user->setDataType(\Gtn\GtnLsthooks\Domain\Model\LstUser::class);
        }
    }

    /**
     * action create
     *
     * @param User $user
     * @validate $user In2code\Femanager\Domain\Validator\ServersideValidator
     * @validate $user In2code\Femanager\Domain\Validator\PasswordValidator
     * @return void
     */
    public function createAction(User $user, string $captcha = null): ResponseInterface
    {
       return parent::createAction($user);
    }
}