<?php
namespace GDO\UI;

use GDO\Core\Method;
use GDO\Core\GDO;
use GDO\Util\Common;
use GDO\Core\GDT_Response;

abstract class MethodCard extends Method
{
    /**
     * @return GDO
     */
    public abstract function gdoTable();
    
    public function idName() { return 'id'; }

    public function getID() { return Common::getRequestString('id'); }
    
    public function execute()
    {
        $gdo = $this->gdoTable()->find($this->getID());
        return GDT_Response::makeWithHTML($gdo->renderCard());
    }
    
}
