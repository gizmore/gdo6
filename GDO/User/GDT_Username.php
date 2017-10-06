<?php
namespace GDO\User;
use GDO\Core\GDT_Template;
use GDO\DB\GDT_String;
/**
 * Username field with optional ajax completion.
 * @author gizmore
 * @version 6.05
 * @since 5.00
 */
class GDT_Username extends GDT_String
{
	const LENGTH = 32;
	
	public $min = 2;
	public $max = 32;
	public $pattern = "/^[a-z][_0-9a-z]{1,31}$/i";

	public function defaultLabel() { return $this->label('username'); }
	
	##################
	### Completion ###
	##################
	public $completion;
	public function completion()
	{
		$this->completion = true;
		return $this;
	}
	
	##############
	### Exists ###
	##############
	public $exists;
	public function exists()
	{
		$this->exists= true;
		return $this;
	}
	
	##############
	### Render ###
	##############
	public function render()
	{
		$tVars = array(
			'field' => $this,
		);
		return GDT_Template::php('User', 'form/username.php', $tVars);
	}
	
	public function renderCell()
	{
		return $this->gdo->displayNameLabel();
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
		
		# Check existance
		if ($this->exists)
		{
			if ($user = GDO_User::getByLogin($value))
			{
				$this->gdo = $user;
				return true;
			}
			else
			{
				return $this->error('err_user');
			}
		}
		# Check name pattern validity
		return parent::validate($value);
	}
}
