<?php
namespace GDO\DB;

use GDO\Core\GDT;
use GDO\UI\WithIcon;
use GDO\UI\WithLabel;
use GDO\Form\WithFormFields;
use GDO\Core\GDT_Template;
use GDO\Core\WithCompletion;
use GDO\UI\WithPHPJQuery;

/**
 * ENUMs are similiar to a select, but only allow 1 item being chosen.
 * For the database an enum column will be created.
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 5.0.0
 * 
 * @example GDT_Enum::make()->enumValues('one', 'two')->notNull()->initial('one')
 * 
 * @see GDT_Select
 */
class GDT_Enum extends GDT
{
	use WithIcon;
	use WithLabel;
	use WithDatabase;
	use WithFormFields;
	use WithCompletion;
	use WithPHPJQuery;
	
	public $orderable = true;
	public $filterable = true;
	public $readable = true;
	public $editable = true;
	public $writable = true;
	public $focusable = true;
	
	public function isSerializable() { return true; }
	
	############
	### Base ###
	############
	public function gdoColumnDefine()
	{
		$values = implode(',', array_map(array('GDO\Core\GDO', 'quoteS'), $this->enumValues));
		return "{$this->identifier()} ENUM ($values) CHARSET ascii COLLATE ascii_bin {$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
	}
	
	public function renderForm()
	{
	    if ($this->completionHref)
	    {
	        return GDT_Template::php('DB', 'form/object_completion.php', ['field' => $this]);
	    }
	    return GDT_Template::php('DB', 'form/enum.php', ['field' => $this]);
	
	}
	public function renderCell()
	{
	    return $this->enumLabel($this->getVar());
	}
	
	public function renderCLI()
	{
	    $back = $this->displayLabel();
	    $cell = $this->renderCell();
	    return $back ? "{$back}: {$cell}" : $cell;
	}
	
	public function toValue($var)
	{
		return $var === $this->emptyValue ? null : $var;
	}
	
	public function displayValue($var)
	{
	    return $this->enumLabel($var);
	}
	
	public function gdoExampleVars()
	{
	    $vars = array_slice($this->enumValues, 0, 3);
	    $vars = array_map(function($enumValue) {
	        return $this->displayValue($enumValue);
	    }, $vars);
	    if (count($this->enumValues) > 3)
	    {
	        $vars[] = $this->gdoHumanName() . '...';
	    }
	    return implode('|', $vars);
	}
	
	############
	### Enum ###
	############
	public $enumValues;
	public function enumLabel($enumValue=null)
	{
	    return $enumValue === null ? 
	       t($this->emptyLabel, $this->emptyLabelArgs) :
	       t("enum_$enumValue");
	}
	
	public function enumValues(...$enumValues)
	{
	    $this->enumValues = $enumValues;
	    return $this;
	}
	
	public function enumIndex()
	{
	    return $this->enumIndexFor($this->getVar());
	}
	
	public function enumIndexFor($enumValue)
	{
	    $index = array_search($enumValue, $this->enumValues, true);
	    return $index === false ? 0 : $index + 1;
	}
	
	public function enumForId($index)
	{
	    return $index > 0 ?
	       $this->enumValues[$index-1] : null;
	}
	
	public function htmlSelected($enumValue)
	{
	    return $this->getVar() === ((string)$enumValue) ?
	       ' selected="selected"' : '';
	}

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
	public function renderFilter($f)
	{
	    return GDT_Template::php('DB', 'filter/enum.php', [
	        'field' => $this, 'f' => $f]);
	}
	
	/**
	 * Filter value is an array.
	 */
	public function filterVar($rq=null)
	{
		if ($filter = parent::filterVar($rq))
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
	public function filterQuery(Query $query, $rq=null)
	{
		$filter = $this->filterVar($rq);
		$filter = array_filter($filter, function($f) { return !!$f; });
		if ($filter)
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
	private $enumLabels;
	private function generateEnumLabels()
	{
	    if ($this->enumLabels === null)
	    {
    	    $labels = [];
    	    foreach ($this->enumValues as $enum)
    	    {
    	        $labels[] = $this->enumLabel($enum);
    	    }
    	    $this->enumLabels = $labels;
	    }
	    return $this->enumLabels;
	}
	
	public function configJSON()
	{
	    if ($this->completionHref)
	    {
	        if ($value = $this->getValue())
	        {
	            $selected = array(
	                'id' => $this->getVar(),
	                'text' => $this->getVar(),
	                'display' => $value,
	            );
	        }
	        else
	        {
	            $selected = array(
    	            'id' => $this->emptyValue,
	                'text' => $this->displayEmptyLabel(),
    	            'display' => $this->displayEmptyLabel(),
	            );
	        }
    		return array_merge(parent::configJSON(), array(
    			'emptyValue' => $this->emptyValue,
    			'emptyLabel' => $this->displayEmptyLabel(),
    		    'completionHref' => $this->completionHref,
    		    'display' => $this->renderCell(),
    		    'selected' => $selected,
    		));
	    }
	    else
	    {
	        return array_merge(parent::configJSON(), [
	            'enumValues' => $this->enumValues,
	            'enumLabels' => $this->generateEnumLabels(),
	            'emptyValue' => $this->emptyValue,
	            'emptyLabel' => $this->displayEmptyLabel(),
	            'completionHref' => $this->completionHref,
	            'display' => $this->renderCell(),
	        ]);
	    }
	}
  
}
