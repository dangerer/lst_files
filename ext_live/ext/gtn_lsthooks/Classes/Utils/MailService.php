<?php
namespace Gtn\GtnLsthooks\Utils;
/***************************************************************
 *  Copyright notice
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * This class simulates an external API call to validate data
 *
 */
class MailService implements \TYPO3\CMS\Core\SingletonInterface {




	/**
	 * @param array $recipient recipient of the email in the format array('recipient@domain.tld' => 'Recipient Name')
	 * @param array $sender sender of the email in the format array('sender@domain.tld' => 'Sender Name')
	 * @param string $subject subject of the email
	 * @param string $templateName template name (UpperCamelCase)
	 * @param array $variables variables to be passed to the Fluid view
	 * @return boolean TRUE on success, otherwise false
	 */
    public function sendTemplateEmail(array $recipient, array $sender, $subject, $templateName, array $variables = array(), $attachments = null, $templateNameAsSource = false) {
        $configurationManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
        $extbaseFrameworkConfiguration = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $extbaseFrameworkConfiguration = $extbaseFrameworkConfiguration['plugin.']['tx_gtnlsthooks.'];
        if (empty($extbaseFrameworkConfiguration['view']['templateRootPath'])) {
            $extbaseFrameworkConfiguration['view']['templateRootPath'] = @$extbaseFrameworkConfiguration['view.']['templateRootPaths.'][0];
        }
        $templateRootPath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($extbaseFrameworkConfiguration['view']['templateRootPath']);
        /** @var \TYPO3\CMS\Fluid\View\StandaloneView $emailView */
        $emailView = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $templatePathAndFilename = $templateRootPath . 'Emails/' . $templateName . '.html';
        if ($templateNameAsSource) {
            $emailView->setTemplateSource($templateName);
        } else {
            $emailView->setTemplatePathAndFilename($templatePathAndFilename);
        }
        $emailView->assignMultiple($variables); // assign Fluid variable
        $emailBody = $emailView->render();
//		DebuggerUtility::var_dump($subject);
//		DebuggerUtility::var_dump($emailBody);
//		DebuggerUtility::var_dump($recipient);
//		DebuggerUtility::var_dump($sender);
//		echo $emailBody;
//		exit;
        /** @var $message \TYPO3\CMS\Core\Mail\MailMessage */
        $message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Mail\\MailMessage');
        $message->subject($subject)
            ->from(new \Symfony\Component\Mime\Address(key($sender), reset($sender)))
            ->sender(new \Symfony\Component\Mime\Address(key($sender)))
            ->replyTo(new \Symfony\Component\Mime\Address(key($sender), reset($sender)))
            ->priority(\Symfony\Component\Mime\Email::PRIORITY_NORMAL)
            ->to(new \Symfony\Component\Mime\Address(key($recipient)));

        // ATTACHMENT
//        if ($fileAttachment && file_exists($fileAttachment)) {
//            $mail->attachFromPath($fileAttachment);
//        }
        // HTML
        $HTMLContent = self::embedMedia($message, $emailBody);
        $message->html($HTMLContent);

        $message->setReturnPath(key($sender));
        $message->send();

/*
        $message->setTo($recipient)
            ->setFrom($sender)
            ->setSubject($subject);

        // Possible attachments here
        /*if (count($attachments)) {
            foreach ($attachments as $file => $name) {
                if (file_exists($file)) {
                    if (trim($name)) {
                        $message->attach(\Swift_Attachment::fromPath($file)->setFilename($name));
                    } else {
                        $message->attach(Swift_Attachment::fromPath($file));
                    }
                }
            }
        } ------ /

        // Plain text example
        //$message->setBody($emailBody, 'text/plain');

        // HTML Email
        $message->setBody($emailBody, 'text/html');

        $message->send();*/
        return $message->isSent();
    }

    /**
     * Embeds media into the mail message
     *
     * @param MailMessage $mail: mail message
     * @param string $htmlContent: the HTML content of the message
     * @return string the subtituted HTML content
     */
    static protected function embedMedia(MailMessage $mail, $htmlContent)
    {
        $typo3Version = GeneralUtility::makeInstance(Typo3Version::class);
        $typo3Branch = $typo3Version->getBranch();
        $substitutedHtmlContent = $htmlContent;
        $media = [];
        $attribRegex = self::makeTagRegex(array('img', 'embed', 'audio', 'video'));
        // Split the document by the beginning of the above tags
        $codepieces = preg_split($attribRegex, $htmlContent);
        $len = strlen($codepieces[0]);
        $pieces = count($codepieces);
        $reg = [];
        for ($i = 1; $i < $pieces; $i++) {
            $tag = strtolower(strtok(substr($htmlContent, $len + 1, 10), ' '));
            $len += strlen($tag) + strlen($codepieces[$i]) + 2;
            $dummy = preg_match('/[^>]*/', $codepieces[$i], $reg);
            // Fetches the attributes for the tag
            $attributes = self::getTagAttributes($reg[0]);
            // Adds each unique src to media only once
            $src_uid = md5($attributes['src']);
            if ($attributes['src'] && !isset($media[$src_uid])) {
                $media[$src_uid]['src'] = $attributes['src'];
                if (version_compare($typo3Branch, '10.4', '>=')) {
                    $mail->embedFromPath($attributes['src'], $src_uid);
                    $media[$src_uid]['cid'] = 'cid:' . $src_uid;
                } else {
                    $media[$src_uid]['cid'] = $mail->embed(\Swift_Image::fromPath($attributes['src']));
                }
            }
        }
        foreach ($media as $embeddedMedia) {
            $substitutedHtmlContent = str_replace(
                '"' . $embeddedMedia['src'] . '"',
                '"' . $embeddedMedia['cid'] . '"',
                $substitutedHtmlContent);
        }
        return $substitutedHtmlContent;
    }

    /**
     * Creates a regular expression out of an array of tags
     *
     * @param array $tags: the array of tags
     * @return string the regular expression
     */
    static protected function makeTagRegex(array $tags)
    {
        $regexpArray = array();
        foreach ($tags as $tag) {
            $regexpArray[] = '<' . $tag . '[[:space:]]';
        }
        return '/' . implode('|', $regexpArray) . '/i';
    }

    /**
     * This function analyzes a HTML tag
     * If an attribute is empty (like OPTION) the value of that key is just empty. Check it with is_set();
     *
     * @param string $tag: is either like this "<TAG OPTION ATTRIB=VALUE>" or this " OPTION ATTRIB=VALUE>" which means you can omit the tag-name
     * @return array array with attributes as keys in lower-case
     */
    static protected function getTagAttributes($tag)
    {
        $attributes = array();
        $tag = ltrim(preg_replace('/^<[^ ]*/', '', trim($tag)));
        $tagLen = strlen($tag);
        $safetyCounter = 100;
        // Find attribute
        while ($tag) {
            $value = '';
            $reg = preg_split('/[[:space:]=>]/', $tag, 2);
            $attrib = $reg[0];
            $tag = ltrim(substr($tag, strlen($attrib), $tagLen));
            if (substr($tag, 0, 1) == '=') {
                $tag = ltrim(substr($tag, 1, $tagLen));
                if (substr($tag, 0, 1) == '"') {
                    // Quotes around the value
                    $reg = explode('"', substr($tag, 1, $tagLen), 2);
                    $tag = ltrim($reg[1]);
                    $value = $reg[0];
                } else {
                    // No quotes around value
                    preg_match('/^([^[:space:]>]*)(.*)/', $tag, $reg);
                    $value = trim($reg[1]);
                    $tag = ltrim($reg[2]);
                    if (substr($tag, 0, 1) == '>') {
                        $tag = '';
                    }
                }
            }
            $attributes[strtolower($attrib)] = $value;
            $safetyCounter--;
            if ($safetyCounter < 0) {
                break;
            }
        }
        return $attributes;
    }



}
