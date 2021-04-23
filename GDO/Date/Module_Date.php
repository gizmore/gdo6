<?php
namespace GDO\Date;

use GDO\Core\GDO_Module;

/**
 * Date specific stuff.
 * @author gizmore
 * @version 6.10.1
 */
final class Module_Date extends GDO_Module
{
    public function isCoreModule() { return true; }
    public function onLoadLanguage() { return $this->loadLanguage('lang/date'); }
    
}
