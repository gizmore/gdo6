<?php
namespace GDO\DB;
/**
 * The auto inc column is unsigned and sets the primary key after insertions.
 *
 * @author gizmore
 * @since 5.0
 * @see GDT_CreatedAt
 * @see GDT_CreatedBy
 * @see GDT_EditedAt
 * @see GDT_EditedBy
 */
final class GDT_AutoInc extends GDT_UInt
{
	############
	### Base ###
	############
	public $writable = false;
	public $editable = false;
	public function defaultLabel() { return $this->label('id'); }
	
	##############
	### Column ###
	##############
	public function primary($primary=true) { return $this; } 
	public function isPrimary() { return true; } # Weird workaround for mysql primary key defs.
	public function gdoColumnDefine() { return "{$this->identifier()} INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY"; }
	public function validate($value) { return true; } # We simply do nothing in the almighty validate.
	
	##############
	### Events ###
	##############
	public function gdoAfterCreate()
	{
		if ($id = Database::$INSTANCE->insertId())
		{
			$this->gdo->setVar($this->name, $id, false);
		}
	}
}
