<?php
namespace GDO\DB;

use GDO\Core\GDT;

/**
 * Index db column definition.
 * The default algo is HASH. BTREE available
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.05
 */
class GDT_Index extends GDT
{
	use WithDatabase;
	
	###########
	### GDT ###
	###########
	public function isSerializable() { return false; }
	
	public function gdoColumnDefine()
	{
	    return "{$this->fulltextDefine()} INDEX({$this->indexColumns}) {$this->usingDefine()}";
	}
	
	private function fulltextDefine()
	{
	    return $this->indexFulltext ? $this->indexFulltext : '';
	}
	
	private function usingDefine()
	{
	    return $this->indexUsing === false ? '' : $this->indexUsing;
	}
	
	public function getGDOData()
	{
	    # no data
	}
	
	###############
	### Columns ###
	###############
	private $indexColumns;
	public function indexColumns(...$indexColumns)
	{
// 	    $this->indexColumns = implode(',', array_map(
// 	        ['GDO\Core\GDO', 'escapeIdentifierS'], $indexColumns));
	    $this->indexColumns = implode(',', $indexColumns);
	    return $this;
	}
	
	##################
	### Index Type ###
	##################
	const FULLTEXT = 'FULLTEXT';
	const HASH = 'USING HASH';
	const BTREE = 'USING BTREE';
	private $indexFulltext = false;
	private $indexUsing = self::HASH;
	public function hash() { $this->indexUsing = self::HASH; return $this; }
	public function btree() { $this->indexUsing = self::BTREE; return $this; }
	public function fulltext() { $this->indexFulltext = self::FULLTEXT; return $this; }
	
}
