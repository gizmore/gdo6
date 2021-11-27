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

	#######################
	### Timezone Object ###
	#######################
	/**
	 * @var \DateTimeZone
	 */
	private $timezone = null;
	
	/**
	 * @return \DateTimeZone
	 */
	public function getTimezone()
	{
		if ($this->timezone === null)
		{
			$this->timezone = new \DateTimeZone($this->getName());
		}
		return $this->timezone;
	}

}
