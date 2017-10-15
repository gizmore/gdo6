<?php
namespace GDO\UI;

/**
 * Adds an action bar to this gdo type.
 * 
 * @author gizmore
 * @since 6.05
 * @version 6.06
 */
trait WithActions
{
	/**
	 * @var \GDO\UI\GDT_Bar
	 */
    private $actions;

    /**
     * Init or get action bar-
     * @return \GDO\UI\GDT_Bar
     */
    public function actions()
    {
        if (!$this->actions)
        {
            $this->actions = GDT_Bar::make()->horizontal();
        }
        return $this->actions;
    }

}
