<?php
namespace GDO\DB;

/**
 * Named identifier.
 * Is unique among their table and case-s ascii.
 * 
 * @author gizmore
 * @version 6.11.0
 * @since 6.1.0
 */
class GDT_Name extends GDT_String
{
	public function plugVar() { return 'Name_' . self::$COUNT; }
	
	public function defaultLabel() { return $this->label('name'); }

	const LENGTH = 64;
	
	public $min = 2, $max = self::LENGTH;
	public $encoding = self::ASCII;
	public $caseSensitive = true;
	public $pattern = "/^[A-Za-z][-A-Za-z _0-9;:]{1,63}$/sD";
	public $notNull = true;
	public $unique = true;
	
	##############
	### Render ###
	##############
	public function renderCell()
	{
	    if ($this->gdo)
	    {
	        return $this->gdo->displayName();
	    }
	    return $this->displayVar();
	}
	
	public function renderCLI()
	{
	    return $this->renderCell();
	}
	
	public function renderJSON()
	{
	    return $this->renderCell();
	}
	
}
