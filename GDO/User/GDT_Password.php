<?php
namespace GDO\User;
use GDO\Core\GDT_Template;
use GDO\Util\BCrypt;
use GDO\DB\GDT_String;
/**
 * Bcrypt hash form and database value.
 * @author gizmore
 * @since 5.0
 */
class GDT_Password extends GDT_String
{
	public function __construct()
	{
		$this->min = 59;
		$this->max = 60;
		$this->encoding = self::ASCII;
		$this->caseSensitive = true;
	}

	public function defaultLabel() { return $this->label('password'); }
	
	public function toValue($var)
	{
		return $var === null ? null : new BCrypt($var);
	}
	
	public function getGDOData()
	{
	    $pass = $this->toValue($this->var);
	    return [$this->name => $pass ? $pass->__toString() : null];
	}
	
	public function renderForm()
	{
		return GDT_Template::php('User', 'form/password.php', ['field'=>$this]);
	}
	
	public function validate($value)
	{
	    if ( ($value === null) && ($this->notNull) )
		{
			return $this->errorNotNull();
		}
		elseif (mb_strlen($value) < 4)
		{
			return $this->error('err_pass_too_short', [4]);
		}
		return true;
	}
}
