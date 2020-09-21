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
	public function renderCell() { return Time::displayDate($this->getVar(), $this->format, '---'); }
	public function renderForm() { return GDT_Template::php('Date', 'form/datetime.php', ['field'=>$this]); }

	##############
	### Config ###
	##############
	public function configJSON()
	{
		return array(
			'dateStartView' => $this->dateStartView,
			'format' => $this->format,
			'minDate' => $this->minDate,
			'maxDate' => $this->maxDate,
		);
	}
	
}
