<?php
namespace GDO\DB;

use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\GDOError;
use GDO\UI\WithLabel;
use GDO\Form\WithFormFields;
use GDO\Table\WithOrder;
use GDO\Util\Common;

/** 
 * Database capable base integer class.
 * 
 * Control ->bytes(4) for size.
 * Control ->unsigned(true) for unsigned.
 * Control ->min() and ->max() for validation.
 * Control ->step() for html5 fancy.
 * 
 * Is inherited by GDT_Object for auto_inc relation.
 * Can validate uniqueness.
 * Can compare gdo instances.
 * Is searchable and orderable.
 * Uses WithLabel, WithFormFields, WithDatabase and WithOrder.
 * 
 * @author gizmore
 * @version 6.11.2
 * @since 6.0.0
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
	
	public function isSerializable() { return true; }

	public function toValue($var)
	{
	    return (($var === null) ||
	    	    (trim($var, "\r\n\t ") === '')) ?
	    	null : (int) $var;
	}
	
	public $min;
	public $max;
	public $unsigned = false;
	public $bytes = 4;
	public $step = 1;
	public $filterable = true;
	public $searchable = true;
	public $orderable = true;
	public $orderDefaultAsc = true;
	public $editable = true;
	public $writable = true;
	public $focusable = true;
	
	public function step($step) { $this->step = $step; return $this; }
	public function bytes($bytes = 4) { $this->bytes = $bytes; return $this; }
	public function unsigned($unsigned=true) { $this->unsigned = $unsigned; return $this; }
	public function min($min) { $this->min = $min; return $this; }
	public function max($max) { $this->max = $max; return $this; }
	
	################
	### Validate ###
	################
	public function is_numeric($input)
	{
		return !!Common::regex('/^\\d+[.,]?\\d*$/iD', $input);
	}
	
	public function validate($value)
	{
		if (parent::validate($value))
		{
			if ($value !== null)
			{
// 				if (!$this->is_numeric($this->getRequestVar()))
// 				{
// 					return $this->numericError();
// 				}
				
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
			if ($this->gdo->isPersisted()) // persisted
			{ // ignore own row
				$condition .= " AND NOT ( " . $this->gdo->getPKWhere() . " )";
			}
			return $this->gdo->table()->select('1')->where($condition)->first()->exec()->fetchValue() !== '1';
		}
		return true;
	}
	
	private function numericError()
	{
		return $this->error('err_input_not_numeric');
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
	
	public function gdoExampleVars()
	{
	    if ( ($this->min !== null) && ($this->max !== null) )
	    {
	        if ($this->min === $this->max)
	        {
	            return $this->min;
	        }
	        else
	        {
	            return $this->min . '-' . $this->max;
	        }
	    }
	    if ($this->max !== null)
	    {
	        return '-∞-' . $this->max;
	    }
	    if ($this->min !== null)
	    {
	        return $this->min . '-∞';
	    }
	    return t('number');
	}
	
	
	##########
	### DB ###
	##########
	public function gdoColumnNames()
	{
		return [$this->name];
	}
	
	public function gdoColumnDefine()
	{
		$unsigned = $this->unsigned ? " UNSIGNED" : "";
		return "{$this->identifier()} {$this->gdoSizeDefine()}INT{$unsigned}{$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
	}
	
	protected function gdoSizeDefine()
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
		return GDT_Float::displayS($this->getVar(), 0);
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
	    $filter = $this->filterVar($rq);
	    if ($filter != '')
	    {
	        if ($condition = $this->searchQuery($query, $filter, true))
	        {
	            $this->filterQueryCondition($query, $condition);
	        }
	    }
	}
	
	public function filterGDO(GDO $gdo, $filtervalue)
	{
		$min = $filtervalue['min'];
		$max = $filtervalue['max'];
		$var = $this->getVar();
		if ( ($min !== null) && ($var < $min) )
		{
			return false;
		}
		if ( ($max !== null) && ($var > $max) )
		{
			return false;
		}
		return true;
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
