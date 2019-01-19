<?php
namespace GDO\Country\Method;
use GDO\Core\MethodCompletion;
use GDO\Country\GDO_Country;
use GDO\Country\GDT_Country;
/**
 * Autocomplete adapter for countries.
 * @author gizmore
 * @since 6.00
 * @version 6.05
 */
final class Completion extends MethodCompletion
{
	public function execute()
	{
		$response = [];
		$q = $this->getSearchTerm();
		$cell = GDT_Country::make('c_iso');
		foreach (GDO_Country::table()->all() as $iso => $country)
		{
			if ( (!$q) || ($country->getISO() === $q) ||
				(mb_stripos($country->displayName(), $q) !== false) )
			{
				$response[] = array(
					'id' => $iso,
					'text' => $country->displayName(),
					'display' => $cell->gdo($country)->renderCell(),
				);
			}
		}
		
		die(json_encode($response));
	}
}
