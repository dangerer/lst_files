<?php
namespace Gtn\GtnLsthooks\Xclass\Powermail\Mail;

class PlaintextService extends \In2code\Powermail\Domain\Service\Mail\PlaintextService
{

    /**
     * Function makePlain() removes html tags and add linebreaks
     *        Easy generate a plain email bodytext from a html bodytext
     *
     * @param string $content HTML Mail bodytext
     * @return string $content
     */
    public function makePlain(string $content): string
    {
		if (@$GLOBALS['TSFE']->fe_user->user['uid'] == 4842) {

			var_dump($content);
			die();
		}

        $content = parent::makePlain($content);
        // To convert all HTML entities (like &amp;, &lt;, etc.) into their actual characters,

        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return $content;
    }

}