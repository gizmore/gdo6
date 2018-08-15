<?php
namespace GDO\DB;
use GDO\Core\GDT;

/**
 * Can be used with $query->joinObject('col_name') to add a predefined join to a query.
 * @author gizmore
 * @see GDT_Object
 * @since 6.0.1
 */
final class GDT_Join extends GDT
{
	# Ducktype database related fields
	public $unique = false;
	public $primary = false;
	
	############
	### Join ###
	############
	public $join;
	public function join($join, $type='LEFT')
	{
		$this->join = "$type JOIN $join";
		return $this;
	}
	
	###################
	### Render stub ###
	###################
	public function renderForm() {}
	public function renderCell() {}
}
