<?php
namespace GDO\Core;

use GDO\UI\GDT_Link;
use GDO\Session\GDO_Session;
use GDO\UI\GDT_HTML;
use GDO\UI\GDT_Page;
use GDO\UI\GDT_Container;
use GDO\CSS\Minifier;
use GDO\CSS\Module_CSS;

/**
 * General Website utility.
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 3.0.5
 * @see \GDO\UI\GDT_Page
 */
final class Website
{
	private static $_links = []; # TODO: Uppercase static members.
// 	private static $_inline_css = '';
	private static $_redirected = false;
	
	/**
	 * @param number $time
	 * @return \GDO\Core\GDT_Response
	 */
	public static function redirectBack($time=0, $default=null)
	{
	    return self::redirect(self::hrefBack($default), $time);
	}
	
	/**
	 * Try to get a referrer URL for hrefBack.
	 * @param string $default
	 * @return string
	 */
	public static function hrefBack($default=null)
	{
	    if (Application::instance()->isCLI())
	    {
	        return $default ? $default : hrefDefault();
	    }
	    
	    $sess = GDO_Session::instance();
	    if ( (!$sess) || (!($url = $sess->getLastURL())) )
	    {
	        $url = isset($_SERVER['HTTP_REFERER']) ?
	           $_SERVER['HTTP_REFERER'] :
	           ($default ? $default : hrefDefault());
	    }
	    return $url;
	}
	
	public static function redirect($url, $time=0)
	{
	    if (Application::instance()->isCLI())
	    {
	        return null;
	    }
		switch (Application::instance()->getFormat())
		{
			case 'html':
				if (Application::instance()->isAjax())
				{
					return GDT_Response::makeWith(GDT_HTML::withHTML(self::ajaxRedirect($url, $time)));
				}
				elseif (!self::$_redirected)
				{
					if ($time > 0)
					{
					    hdr("Refresh:$time; url=$url");
					}
					else
					{
						hdr('Location: ' . $url);
					}
					self::$_redirected = true;
				}
		}
		self::topResponse()->addField(GDT_Success::with('msg_redirect', [GDT_Link::anchor($url), $time]));
	}

	private static function ajaxRedirect($url, $time)
	{
		# Don't do this at home kids!
		return sprintf('<script>setTimeout(function(){ window.location.href="%s" }, %d);</script>', $url, $time*1000);
	}
	
	public static function addInlineCSS($css)
	{
	    Minifier::addInline($css);
// 	    self::$_inline_css .= $css;
	}
	
	public static function addCSS($path)
	{
	    Minifier::addFile($path);
// 	    self::addLink($path, 'text/css', 'stylesheet');
	}
	
	/**
	 * add an html <link>
	 * @param string $type = mime_type
	 * @param mixed $rel relationship (one
	 * @param int $media
	 * @param string $href URL
	 * @see http://www.w3schools.com/tags/tag_link.asp
	 */
	public static function addLink($href, $type, $rel, $title=null)
	{
		self::$_links[] = array($href, $type, $rel, $title);
	}
	
	public static function addPrefetch($href, $type)
	{
	    array_unshift(self::$_links, [$href, $type, 'prefetch']);
	}
	
	/**
	 * Output of {$head_links}
	 * @return string
	 */
	public static function displayLink()
	{
		$back = '';
		
		foreach(self::$_links as $link)
		{
			list($href, $type, $rel, $title) = $link;
			$title = $title ? " title=\"$title\"" : '';
			$back .= sprintf('<link rel="%s" type="%s" href="%s"%s />'."\n", $rel, $type, $href, $title);
		}
		
		if (Module_CSS::instance()->cfgMinify())
		{
		    return $back . "\n" . Minifier::renderMinified();
		}

		$back .= Minifier::renderOriginal();
		
		
		return $back;
	}
	
	############
	### Meta ###
	############
	private static $_meta = [];
	/**
	 * add an html <meta> tag
	 * @param array $meta = array($name, $content, 0=>name;1=>http-equiv);
	 * @param boolean $overwrite overwrite key if exist?
	 * @return boolean false if metakey was not overwritten, otherwise true
	 * @TODO possible without key but same functionality?
	 * @TODO strings as params? addMeta($name, $content, $mode, $overwrite)
	 */
	public static function addMeta(array $metaA, $overwrite=true)
	{
		if ((false === $overwrite) && (isset(self::$_meta[$metaA[0]]) === true))
		{
			return false;
		}
		self::$_meta[$metaA[0]] = $metaA;
		return true;
	}
	
	public static function addMetaA(array $metaA)
	{
		foreach($metaA as $meta)
		{
			self::addMeta($meta);
		}
	}
	
	/**
	 *
	 * @see addMeta()
	 */
	public static function displayMeta()
	{
	    $method = Application::instance()->getMethod();
	    if ($method && $method->isSEOIndexed())
	    {
    	    self::$_meta[] = ['robots', 'index, follow', 'name'];
	        
	    }
	    else
	    {
    	    self::$_meta[] = ['robots', 'noindex', 'name'];
	    }
	    $back = '';
// 		$mode = array('name', 'http-equiv');
		foreach (self::$_meta as $meta)
		{
// 			if (!is_array($meta))
// 			{
// 				continue; # TODO: spaceone fix.
// 			}
			list($name, $content, $equiv) = $meta;
// 			$equiv = $mode[$equiv];
            if ($content)
            {
                $back .= sprintf("<meta %s=\"%s\" content=\"%s\" />\n", $equiv, $name, $content);
            }
		}
		return $back;
	}
	
	/**
	 * Renders a json response and dies.
	 * @param mixed $json
	 * @param boolean $die
	 */
	public static function renderJSON($json)
	{
	    if (!Application::instance()->isCLI())
		{
			hdr('Content-Type: application/json');
		}
		return json_encode($json, JSON_PRETTY_PRINT); # pretty json
	}
	
	public static function outputStarted()
	{
		return headers_sent() || ob_get_contents();
	}
	
	#############
	### Error ###
	#############
	public static function error($key, array $args=null)
	{
	    self::topResponse()->addField(GDT_Error::with($key, $args, 405));
	}
	
	/**
	 * Redirect and show a message at the new page.
	 * @param string $key
	 * @param array $args
	 * @param string $url
	 * @param number $time
	 * @return \GDO\Core\GDT_Response
	 */
	public static function redirectMessage($key, array $args=null, $url=null, $time=0)
	{
	    return self::redirectMessageRaw(t($key, $args), $url, $time);
	}
	
	public static function redirectMessageRaw($message, $url=null, $time=0)
	{
	    $app = Application::instance();
	 
	    self::topResponse()->addField(GDT_Success::withText($message));
	  
	    if ($app->isCLI())
	    {
// 	        echo "{$message}\n";
	        if ($app->isUnitTests())
	        {
	            echo "Redirect => $url\n";
	        }
	        return;
	    }
	    
	    $url = $url === null ? self::hrefBack() : $url;
	    
	    if (!$app->isInstall())
	    {
	        GDO_Session::set('redirect_message', $message);
	        return self::redirect($url, $time);
	    }
	}
	
	public static function redirectError($key, array $args=null, $url=null, $time=0)
	{
	    return self::redirectErrorRaw(t($key, $args), $url, $time);
	}
	
	public static function redirectErrorRaw($message, $url=null, $time=0)
	{
	    self::topResponse()->addField(GDT_Error::withText($message, 405));
	    
	    if (Application::instance()->isCLI())
	    {
	        echo "{$message}\n";
	        return true;
	    }
	    
	    $url = $url === null ? self::hrefBack() : $url;
	    $app = Application::instance();
	    if (!$app->isInstall())
	    {
	        GDO_Session::set('redirect_error', $message);
	        return self::redirect($url, $time);
	    }
	}
	
	####################
	### Top Response ###
	####################
	public static $TOP_RESPONSE = null;
	public static function topResponse()
	{
	    if (self::$TOP_RESPONSE === null)
	    {
	        self::$TOP_RESPONSE = GDT_Container::make('topResponse');
	        if (!Application::instance()->isInstall())
	        {
	            if ($message = GDO_Session::get('redirect_message'))
	            {
	                GDO_Session::remove('redirect_message');
	                self::$TOP_RESPONSE->addField(GDT_Success::make()->textRaw($message));
	            }
	            if ($message = GDO_Session::get('redirect_error'))
	            {
	                GDO_Session::remove('redirect_error');
	                self::$TOP_RESPONSE->addField(GDT_Error::make()->textRaw($message));
	            }
	        }
	    }
	    return self::$TOP_RESPONSE;
	}
	
	public static function renderTopResponse()
	{
	    return self::topResponse()->render();
	}
	
	#####################
	### JSON Response ###
	#####################
	public static $JSON_RESPONSE;
	public static function jsonResponse()
	{
	    if (!self::$JSON_RESPONSE)
	    {
	        self::$JSON_RESPONSE = GDT_Response::make();
	    }
	    return self::$JSON_RESPONSE;
	}
	
	public static function renderJSONResponse()
	{
	    if (self::$JSON_RESPONSE)
	    {
	        return self::$JSON_RESPONSE->renderJSON();
	    }
	}
	
	####################
	### Generic Head ###
	####################
	private static $HEAD = '';
	public static function addHead($string)
	{
		self::$HEAD .= $string . "\n";
	}
	
	public static function displayHead()
	{
		return self::$HEAD;
	}
	
	#############
	### Title ###
	#############
	private static $TITLE = GDO_SITENAME;
	public static function setTitle($title)
	{
	    self::$TITLE = $title;
	    GDT_Page::$INSTANCE->titleRaw(self::displayTitle());
	}
	
	public static function displayTitle()
	{
	    $title = html(self::$TITLE);
	    if (module_enabled('Core'))
	    {
    	    if (Module_Core::instance()->cfgSiteShortTitleAppend())
    	    {
    	        $title .= " [" . GDO_SITENAME . "]";
    	    }
	    }
	    return $title;
	}

}
