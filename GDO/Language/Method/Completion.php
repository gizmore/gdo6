<?php
namespace GDO\Language\Method;
use GDO\Language\GDO_Language;
use GDO\Language\GDT_Language;
use GDO\Core\MethodCompletion;

final class Completion extends MethodCompletion
{
	public function execute()
	{
		$response = [];
		$q = $this->getSearchTerm();
		
		$table = GDO_Language::table();
		$languages = isset($_REQUEST['all']) ? $table->all() : $table->allSupported();
		
		$cell = GDT_Language::make('lang_iso');
		foreach ($languages as $iso => $language)
		{
			if ( ($q === '') || ($language->getISO() === $q) ||
				 (mb_stripos($language->displayName(), $q) !== false) ||
				 (mb_stripos($language->displayNameIso('en'), $q)!==false))
			{
				$response[] = array(
					'id' => $iso,
					'text' => $language->displayName(),
					'display' => $cell->gdo($language)->renderChoice($language),
				);
			}
		}
		die(json_encode($response));
	}
}
