<?php
namespace GDO\Language\Method;

use GDO\Core\Method;
use GDO\Language\GDT_Language;
use GDO\Language\Trans;
use GDO\User\GDO_Session;
use GDO\Core\GDT_Success;
use GDO\Core\Website;
use GDO\Net\GDT_Url;

/**
 * Switch language to user's choice.
 * Overrides HTTP_ACCEPT_LANGUAGE and user_lang for language detection in Module_Language.
 * Stores your choice in your session.
 * 
 * @author gizmore
 * @since 6.09
 * @version 6.09
 * 
 * @see Module_Language
 * @see GDO_Session
 */
final class SwitchLanguage extends Method
{
	public function gdoParameters()
	{
		return array(
			GDT_Language::make('lang')->notNull(),
			GDT_Url::make('ref')->allowExternal(false)->allowLocal(),
		);
	}
	
	/**
	 * @return \GDO\Language\GDO_Language
	 */
	protected function getLanguage()
	{
		return $this->gdoParameterValue('lang');
	}
	
	public function execute()
	{
		# Set new ISO language
		$iso = $this->getLanguage()->getISO();
		GDO_Session::set('gdo-language', $iso);
		Trans::setISO($iso);
		
		# Build response
		$response = GDT_Success::responseWith('msg_language_set', [$this->getLanguage()->displayName()]);
		
		# Redirect if 'ref' is set
		if ($url = $this->gdoParameterVar('ref'))
		{
			$response->add(Website::redirect($url));
		}

		return $response;
	}
	
}
