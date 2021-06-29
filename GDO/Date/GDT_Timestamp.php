<?php
namespace GDO\Date;

use GDO\Core\GDT_Template;
use GDO\Core\GDT;
use GDO\UI\WithLabel;
use GDO\Form\WithFormFields;
use GDO\DB\WithDatabase;
use GDO\Table\WithOrder;
use GDO\Core\Application;

/**
 * The GDT_Timestamp field is the baseclass for all datefields.
 * The var type is a mysql date.
 * The value type is an integer/timestamp.
 *  
 * @author gizmore
 * @version 6.07
 */
class GDT_Timestamp extends GDT
{
	use WithLabel;
	use WithFormFields;
	use WithDatabase;
	use WithOrder;
	
	public $icon = 'time';

	public $orderable = true;
	public $filterable = true;
	public $searchable = false;
	public $readable = true;
	public $editable = true;
	public $writable = true;
	
	public function isSerializable() { return true; }
	
	#############
	### Value ###
	#############
	public function toValue($var)
	{
		return $var === null ? null : Time::getTimestamp($var);
	}
	
	public function toVar($value)
	{
		return $value === null ? null : Time::getDate($value);
	}
	
	public function getVar()
	{
		$var = trim(parent::getVar());
		return $var ? $var : null;
	}
	
	public function initialSnap($mod)
	{
		$time = Application::$TIME;
		$time = $time - ($time % $mod) + $mod;
		return $this->initialValue($time);
	}
	
	public function inputToVar($input)
	{
	    return $input;
	}

	#####################
	### Starting view ###
	#####################
	public $dateStartView = 'month';
	public function startWithYear()
	{
		$this->dateStartView  = 'year';
		return $this;
	}
	public function startWithMonth()
	{
		$this->dateStartView  = 'month';
		return $this;
	}
	
	##############
	### Format ###
	##############
	public $format = Time::FMT_SHORT;
	public function format($key)
	{
		$this->format = $key;
		return $this;
	}
	
	###############
	### Min/Max ###
	###############
	/**
	 * @param int $duration
	 * @return \GDO\Date\GDT_Timestamp
	 */
	public function minAge($duration) { return $this->minTimestamp(Application::$TIME - $duration); }
	public function maxAge($duration) { return $this->maxTimestamp(Application::$TIME + $duration); }
	
	public $minDate;
	public function minTimestamp($minTimestamp)
	{
		return $this->minDate(Time::getDate($minTimestamp));
	}
	public function minDate($minDate)
	{
		$this->minDate = $minDate;		
		return $this;
	}
	
	public $maxDate;
	public function maxTimestamp($maxTimestamp)
	{
		return $this->maxDate(Time::getDate($maxTimestamp));
	}
	public function maxDate($maxDate)
	{
		$this->maxDate = $maxDate;
		return $this;
	}

	################
	### Validate ###
	################
	public function validate($value)
	{
		if ( ($value === null) && (!$this->notNull) )
		{
			return true;
		}
		if ( ($this->minDate !== null) && ($value < Time::getTimestamp($this->minDate)) )
		{
			return $this->error('err_min_date', [Time::displayDate($this->minDate, 'short')]);
		}
		if ( ($this->maxDate !== null) && ($value > Time::getTimestamp($this->maxDate)) )
		{
			return $this->error('err_max_date', [Time::displayDate($this->maxDate, 'short')]);
		}
		return parent::validate($value);
	}
	
	##############
	### Column ###
	##############
	public function gdoColumnDefine()
	{
		return "{$this->identifier()} TIMESTAMP{$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
	}
	
	##############
	### Render ###
	##############
	public function displayVar() { return Time::displayDate($this->getVar(), $this->format); }
	public function renderCell() { return $this->renderCellSpan(Time::displayDate($this->getVar(), $this->format, '---')); }
	public function renderForm() { return GDT_Template::php('Date', 'form/datetime.php', ['field'=>$this]); }
	public function renderAge() { return Time::displayAge($this->getVar()); }
	public function renderCLI() { return $this->displayLabel() . ': ' . $this->displayVar(); }
	public function renderJSON() { return $this->displayVar(); }
	
	##############
	### Config ###
	##############
	public function configJSON()
	{
		return array_merge(parent::configJSON(), [
			'dateStartView' => $this->dateStartView,
			'format' => $this->format,
			'minDate' => $this->minDate,
			'maxDate' => $this->maxDate,
		]);
	}
	
}
