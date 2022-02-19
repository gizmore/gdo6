<?php
namespace GDO\Language\Method;

use GDO\Core\Method;
use GDO\Core\Website;
use GDO\Language\GDT_Language;
use GDO\Language\Trans;
use GDO\Session\GDO_Session;
use GDO\Core\GDT_Success;
use GDO\Net\GDT_Url;

/**
 * Switch language to user's choice.
 * Overrides HTTP_ACCEPT_LANGUAGE and user_lang for language detection in Module_Language.
 * Stores your choice in your session.
 * 
 * @author gizmore
 * @version 6.11.3
 * @since 6.9.0
 * 
 * @see Module_Language
 * @see GDO_Session
 */
final class SwitchLanguage extends Method
{
	public function gdoParameters()
	{
		return [
			GDT_Language::make('_lang')->notNull(),
			GDT_Url::make('ref')->allowExternal(false)->allowLocal(),
		];
	}
	
	public function getDescription()
	{
	    if ($this->getLanguage(false))
	    {
	        return t($this->getDescriptionLangKey(), [$this->getLanguage()->displayName()]);
	    }
	    else
	    {
	        return t($this->getDescriptionLangKey().'2');
	    }
	}
	
	/**
	 * @return \GDO\Language\GDO_Language
	 */
	protected function getLanguage($throw=true)
	{
	    try
	    {
	        return $this->gdoParameterValue('_lang');
	    }
	    catch (\Throwable $ex)
	    {
	        if ($throw)
	        {
	            throw $ex;
	        }
	        return null;
	    }
	}
	
	public function execute()
	{
		# Set new ISO language
		$iso = $this->getLanguage()->getISO();
		$_SERVER['REQUEST_URI'] = preg_replace("/_lang=[a-z]{2}/", "_lang=".$iso , urldecode($_SERVER['REQUEST_URI']));
		$_REQUEST['_lang'] = $iso;
		GDO_Session::set('gdo-language', $iso);
		Trans::setISO($iso);
		
		# Build response
		$response = GDT_Success::responseWith('msg_language_set', [$this->getLanguage()->displayName()]);
		
		# Redirect if 'ref' is set
		if ($url = $this->gdoParameterVar('ref'))
		{
			$url = preg_replace("/_lang=[a-z]{2}/", "_lang=".$iso , $url);
			$response->addField(Website::redirect($url));
		}

		return $response;
	}
	
}
