<?php
namespace GDO\DB;

use GDO\Core\GDO;

/**
 * Adds primary attribute.
 * Adds index attribute.
 * Adds virtual attribute.
 * Defaults getGDOData and setGDOData.
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.00
 * 
 * @see GDT
 */
trait WithDatabase
{
// 	public $notNull = false;
// 	public function notNull($notNull=true) { $this->notNull = $notNull; return $this; }
	
// 	public $unique = false;
	public function unique($unique=true) { $this->unique = $unique; return $this; }
	
//	 public $primary = false;
	public function primary($primary=true) { $this->primary = $primary; return $this; }
	public function isPrimary() { return $this->primary; }
	
// 	public $index = false;
// 	public function index() { $this->index = true; return $this; }
  
	public $virtual = false;
	public function virtual($virtual=true) { $this->virtual = $virtual; return $this; }
	
	public function filterQueryCondition(Query $query, $condition)
	{
		return $this->virtual ? $query->having($condition) : $query->where($condition);
	}
	
	public function renderHeader() { return $this->displayLabel(); }
	
	###########
	### GDO ###
	###########
	public function gdoColumnDefine() {}
	public function gdoNullDefine() { return $this->notNull ? ' NOT NULL' : ' NULL'; }
	public function gdoInitialDefine() { return isset($this->initial) ? (" DEFAULT ".GDO::quoteS($this->initial)) : ''; }
	public function identifier() { return $this->name; }
	public function blankData() { return [$this->name => $this->initial]; }
	public function getGDOData() { return [$this->name => $this->getVar()]; }
	
}
