<?php
namespace GDO\DB;

use GDO\Core\GDO;

/**
 * Trait for GDT that can make use of the database.
 * 
 * @author gizmore
 * @version 6.10.2
 * @since 6.0.0
 * 
 * @see GDT
 */
trait WithDatabase
{
	public function unique($unique=true) { $this->unique = $unique; return $this; }

	public function primary($primary=true) { $this->primary = $primary; return $this->notNull(); }
	public function isPrimary() { return $this->primary; }

	public $virtual = false;
	public function virtual($virtual=true) { $this->virtual = $virtual; return $this; }

	public function filterQueryCondition(Query $query, $condition)
	{
		return $this->virtual ? $query->having($condition) : $query->where($condition);
	}

	public function renderHeader()
	{
	    $tt = $this->iconText ? sprintf(' title="%s"', t($this->iconText, $this->iconTextArgs)) : '';
	    return sprintf('<label%s>%s</label>', $tt, $this->displayLabel());
	}

	###########
	### GDT ###
	###########
	public function gdoColumnDefine() {}
	public function gdoNullDefine() { return $this->notNull ? ' NOT NULL' : ' NULL'; }
	public function gdoInitialDefine() { return isset($this->initial) ? (" DEFAULT ".GDO::quoteS($this->initial)) : ''; }
	public function identifier() { return $this->name; }
	public function blankData() { return [$this->name => $this->var]; }

	public function getGDOData()
	{
	    $v = $this->getVar();
	    $v = $v === '' || $v === null ? null : $v;
	    return [$this->name => $v];
	}

}
