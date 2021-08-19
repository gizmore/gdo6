<?php
namespace GDO\DB;

use GDO\Util\Random;
use GDO\Core\GDO;

/**
 * Default random token is 16 chars alphanumeric.
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 4.0.0
 */
class GDT_Token extends GDT_Char
{
	public function defaultName() { return 'token'; }
	public function defaultLabel() { return $this->label('token'); }

	protected function __construct()
	{
	    parent::__construct();
	    $this->length(GDO::TOKEN_LENGTH);
	}

	public function length($size)
	{
		$this->pattern = '/^[a-zA-Z0-9]{'.$size.'}$/D';
		return parent::length($size);
	}

	public $initialNull = false;
	public function initialNull($initialNull=true)
	{
		$this->initialNull = $initialNull;
		return $this;
	}

	public function blankData()
	{
		return [
		    $this->name => $this->initialNull ? 
		        null : Random::randomKey($this->max)];
	}

}
