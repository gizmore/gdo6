<?php
namespace GDO\Mail;

use GDO\Core\GDO_Module;
use GDO\DB\GDT_Checkbox;
use GDO\UI\GDT_Page;
use GDO\UI\GDT_Link;

/**
 * - Mail stuff.
 * - Some user settings.
 * - Send function.
 * 
 * @author gizmore
 *
 */
final class Module_Mail extends GDO_Module
{
    public $module_priority = 30;
    
    public function isCoreModule() { return true; }
    
    public function onLoadLanguage()
    {
        return $this->loadLanguage('lang/mail');
    }
    
    public function getConfig()
    {
        return [
            GDT_Checkbox::make('show_in_sidebar')->initial('1'),
        ];
    }
    
    public function cfgSidebar() { return $this->getConfigValue('show_in_sidebar'); }
    
    public function getUserSettings()
    {
        return [
            GDT_Checkbox::make('allow_email')->initial('1')->label('cfg_user_allow_email'),
            GDT_EmailFormat::make('email_format')->initial('html'),
        ];
    }
    
    public function onInitSidebar()
    {
        if ($this->cfgSidebar())
        {
            GDT_Page::$INSTANCE->rightNav->addField(
                GDT_Link::make('ft_mail_send')->href(
                    href('Mail', 'Send')));
        }
    }
    
}
