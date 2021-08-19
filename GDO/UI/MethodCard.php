<?php
namespace GDO\UI;

use GDO\Core\GDT_Response;
use GDO\Core\Method;
use GDO\Core\GDO;
use GDO\Util\Common;
use GDO\DB\GDT_Object;
use GDO\Core\GDT_ResponseCard;
use GDO\Table\GDT_GWF;

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
        return $this->gdoTable()->find($this->getID());
    }

    public function execute()
    {
        $gdo = $this->getObject();
        if (!$gdo)
        {
            return $this->error('err_no_data_yet');
        }
        return GDT_ResponseCard::newWith()->gdo($gdo);
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
