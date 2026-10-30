<?php

namespace Gtn\GtnLsthooks\EventListeners;


use In2code\Powermail\Events\FormControllerCreateActionAfterMailDbSavedEvent;
use In2code\Powermail\Events\FormControllerFormActionEvent;
use In2code\Powermail\Events\FormControllerConfirmationActionEvent;
use In2code\Powermail\Events\FormControllerCreateActionBeforeRenderViewEvent;
use In2code\Powermail\Events\SendMailServicePrepareAndSendEvent;
use In2code\Powermail\Events\ReceiverMailSenderPropertiesGetSenderEmailEvent;
use In2code\Powermail\Events\ReceiverMailSenderPropertiesGetSenderNameEvent;
use Gtn\GtnLsthooks\Hooks\PowermailHooks;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PowermailEventListener
{


	public function afterSaveDB(FormControllerCreateActionAfterMailDbSavedEvent $event): void
	{
        $hooks = GeneralUtility::makeInstance(PowermailHooks::class);
        $hooks->afterSaveDB($event->getMail(), $event->getFormController());
        //AbstractMenuContentObject::processFilter
    }

    public function createBeforePreviewRender(FormControllerCreateActionBeforeRenderViewEvent $event): void
	{
        $hooks = GeneralUtility::makeInstance(PowermailHooks::class);
        $hooks->createBeforePreviewRender($event->getMail(), $event->getHash(), $event->getFormController());
    
        //AbstractMenuContentObject::processFilter
    }

    public function getFilesForAttach(SendMailServicePrepareAndSendEvent $event): void
	{
        $hooks = GeneralUtility::makeInstance(PowermailHooks::class);
        $hooks->getFilesForAttach($event->getMailMessage(), $event->getEmail(), $event->getSendMailService());
        //AbstractMenuContentObject::processFilter
    }

    public function getSenderEmail(ReceiverMailSenderPropertiesGetSenderEmailEvent $event): void
	{
        $hooks = GeneralUtility::makeInstance(PowermailHooks::class);
        $hooks->getSenderEmail($event->getSenderEmail(), null);
        //AbstractMenuContentObject::processFilter
    }

    public function getSenderName(ReceiverMailSenderPropertiesGetSenderNameEvent $event): void
	{
        $hooks = GeneralUtility::makeInstance(PowermailHooks::class);
        $hooks->getSenderName($event->getSenderName(), null);
        //AbstractMenuContentObject::processFilter
    }

    public function formBeforeRender(FormControllerFormActionEvent $event): void
	{
        $hooks = GeneralUtility::makeInstance(PowermailHooks::class);
        $hooks->formBeforeRender($event->getForm(), $event->getFormController());
        //AbstractMenuContentObject::processFilter
    }

    public function formBeforePreviewRender(FormControllerConfirmationActionEvent $event): void
	{
        $hooks = GeneralUtility::makeInstance(PowermailHooks::class);
        $hooks->formBeforePreviewRender($event->getMail(), $event->getFormController());
        //AbstractMenuContentObject::processFilter
    }
}