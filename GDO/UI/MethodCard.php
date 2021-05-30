<?php
namespace GDO\UI;

use GDO\Core\Method;
use GDO\Core\GDO;
use GDO\Util\Common;
use GDO\Core\GDT_Response;
use GDO\DB\GDT_Object;

abstract class MethodCard extends Method
{
    /**
     * @return GDO
     */
    public abstract function gdoTable();
    
    public function idName() { return 'id'; }

    public function getID() { return Common::getRequestString($this->idName()); }
    
    public function gdoParameters()
    {
        return [
            GDT_Object::make($this->idName())->table($this->gdoTable())->notNull(),
        ];
    }
    
    /**
     * @return GDO
     */
    public function getObject()
    {
        return $this->gdoTable()->getById(Common::getRequestString($this->idName()));
    }
    
    public function execute()
    {
        $gdo = $this->getObject();
        if (!$gdo)
        {
            return $this->error('err_no_data_yet');
        }
        return GDT_Response::makeWithHTML($gdo->renderCard());
    }
    
    public function getTitle()
    {
        if ($gdo = $this->getObject())
        {
            return $gdo->displayName();
        }
        return parent::getTitle();
    }
    
}
