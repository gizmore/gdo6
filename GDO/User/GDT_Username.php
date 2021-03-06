<?php
namespace GDO\User;

use GDO\Core\GDT_Template;
use GDO\DB\GDT_String;

/**
 * Username field with optional ajax completion.
 * @TODO autocomplete not implemented here.
 * @see GDT_User
 * 
 * @author gizmore
 * @version 6.10.1
 * @since 5.0.0
 */
class GDT_Username extends GDT_String
{
	const LENGTH = 32;
	
	public $min = 2;
	public $max = 64;
	
	public $icon = 'face';
	
	# Allow - _ LETTERS DIGITS
	public $pattern = "/^[\\p{L}0-9][-_\\p{L}0-9]{1,31}$/iuD";

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
	    return GDT_Template::php('User', 'form/username.php', ['field' => $this]);
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
		if (!parent::validate($value))
		{
			return false;
		}
		
		# Check existance
		if ($this->exists)
		{
			if ($user = GDO_User::getByLogin($value))
			{
				$this->gdo = $user;
			}
			else
			{
				return $this->error('err_user');
			}
		}
		return true;
	}
	
	public function plugVar()
	{
	    return 'Lazer'; # new created user in unit tests.
	}
	
}
