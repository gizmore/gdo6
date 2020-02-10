<?php
namespace GDO\Language\Method;
use GDO\Language\GDO_Language;
use GDO\Language\GDT_Language;
use GDO\Core\MethodAjax;

final class Completion extends MethodAjax
{
	public function execute()
	{
		$response = [];
		$q = $this->getSearchTerm();
		$cell = GDT_Language::make('lang_iso');
		foreach (GDO_Language::table()->all() as $iso => $language)
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
