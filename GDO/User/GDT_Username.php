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
	
	public $icon = 'face';
	
	# Allow - _ LETTERS DIGITS
	public $pattern = "/^[-_\\p{L}0-9]{2,32}$/iuD";

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
}
