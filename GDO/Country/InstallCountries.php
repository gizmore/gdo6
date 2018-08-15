<?php
namespace GDO\Country;
final class InstallCountries
{
	public static function install()
	{
		$module = Module_Country::instance();
		$path = $module->filePath('data/countries.csv');
		
		$bulkData = [];
		
		if ($fh = fopen($path, 'r'))
		{
			# Build csv index names from header row
			$headers = fgetcsv($fh, null, ";");
			$cca2 = array_search('cca2', $headers);
			$cca3 = array_search('cca3', $headers);
			$phone = array_search('callingCode', $headers);
			# Loop
			while ($row = fgetcsv($fh, null, ";"))
			{
				if (count($row) > 3)
				{
					$bulkData[] = array(
						strtolower($row[$cca2]),
						strtolower($row[$cca3]),
						$row[$phone],
						null,
					);
				}
			}
			
			fclose($fh);
		}
	
		$c = GDO_Country::table();
		$fields = array(
			$c->gdoColumn('c_iso'),
			$c->gdoColumn('c_iso3'),
			$c->gdoColumn('c_phonecode'),
			$c->gdoColumn('c_population'),
			
		);
		GDO_Country::bulkReplace($fields, $bulkData);
	}
}