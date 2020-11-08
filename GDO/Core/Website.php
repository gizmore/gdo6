<?php
namespace GDO\Core;
use GDO\UI\GDT_Link;
use GDO\UI\GDT_Panel;
use GDO\Session\GDO_Session;
/**
 * General Website utility.
 * @author gizmore
 */
final class Website
{
	private static $_links = [];
	private static $_inline_css = '';
	
	public static function redirectBack($time=0)
	{
		return self::redirect(Website::hrefBack(), $time);
	}
	
	public static function hrefBack()
	{
	    if (Application::instance()->isCLI())
	    {
	        return hrefDefault();
	    }
	    if (!($url = GDO_Session::instance()->getLastURL()))
	    {
	        $url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : hrefDefault();
	    }
	    return $url;
	}
	
	public static function redirect($url, $time=0)
	{
		switch (Application::instance()->getFormat())
		{
			case 'html':
				if (Application::instance()->isAjax())
				{
					return GDT_Response::makeWith(GDT_Panel::withHTML(self::ajaxRedirect($url, $time)));
				}
				else
				{
					if ($time > 0)
					{
						header("Refresh:$time;url=$url");
					}
					else
					{
						header('Location: ' . $url);
					}
				}
		}
		self::topResponse()->add(GDT_Success::responseWith('msg_redirect', [GDT_Link::anchor($url), $time]));
	}

	private static function ajaxRedirect($url, $time)
	{
		# Don't do this at home kids!
		return sprintf('<script type="text/javascript">setTimeout(function(){ window.location.href="%s" }, %d);</script>', $url, $time*1000);
	}
	
	public static function addInlineCSS($css) { self::$_inline_css .= $css; }
	public static function addCSS($path, $media=0) { self::addLink($path, 'text/css', 'stylesheet', $media); }
	public static function addBowerCSS($path) { self::addCSS("bower_components/$path"); }
	
	/**
	 * add an html <link>
	 * @param string $type = mime_type
	 * @param mixed $rel relationship (one
	 * @param int $media
	 * @param string $href URL
	 * @see http://www.w3schools.com/tags/tag_link.asp
	 */
	public static function addLink($href, $type, $rel)
	{
		self::$_links[] = array(html($href), $type, $rel);
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
			list($href, $type, $rel) = $link;
			$back .= sprintf('<link rel="%s" type="%s" href="%s" />'."\n", $rel, $type, $href);
		}
		# embedded CSS (move?)
		if('' !== self::$_inline_css)
		{
			$back .= sprintf("\n\t<style><!--\n\t%s\n\t--></style>\n", self::indent(self::$_inline_css, 2));
		}
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
	 * @todo possible without key but same functionality?
	 * @todo strings as params? addMeta($name, $content, $mode, $overwrite)
	 */
	public static function addMeta(array $metaA, $overwrite=false)
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
		$back = '';
// 		$mode = array('name', 'http-equiv');
		foreach (self::$_meta as $meta)
		{
			if (!is_array($meta))
			{
				continue; # TODO: spaceone fix.
			}
			list($name, $content, $equiv) = $meta;
// 			$equiv = $mode[$equiv];
			$back .= sprintf("<meta %s=\"%s\" content=\"%s\" />\n", $equiv, $name, $content);
		}
		return $back;
	}
	
	/**
	 * Renders a json response and dies.
	 * @param mixed $json
	 * @param boolean $die
	 */
	public static function renderJSON($json, $die=true)
	{
		if (!Application::instance()->isCLI())
		{
			header('Content-Type: application/json');
		}
		
		echo json_encode($json, JSON_PRETTY_PRINT);

		if ($die)
		{
		    die(0);
		}
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
	    self::topResponse()->addField(GDT_Error::with($key, $args));
	}
	
	/**
	 * Redirect and show a message at the new page.
	 * @param string $key
	 * @param array $args
	 * @param string $url
	 * @param number $time
	 * @return \GDO\Core\GDT_Response
	 */
	public static function redirectMessage($key, array $args=null, $url, $time=0)
	{
	    self::topResponse()->addField(GDT_Success::with($key, $args));
	    if (!Application::instance()->isInstall())
	    {
    	    GDO_Session::set('redirect_message', t($key, $args));
    	    return self::redirect($url, $time);
	    }
	}
	
	####################
	### Top Response ###
	####################
	public static $TOP_RESPONSE;
	public static function topResponse()
	{
	    if (!self::$TOP_RESPONSE)
	    {
	        self::$TOP_RESPONSE = GDT_Response::make('topRespnse');
	        if (!Application::instance()->isInstall())
	        {
    	        if ($message = GDO_Session::get('redirect_message'))
    	        {
    	            GDO_Session::remove('redirect_message');
    	            self::$TOP_RESPONSE->addField(GDT_Success::withHTML($message));
    	        }
	        }
	    }
	    return self::$TOP_RESPONSE;
	}
	
	public static function renderTopResponse()
	{
	    echo self::topResponse()->render();
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

}
