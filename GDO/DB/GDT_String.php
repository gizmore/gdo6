<?php
namespace GDO\DB;

use GDO\Table\WithOrder;
use GDO\Core\GDT_Template;
use GDO\UI\WithLabel;
use GDO\Form\WithFormFields;
use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\UI\WithPHPJQuery;

/**
 * Basic String type with database support.
 * Base class for further textual data like a text(area) or message.
 * Used "With" Traits: label, formfields, tooltip, order, database and phpjquery.
 * Provided Variables: encoding, pattern, caseSensitive, min, max
 * 
 * @author gizmore
 * @version 6.08
 * @since 6.00
 */
class GDT_String extends GDT
{
	use WithLabel;
	use WithFormFields;
	use WithOrder;
	use WithDatabase;
	use WithPHPJQuery;
	
	const UTF8 = 1;
	const ASCII = 2;
	const BINARY = 3;
	
	public $pattern;
	public $encoding = self::UTF8;
	public $caseSensitive = false;
	
	public $min = 0;
	public $max = 255;
	
	public $_inputType = 'text';
	
	public $filterable = true;
	public $searchable = true;
	public $orderable = true;
	
	public function utf8() { return $this->encoding(self::UTF8); }
	public function ascii() { return $this->encoding(self::ASCII); }
	public function binary() { return $this->encoding(self::BINARY); }
	public function isBinary() { return $this->encoding === self::BINARY; }
	
	public function encoding($encoding) { $this->encoding = $encoding; return $this; }
	
	public function pattern($pattern) { $this->pattern = $pattern; return $this; }
	public function htmlPattern() { return $this->pattern ? " pattern=\"{$this->htmlPatternValue()}\"" : ''; }
	public function htmlPatternValue() { return trim(rtrim($this->pattern, 'iuD'), $this->pattern[0].'^$'); }
	public function caseI($caseInsensitive=true) { return $this->caseS(!$caseInsensitive); }
	public function caseS($caseSensitive=true) { $this->caseSensitive = $caseSensitive; return $this; }
	
	public function min($min) { $this->min = $min; return $this; }
	public function max($max) { $this->max = $max; return $this; }
	
	public function getVar()
	{
		$var = trim(parent::getVar(), "\r\n\t ");
		return $var === '' ? null : $var;
	}
	
	######################
	### Table creation ###
	######################
	public function gdoColumnDefine()
	{
		$charset = $this->gdoCharsetDefine();
		$collate = $this->gdoCollateDefine($this->caseSensitive);
		return "{$this->identifier()} VARCHAR({$this->max}) CHARSET $charset $collate{$this->gdoNullDefine()}";
	}
	
	public function gdoCharsetDefine()
	{
		switch ($this->encoding)
		{
			case self::UTF8: return 'utf8mb4';
			case self::ASCII: return 'ascii';
			case self::BINARY: return 'binary';
		}
	}
	
	public function gdoCollateDefine($caseSensitive)
	{
		if ($this->isBinary())
		{
			return '';
		}
		$append = $caseSensitive ? '_bin' : '_general_ci';
		return 'COLLATE ' . $this->gdoCharsetDefine() . $append;
	}
	
	##############
	### Render ###
	##############
	public function renderCell() { return html($this->getVar()); }
	public function renderForm() { return GDT_Template::php('DB', 'form/string.php', ['field' => $this]); }

	################
	### Validate ###
	################
	public function validate($value)
	{
		if (parent::validate($value))
		{
			if ($value !== null)
			{
				$len = mb_strlen($value);
				if ( ($this->min !== null) && ($len < $this->min) )
				{
					return $this->strlenError();
				}
				if ( ($this->max !== null) && ($len > $this->max) )
				{
					return $this->strlenError();
				}
				if ( ($this->pattern !== null) && (!preg_match($this->pattern, $value)) )
				{
					return $this->patternError();
				}
				if (!$this->validateUnique($value))
				{
					return $this->error('err_db_unique');
				}
			}
			return true;
		}
	}
	
	private function validateUnique($value)
	{
		if ($this->unique)
		{
			$condition = "{$this->identifier()}=".GDO::quoteS($value);
			if ($this->gdo && $this->gdo->getID())
			{
				$condition .= " AND NOT ( " . $this->gdo->getPKWhere() . " )";
			}
			if (!$this->gdtTable)
			{
			    $this->gdtTable = $this->gdo->table();
			}
		    if ($this->gdtTable)
		    {
		        return $this->gdtTable->select('COUNT(*)')->where($condition)->first()->exec()->fetchValue() === '0';
		    }
		}
		return true;
	}
	
	private function patternError()
	{
		return $this->error('err_string_pattern');
	}
	
	private function strlenError()
	{
		if ( ($this->max !== null) && ($this->min !== null) )
		{
			return $this->error('err_strlen_between', [$this->min, $this->max]);
		}
		elseif ($this->max !== null)
		{
			return $this->error('err_strlen_too_large', [$this->max]);
		}
		elseif ($this->min !== null)
		{
			return $this->error('err_strlen_too_small', [$this->min]);
		}
	}
	
	##############
	### filter ###
	##############
	public function renderFilter()
	{
		return GDT_Template::php('DB', 'filter/string.php', ['field'=>$this]);
	}
	
    public function searchCondition($searchTerm, $fkTable=null)
    {
        $collate = $this->caseSensitive ? (' '.$this->gdoCollateDefine(false)) : '';
        $condition = sprintf('%s%s%s LIKE \'%%%s%%\'', 
            $fkTable ? $fkTable.'.' : '', $this->identifier(), $collate, GDO::escapeSearchS($searchTerm));
        return $condition;
    }
	
	public function filterQuery(Query $query)
	{
		if ($filter = (string)$this->filterValue())
		{
		    $this->applyQueryFilter($query, $filter);
		}
	}
	
	private function applyQueryFilter(Query $query, $searchValue)
	{
	    $condition = $this->searchCondition($searchValue);
	    $this->filterQueryCondition($query, $condition);
	}
	
	public function filterGDO(GDO $gdo)
	{
		if ($filter = $this->filterValue())
		{
			$pattern = chr(1).preg_quote($filter, chr(1)).chr(1);
			if ($this->caseSensitive) { $pattern .= 'i'; } # Switch to case-i if necessary
			return !preg_match($pattern, $this->getVar());
		}
	}
	
	##############
	### Search ###
	##############
	public function searchQuery(Query $query, $searchTerm, $first)
	{
	    return $this->searchCondition($searchTerm);
	}
	
	##############
	### Config ###
	##############
	public function configJSON()
	{
		return array_merge(parent::configJSON(), array(
			'min' => $this->min,
			'max' => $this->max,
			'pattern' => $this->pattern,
			'encoding' => $this->encoding,
			'caseS' => $this->caseSensitive,
		));
	}

}
