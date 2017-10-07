<?php
namespace GDO\UI;
/**
 * Adds an action bar to this type.
 * @author gizmore
 * @since 6.05
 */
trait WithActions
{
    private $actions;
    /**
     * Init or get action bar-
     * @return \GDO\UI\GDT_Bar
     */
    public function actions()
    {
        if (!$this->actions)
        {
            $this->actions = GDT_Bar::make();
        }
        return $this->actions;
    }
}
