<?php
namespace GDO\CLI;

use GDO\Core\GDO_Module;

/**
 * CLI Specific code.
 * @TODO Move CLI utils into this folder.
 * @author gizmore
 * @version 6.10.4
 * @since 6.10.4
 */
final class Module_CLI extends GDO_Module
{
    public function onLoadLanguage()
    {
        return $this->loadLanguage('lang/cli');
    }

}
