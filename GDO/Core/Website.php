<?php
namespace GDO\Core;
use GDO\UI\GDT_Link;
use GDO\UI\GDT_Panel;
/**
 * General Website utility.
 * @author gizmore
 */
final class Website
{
	private static $_links = [];
	private static $_inline_css = '';
	
	public static function redirectMessage($url, $time=12)
	{
		return self::redirect($url, $time);
	}
	
	public static function redirectBack($time=0)
	{
		$url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : GWF_WEB_ROOT;
		return self::redirect($url, $time);
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
					
					return GDT_Success::responseWith('msg_redirect', [GDT_Link::anchor($url), $time]);
				}
			case 'json': return ['redirect' => $url, 'redirectWait' => $time];
		}
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
		self::$_links[] = array(htmlspecialchars($href), $type, $rel);
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
		if((false === $overwrite) && (isset(self::$_meta[$metaA[0]]) === true))
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
		$mode = array('name', 'http-equiv');
		foreach (self::$_meta as $meta)
		{
			if (!is_array($meta))
			{
				continue; # TODO: spaceone fix.
			}
			list($name, $content, $equiv) = $meta;
			$equiv = $mode[$equiv];
			$back .= sprintf('<meta %s="%s" content="%s"%s', $equiv, $name, $content, self::$xhtml);
		}
		return $back;
	}
	
	public static function renderJSON($json)
	{
		if (!Application::instance()->isCLI())
		{
			header('Content-Type: application/json');
		}
		die(json_encode($json));
	}
}
