<?php
namespace GDO\DB;

use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\GDOError;
use GDO\UI\WithLabel;
use GDO\Util\Strings;
use GDO\Form\WithFormFields;
use GDO\Table\WithOrder;

/** 
 * Database capable base integer class.
 * Control ->bytes(4) for size.
 * Control ->unsigned(true) for unsigned.
 * Control ->min() and ->max() for validation.
 * Control ->step() for html5 fancy.
 * Is inherited by GDT_Object for auto_inc relation.
 * Can validate uniqueness.
 * Can compare gdo instances.
 * Is searchable and orderable.
 * Uses WithLabel, WithFormFields, WithDatabase and WithOrder.
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.00
 * 
 * @see GDT_UInt
 * @see GDT_Decimal
 * @see GDT_Object
 */
class GDT_Int extends GDT
{
	use WithLabel;
	use WithFormFields;
	use WithDatabase;
	use WithOrder;

	public function toValue($var) { return $var === null || trim($var) === '' ? null : (int) $var; }
	
	public $min;
	public $max;
	public $unsigned = false;
	public $bytes = 4;
	public $step = 1;
	public $filterable = true;
	public $searchable = true;
	public $orderable = true;
	public $orderDefaultAsc = true;
	
	public function step($step) { $this->step = $step; return $this; }
	public function bytes($bytes = 4) { $this->bytes = $bytes; return $this; }
	public function unsigned($unsigned=true) { $this->unsigned = $unsigned; return $this; }
	public function min($min) { $this->min = $min; return $this; }
	public function max($max) { $this->max = $max; return $this; }
	
	################
	### Validate ###
	################
	public function validate($value)
	{
		if (parent::validate($value))
		{
			if ($value !== null)
			{
				if ( (($this->min !== null) && ($value < $this->min)) ||
					 (($this->max !== null) && ($value > $this->max)) )
				{
					return $this->intError();
				}
				if (!$this->validateUnique($value))
				{
					return $this->error('err_db_unique');
				}
			}
			return true;
		}
	}
	
	protected function validateUnique($value)
	{
		if ($this->unique)
		{
			$condition = "{$this->identifier()}=".GDO::quoteS($value);
			if ($this->gdo->getID()) // persisted
			{ // ignore own row
				$condition .= " AND NOT ( " . $this->gdo->getPKWhere() . " )";
			}
			return $this->gdo->table()->select('1')->where($condition)->first()->exec()->fetchValue() !== '1';
		}
		return true;
	}
	
	/**
	 * Appropiate min / max validation.
	 * @return boolean
	 */
	private function intError()
	{
		if (($this->min !== null) && ($this->max !== null))
		{
			return $this->error('err_int_not_between', [$this->min, $this->max]);
		}
		if ($this->min !== null)
		{
			return $this->error('err_int_too_small', [$this->min]);
		}
		if ($this->max !== null)
		{
			return $this->error('err_int_too_large', [$this->max]);
		}
	}
	
	public function plugVar()
	{
	    return "4";
	}
	
	##########
	### DB ###
	##########
	public function gdoColumnDefine()
	{
		$unsigned = $this->unsigned ? " UNSIGNED" : "";
		return "{$this->identifier()} {$this->gdoSizeDefine()}INT{$unsigned}{$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
	}
	
	private function gdoSizeDefine()
	{
		switch ($this->bytes)
		{
			case 1: return "TINY";
			case 2: return "MEDIUM";
			case 4: return "";
			case 8: return "BIG";
			default: throw new GDOError('err_int_bytes_length', [$this->bytes]);
		}
	}
	
	##############
	### Render ###
	##############
	public function htmlClass()
	{
	    return sprintf(' gdt-num %s', parent::htmlClass());
	}
	
	public function renderForm()
	{
		return GDT_Template::php('DB', 'form/integer.php', ['field'=>$this]);
	}
	
	public function renderCell()
	{
		return $this->getVar();
	}
	
	##############
	### Filter ###
	##############
	public function renderFilter($f)
	{
		return GDT_Template::php('DB', 'filter/int.php', ['field' => $this, 'f' => $f]);
	}
	
	public function filterQuery(Query $query, $rq=null)
	{
		if ($filter = $this->filterVar($rq))
		{
		    if ($filter > 0) # 0.0 is not filtered?
		    {
    			$min = (int)Strings::substrTo($filter, '-', $filter);
    			$max = (int)Strings::substrFrom($filter, '-', $filter);
    			$nam = $this->identifier();
    			$this->filterQueryCondition($query, "$nam >= $min AND $nam <= $max");
		    }
		}
	}
	
	public function filterGDO(GDO $gdo, $filtervalue)
	{
	    $min = Strings::substrTo($filtervalue, '-', $filtervalue);
	    $max = Strings::substrFrom($filtervalue, '-', $filtervalue);
		$var = $this->getVar();
		return ($var >= $min) && ($var <= $max);
	}
	
	public function gdoCompare(GDO $a, GDO $b)
	{
		$va = $a->getVar($this->name);
		$vb = $b->getVar($this->name);
		return $va - $vb;
	}
	
	##############
	### Search ###
	##############
	public function searchQuery(Query $query, $searchTerm, $first)
	{
	    return $this->searchCondition($searchTerm);
	}
	
	public function searchGDO($searchTerm)
	{
	    $haystack = (string) $this->getVar();
	    return strpos($haystack, $searchTerm) !== false;
	}
	
	/**
	 * Build a search condition.
	 * @param string $searchTerm
	 */
	public function searchCondition($searchTerm, $fkTable=null)
	{
	    $nameI = GDO::escapeIdentifierS($this->searchField ? $this->searchField : $this->name);
	    $searchTerm = GDO::escapeSearchS($searchTerm);
	    return sprintf('%s.%s LIKE \'%%%s%%\'',
	        $fkTable ? $fkTable : $this->gdtTable->gdoTableName() , $nameI, $searchTerm);
	}
	
	##############
	### Config ###
	##############
	public function configJSON()
	{
		return array_merge(parent::configJSON(), [
			'min' => $this->min,
			'max' => $this->max,
			'unsigned' => $this->unsigned,
			'bytes' => $this->bytes,
			'step' => $this->step,
		]);
	}

}
