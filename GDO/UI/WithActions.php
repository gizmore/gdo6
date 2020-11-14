<?php
namespace GDO\UI;

/**
 * Adds an action bar to this gdo type.
 * 
 * @author gizmore
 * @since 6.05
 * @version 6.10
 */
trait WithActions
{
	/**
	 * @var \GDO\UI\GDT_Bar
	 */
	public $actions;

	/**
	 * Init or get action bar-
	 * @return \GDO\UI\GDT_Bar
	 */
	public function actions()
	{
		if (!$this->actions)
		{
			$this->actions = GDT_Menu::make('actions');
		}
		return $this->actions;
	}
	
	public function getActions()
	{
		return $this->actions;
	}

	public function hasActions()
	{
		return $this->actions && (!empty($this->actions));
	}
	
}
