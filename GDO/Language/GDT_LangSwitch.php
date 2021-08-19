<?php
namespace GDO\Language;

use GDO\Form\GDT_Select;
use GDO\Core\GDT_Template;
use GDO\Util\Strings;

/**
 * Displays a language switcher.
 * 
 * Themes have hard times here.
 * @author gizmore
 * @version 6.10
 * @since 6.00
 */
final class GDT_LangSwitch extends GDT_Select
{
    public function defaultName() { return '_lang'; }

    protected function __construct()
    {
        parent::__construct();
        $this->choices(Module_Language::instance()->cfgSupported());
    }

	public function renderCell()
	{
		return GDT_Template::php('Language', 'cell/langswitch.php',['field'=>$this]);
	}

	public function hrefLangSwitch(GDO_Language $language)
	{
	    $iso = $language->getISO();
	    $q = $_SERVER['QUERY_STRING'];
	    $c = 0;
	    $q = preg_replace('#_lang=[a-z]{2}#', '_lang='.$iso, $q, 1, $c);
	    if ($c == 0)
	    {
	        $q = $q ? ($q.'&_lang=' . $iso) : ('_lang=' .  $iso);
	    }
	    $u = $_SERVER['REQUEST_URI'];
	    $u = Strings::substrTo($u, '?', $u);
	    return $u . '?' . $q;
	}

}
