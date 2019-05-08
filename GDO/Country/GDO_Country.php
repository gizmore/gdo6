<?php
namespace GDO\Country;
use GDO\DB\Cache;
use GDO\Core\GDO;
use GDO\Core\GDT_Template;
use GDO\DB\GDT_Char;
use GDO\DB\GDT_Int;
use GDO\DB\GDT_String;
/**
 * Country table/entity.
 * @author gizmore
 */
final class GDO_Country extends GDO
{
	public function memCached() { return false; }
	
	public function gdoColumns()
	{
		return array(
			GDT_Char::make('c_iso')->label('id')->size(2)->ascii()->caseS()->primary(),
			GDT_Char::make('c_iso3')->size(3)->ascii()->caseS()->notNull()->index(),
			GDT_String::make('c_phonecode')->min(2)->max(32),
			GDT_Int::make('c_population')->initial('0')->unsigned(),
		);
	}
	
	public function getISO() { return $this->getVar('c_iso'); }
	public function getISO3() { return $this->getVar('c_iso3'); }
	public function displayName() { return t('country_'.$this->getISO()); }

	/**
	 * Get a country by ID or return a stub object with name "Unknown".
	 * @param int $id
	 * @return self
	 */
	public static function getByISOOrUnknown($iso=null)
	{
		
		if ( ($iso === null) || (!($country = self::getById($iso))) )
		{
			$country = self::unknownCountry();
		}
		return $country;
	}
	
	public static function unknownCountry()
	{
		return self::blank(['c_iso'=>'zz']);
	}
	
	/**
	 * @return self[]
	 */
	public function all()
	{
		if (false === ($cache = Cache::get('gdo_country')))
		{
			$cache = self::table()->select('*')->exec()->fetchAllArray2dObject();
			Cache::set('gdo_country', $cache);
		}
		return $cache;
	}
	
	public function renderFlag()
	{
		return GDT_Template::php('Country', 'cell/flag.php', ['field' => GDT_Country::make()->gdo($this), 'choice' => false]);
	}

	public function renderCell()
	{
		return GDT_Template::php('Country', 'cell/country.php', ['field' => GDT_Country::make()->gdo($this), 'choice' => false]);
	}

	public function renderChoice()
	{
		return GDT_Template::php('Country', 'cell/country.php', ['field' => GDT_Country::make()->gdo($this)->initial($this->getID()), 'choice' => true]);
	}
}
