<?php
namespace GDO\DB;

/**
 * Named identifier.
 * Is unique among their table and case-s ascii.
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 6.1.0
 */
class GDT_Name extends GDT_String
{
	public function plugVar() { return 'Name_' . self::$COUNT; }
	
	public function defaultLabel() { return $this->label('name'); }

	const LENGTH = 96;
	
	public $min = 2, $max = self::LENGTH;
	public $encoding = self::ASCII;
	public $caseSensitive = true;
	public $pattern = "/^[-a-z _0-9;:@.!?]{1,64}$/is";
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
