<?php
namespace GDO\DB;

use GDO\Core\GDT;
use GDO\UI\WithIcon;
use GDO\UI\WithLabel;
use GDO\Form\WithFormFields;
use GDO\Core\GDT_Template;

/**
 * ENUMs are similiar to a select, but only allow 1 item being chosen.
 * For the database an enum column will be created.
 * 
 * @example GDT_Enum::make()->enumValues('one', 'two')->notNull()->initial('one')
 * 
 * @see GDT_Select
 * 
 * @author gizmore
 * @since 5.00
 * @version 6.05
 */
class GDT_Enum extends GDT
{
	use WithIcon;
	use WithLabel;
	use WithDatabase;
	use WithFormFields;
	
	public $filterable = true;
	public $orderableField = true;

	############
	### Base ###
	############
	public function gdoColumnDefine()
	{
		$values = implode(',', array_map(array('GDO\Core\GDO', 'quoteS'), $this->enumValues));
		return "{$this->identifier()} ENUM ($values) CHARSET ascii COLLATE ascii_bin {$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
	}
	
	public function renderForm() { return GDT_Template::php('DB', 'form/enum.php', ['field' => $this]); }
	public function renderCell() { return $this->enumLabel($this->getVar()); }
	public function renderJSON()
	{
		return array(
			'enumValues' => $this->enumValues,
			'selected' => $this->getVar(),
			'error' => $this->error,
		);
	}
	
	public function toValue($var)
	{
		return $var === $this->emptyValue ? null : $var;
	}
	
	############
	### Enum ###
	############
	public $enumValues;
	public function enumLabel($enumValue=null) { return $enumValue === null ? t($this->emptyLabel, $this->emptyLabelArgs) : t("enum_$enumValue"); }
	public function enumValues(...$enumValues) { $this->enumValues = $enumValues; return $this; }
	public function enumIndex() { return $this->enumIndexFor($this->getVar()); }
	public function enumIndexFor($enumValue) { $index = array_search($enumValue, $this->enumValues, true); return $index === false ? 0 : $index + 1; }
	public function enumForId($index) { return $index > 0 ? $this->enumValues[$index-1] : null; }
	public function htmlSelected($enumValue) { return $this->getVar() === ((string)$enumValue) ? ' selected="selected"' : ''; }

	#############
	### Empty ###
	#############
	public $emptyValue = '0';
	public function emptyValue($emptyValue)
	{
		$this->emptyValue = $emptyValue;
		return $this->emptyLabel('please_choice');
	}
	public $emptyLabel;
	public $emptyLabelArgs;
	public function emptyLabel($emptyLabel, $args=null)
	{
		$this->emptyLabel = $emptyLabel;
		$this->emptyLabelArgs = $args;
		return $this;
	}
	public function displayEmptyLabel()
	{
		return t($this->emptyLabel, $this->emptyLabelArgs);
	}
	
	##############
	### Filter ###
	##############
	/**
	 * Render select filter header.
	 */
	public function renderFilter() { return GDT_Template::php('DB', 'filter/enum.php', ['field' => $this]); }
	
	/**
	 * Filter value is an array.
	 */
	public function filterValue()
	{
		if ($filter = parent::filterValue())
		{
			if ($filter = is_array($filter) ? $filter : json_decode($filter))
			{
				return $filter;
			}
		}
		return [];
	}
	
	/**
	 * Add where clause to query on filter.
	 */
	public function filterQuery(Query $query)
	{
		$filter = $this->filterValue();
		if (!empty($filter))
		{
			$filter = array_map(['GDO\\Core\\GDO', 'escapeS'], $filter);
			$condition = sprintf('%s IN ("%s")', $this->identifier(), implode('","', $filter));
			$this->filterQueryCondition($query, $condition);
		}
	}
	
	################
	### Validate ###
	################
	public function validate($value)
	{
		if (parent::validate($value))
		{
			if ($value !== null)
			{
				if (!in_array($value, $this->enumValues, true))
				{
					return $this->error('err_invalid_choice');
				}
			}
			return true;
		}
	}
	
	##############
	### Config ###
	##############
	public function configJSON()
	{
		return array(
			'enumValues' => $this->enumValues,
			'emptyLabel' => $this->emptyLabel,
			'emptyValue' => $this->emptyValue,
			'value' => $this->var,
		);
	}
  
}
