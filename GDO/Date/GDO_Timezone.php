<?php
namespace GDO\Date;

use GDO\Core\GDO;
use GDO\DB\GDT_AutoInc;
use GDO\DB\GDT_String;
use GDO\DB\GDT_Int;
use GDO\DB\GDT_Index;

/**
 * Timezone mapping.
 * 
 * @author gizmore
 * @version 6.11.0
 * @since 6.10.7
 */
final class GDO_Timezone extends GDO
{
	###########
	### GDO ###
	###########
	public function gdoColumns()
	{
		return [
			GDT_AutoInc::make('tz_id')->bytes(2),
			GDT_String::make('tz_name')->caseS()->ascii()->max(64)->unique()->notNull(),
			GDT_Int::make('tz_offset')->bytes(2)->notNull()->initial('0'),
			GDT_Index::make('tz_index_name')->indexColumns('tz_name')->btree(),
		];
	}
	
	###############
	### Getters ###
	###############
	public function getName() { return $this->getVar('tz_name'); }
	public function getOffset() { return $this->getVar('tz_offset'); }

	###############
	### Display ###
	###############
	public function displayName()
	{
		return $this->getName() . ' ' . $this->displayOffset();
	}
	
	public function displayOffset()
	{
		$o = $this->getOffset();
		$oo = abs($o);
		return sprintf('%s%02d%02d',
			$o >= 0 ? '+' : '-',
			$oo / 60, $oo % 60
		);
	}
	
	#######################
	### Timezone Object ###
	#######################
	/**
	 * @return \DateTimeZone
	 */
	public function getTimezone()
	{
		return Time::getTimezoneObject($this->getID());
	}

	#############
	### Cache ###
	#############
	public function allTimezones()
	{
		return array_values($this->allCached('tz_name', true));
	}
	
}
