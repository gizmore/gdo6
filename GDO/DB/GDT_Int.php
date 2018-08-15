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
use GDO\UI\WithTooltip;
use GDO\UI\WithIcon;
class GDT_Int extends GDT
{
	use WithLabel;
	use WithTooltip;
	use WithFormFields;
	use WithDatabase;
	use WithOrder;

	public function toValue($var) { return $var === null ? null : (int) $var; }
//	 public function setGDOData(GDO $gdo=null) { return $gdo ? $this->val($gdo->getVar($this->name)) : $this; }
	
	public $min = null;
	public $max = null;
	public $unsigned = false;
	public $bytes = 4;
	public $step = 1;
	
	public function step($step) { $this->step = $step; return $this; }
	public function bytes($bytes = 4) { $this->bytes = $bytes; return $this; }
	public function unsigned($unsigned=true) { $this->unsigned = $unsigned; return $this; }
	public function min($min) { $this->min = $min; return $this; }
	public function max($max) { $this->max = $max; return $this; }
	
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
			if ($this->gdo->getID())
			{
				foreach ($this->gdo->table()->gdoPrimaryKeyColumns() as $gdoType)
				{
					$condition .= " AND NOT ( " . $this->gdo->getPKWhere() . " )";
				}
			}
			return $this->gdo->table()->select('COUNT(*)')->where($condition)->first()->exec()->fetchValue() === '0';
		}
		return true;
		
	}
	
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
	
	public function renderForm()
	{
		return GDT_Template::php('DB', 'form/integer.php', ['field'=>$this]);
	}
	
	public function renderCell()
	{
		return $this->getVar();
	}
	
	public function renderJSON()
	{
		return array(
			'min' => $this->min,
			'max' => $this->max,
			'step' => $this->step,
			'bytes' => $this->bytes,
			'unsigned' => $this->unsigned,
			'error' => $this->error,
		);
	}
	
	public function renderFilter()
	{
		return GDT_Template::php('DB', 'filter/int.php', ['field'=>$this]);
	}
	
	public function filterQuery(Query $query)
	{
		if ($filter = $this->filterValue())
		{
			$min = (int)Strings::substrTo($filter, '-', $filter);
			$max = (int)Strings::substrFrom($filter, '-', $filter);
			$nam = $this->identifier();
			$this->filterQueryCondition($query, "$nam >= $min AND $nam <= $max");
		}
	}
	
	public function filterGDO(GDO $gdo)
	{
		if ('' !== ($filter = (string)$this->filterValue()))
		{
			$min = Strings::substrTo($filter, '-', $filter);
			$max = Strings::substrFrom($filter, '-', $filter);
			$var = $this->getVar();
			return ($var < $min) || ($var > $max);
		}
	}
	
	public function gdoCompare(GDO $a, GDO $b)
	{
		$va = $a->getVar($this->name);
		$vb = $b->getVar($this->name);
		return $va - $vb;
	}
	
	public function htmlClass()
	{
		return sprintf(' gdt-num %s', parent::htmlClass());
	}

	##############
	### Config ###
	##############
	public function configJSON()
	{
		return array(
			'min' => $this->min,
			'max' => $this->max,
			'unsigned' => $this->unsigned,
			'bytes' => $this->bytes,
			'step' => $this->step,
			'value' => $this->var,
		);
	}

}
