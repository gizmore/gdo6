<?php
namespace GDO\DB;
use GDO\Core\GDT;
use GDO\Core\GDO;

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
	public $virtual = false;
	public $searchable = true;
	
	############
	### Join ###
	############

	public $as;
	public $table;
	public $join;
	public function join(GDO $table, $as, $on, $type='LEFT')
	{
	    $this->as = $as;
	    $this->table = $table;
		$this->join = "{$type} JOIN {$table->gdoTableIdentifier()} AS {$as} ON {$on}";
		return $this;
	}
	
	public function joinRaw($join, $type='LEFT')
	{
	    $this->table = null;
	    $this->join = "{$type} JOIN $join";
	    return $this;
	}
	
	###################
	### Render stub ###
	###################
	public function renderForm() {}
	public function renderCell() {}
	
	public function searchQuery(Query $query, $searchTerm, $first)
	{
	    if ($this->table)
	    {
	        $conditions = [];
	        foreach ($this->table->gdoColumnsCache() as $gdt)
	        {
	            if ($gdt->searchable)
	            {
	                $conditions[] = $gdt->searchCondition($searchTerm, $this->as);
	            }
	        }
	        return implode(' OR ', $conditions);
	    }
	}
	
}
