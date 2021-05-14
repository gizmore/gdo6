<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;
use GDO\DB\GDT_String;
use GDO\Net\URL;
use GDO\Core\GDO;

/**
 * An anchor for menus or paragraphs.
 * 
 * @author gizmore
 * @version 6.10.3
 * @since 6.0.0
 */
class GDT_Link extends GDT_String
{
	use WithIcon;
	use WithLabel;
	use WithHREF;
	use WithHTML;
	use WithPHPJQuery;
	use WithAnchorRelation;

	public $editable = false;
	public $orderable = false;
	public $filterable = false;
	public $searchable = false;
// 	public function isSerializable() { return false; }

	################
	### GDO href ###
	################
	public function gdo(GDO $gdo=null)
	{
	    $method = "href_{$this->name}";
	    if (method_exists($gdo, $method))
	    {
	        $this->href(call_user_func([$gdo, $method]));
	    }
	    return parent::gdo($gdo);
	}
	
	################
	### Relation ###
	################
	const REL_ALTERNATE = 'alternate';
	const REL_AUTHOR = 'author';
	const REL_BOOKMARK = 'bookmark';
	const REL_EXTERNAL = 'external';
	const REL_HELP = 'help';
	const REL_LICENSE = 'license';
	const REL_NEXT = 'next';
	const REL_NOFOLLOW = 'nofollow';
	const REL_NOREFERRER = 'noreferrer';
	const REL_NOOPENER = 'noopener';
	const REL_PREV = 'prev';
	const REL_SEARCH = 'search';
	const REL_TAG = 'tag';
	
	public $caseSensitive = true;
	
	/**
	 * Output a link / anchor.
	 * @deprecated not the default GDT behaviour. Yet ok?
	 * @param string $href
	 * @param string $label
	 * @return string
	 */
	public static function anchor($href, $label=null)
	{
		$label = $label !== null ? $label : $href;
		return self::make()->href($href)->labelRaw($label)->render();
	}
	
	public static function urlencodeSEO($url)
	{
	    $url = str_replace([' '], '_', $url);
	    return urlencode($url);
	}
	
	##############
	### Render ###
	##############
	public function renderForm() { return $this->renderCell(); }
	public function renderCard() { return $this->renderCell(); }
	public function renderCell() { return GDT_Template::php('UI', 'cell/link.php', ['link' => $this]); }
	public function renderJSON() { return trim($this->displayLabel() . " ( $this->href )"); }
	public function renderFilter($f) {}
	
	###################
	### Link target ###
	###################
	private $target;
	public function target($target) { $this->target = $target; return $this; }
	public function targetBlank() { return $this->target('_blank'); }
	public function htmlTarget() { return $this->target === null ? '' : " target=\"{$this->target}\""; }

	###########
	### URL ###
	###########
	public function getURL() { return new URL($this->href); }
	
}
