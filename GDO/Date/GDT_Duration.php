<?php
namespace GDO\Date;

use GDO\Core\GDT_Template;
use GDO\DB\GDT_String;

/**
 * Duration field int in seconds.
 * 
 * @author gizmore
 * @version 6.11.0
 * @since 6.0.0
 */
class GDT_Duration extends GDT_String
{
	public function defaultLabel() { return $this->label('duration'); }
	
	public $pattern = '/^(?:[0-9 ]+[smhdwy]? *)+$/iD';

	protected function __construct()
	{
	    parent::__construct();
		$this->icon('time');
		$this->ascii();
		$this->max(16);
	}
	
	public $minDuration = 0;
	public function minDuration($minDuration)
	{
		$this->minDuration = $minDuration;
		return $this;
	}
	
	public function toValue($var)
	{
	    return empty($var) ? null : Time::humanToSeconds($var);
	}
	
	public function toVar($value)
	{
	    return $value === null ? null : Time::humanDuration($value);
	}
	
	public function renderCell()
	{
		return $this->renderCellSpan($this->getVar());
	}
	
	public function renderForm()
	{
		return GDT_Template::php('Date', 'form/duration.php', ['field' => $this]);
	}
	
	public function validate($value)
	{
		if (!parent::validate($value))
		{
			return false;
		}
		if ($value < $this->minDuration)
		{
			return $this->error('err_min_duration', [$this->minDuration]);
		}
		return true;
	}
	
}
