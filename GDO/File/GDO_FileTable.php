<?php
namespace GDO\File;

use GDO\Core\GDO;
use GDO\DB\GDT_AutoInc;
use GDO\DB\GDT_Object;
use GDO\DB\GDT_CreatedBy;
use GDO\DB\GDT_CreatedAt;

class GDO_FileTable extends GDO
{
	/**
	 * @return GDO
	 */
	public function gdoFileObjectTable() {}
	public function gdoCached() { return false; }
	public function gdoAbstract() { return $this->gdoFileObjectTable() === null; }
	
	public function gdoColumns()
	{
		return array(
			GDT_AutoInc::make('files_id'),
			GDT_Object::make('files_object')->table($this->gdoFileObjectTable())->notNull(),
			GDT_File::make('files_file')->notNull(),
			GDT_CreatedBy::make('files_creator'),
			GDT_CreatedAt::make('files_created'),
		);
	}
	
	public function getFile() { return $this->getValue('files_file'); }

}
