<?php
namespace GDO\Language;

use GDO\Core\GDO;
use GDO\DB\GDT_Char;
use GDO\Core\GDT_Template;

/**
 * Language table.
 * 
 * @see self::allSupported()
 * 
 * @author gizmore
 * @version 6.10
 * @since 3.00
 */
final class GDO_Language extends GDO
{
	public static function iso() { return Trans::$ISO; }

	/**
	 * Wrap a callback in a temporary changed ISO. Esthetics.
	 * @param string $iso
	 * @param callable $callback
	 * @return mixed
	 */
	public static function withIso($iso, $callback)
	{
		$old = self::iso();
	    try 
	    {
    		Trans::setISO($iso);
    		return call_user_func($callback);
	    }
	    catch (\Throwable $t)
	    {
	        throw $t;
	    }
	    finally
	    {
    		Trans::setISO($old);
	    }
	}

	public function gdoColumns()
	{
		return [
		    GDT_Char::make('lang_iso')->primary()->ascii()->length(2),
		];
	}

	public function getID() { return $this->getISO(); }
	public function getISO() { return $this->getVar('lang_iso'); }
	public function displayName() { return t('lang_'.$this->getISO()); }
	public function displayNameISO($iso) { return tiso($iso, 'lang_'.$this->getISO()); }
	public function renderCell()
	{
		return GDT_Template::php('Language', 'cell/language.php', ['language'=>$this]);
	}
	public function renderChoice()
	{
		return GDT_Template::php('Language', 'choice/language.php', ['language'=>$this]);
	}

	/**
	 * Get a language by ISO or return a stub object with name "Unknown".
	 * @param string $iso
	 * @return self
	 */
	public static function getByISOOrUnknown($iso=null)
	{
		if ( ($iso === null) || (!($language = self::getById($iso))) )
		{
			$language = self::blank(['lang_iso'=>'zz']);
		}
		return $language;
	}

	/**
	 * @return self
	 */
	public static function current()
	{
		return self::getByISOOrUnknown(Trans::$ISO);
	}

	/**
	 * @return self[]
	 */
	public function allSupported()
	{
		return Module_Language::instance()->cfgSupported();
	}

}
