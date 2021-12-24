<?php
namespace GDO\Country;

use GDO\Core\GDO;
use GDO\Core\GDT_Template;
use GDO\DB\GDT_Char;
use GDO\DB\GDT_Int;
use GDO\DB\GDT_String;

/**
 * Country table/entity.
 * @author gizmore
 * @version 6.10
 * @since 3.00
 */
final class GDO_Country extends GDO
{
    ###########
    ### GDO ###
    ###########
	public function gdoColumns()
	{
		return array(
			GDT_Char::make('c_iso')->label('id')->length(2)->ascii()->caseS()->primary(),
			GDT_Char::make('c_iso3')->length(3)->ascii()->caseS()->notNull(),
			GDT_String::make('c_phonecode')->min(2)->max(32),
			GDT_Int::make('c_population')->initial('0')->unsigned(),
		);
	}

	##############
	### Getter ###
	##############
	public function getID() { return $this->getISO(); }
	public function getIDFile() { $iso = strtolower($this->getISO()); return $iso === 'ad' ? 'axx' : $iso; }
	public function getISO() { return $this->getVar('c_iso'); }
	public function getISO3() { return $this->getVar('c_iso3'); }
	public function displayName() { return t('country_'.strtolower($this->getISO())); }
	public function displayEnglishName() { return ten('country_'.strtolower($this->getISO())); }
	
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
	
	###########
	### All ###
	###########
	/**
	 * @return self[]
	 */
	public function &allCached($order=null, $json=false)
	{
	    $all = parent::allCached($order, $json);
	    return $this->allSorted($all);
	}
	
	public function &all($order=null, $json=false)
	{
	    return $this->allSorted(parent::all($order, $json));
	}
	
	private function &allSorted(array &$all)
	{
	    uasort($all, function(GDO_Country $a, GDO_Country $b){
	        $ca = iconv('utf-8', 'ascii//TRANSLIT', $a->displayName());
	        $cb = iconv('utf-8', 'ascii//TRANSLIT', $b->displayName());
	        return strcasecmp($ca, $cb);
	    });
        return $all;
	}
	
	##############
	### Render ###
	##############
	public function renderFlag()
	{
		return GDT_Template::php('Country', 'cell/country.php', ['field' => GDT_Country::make()->gdo($this), 'choice' => false]);
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
