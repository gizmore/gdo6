<?php
namespace GDO\Core;
use GDO\DB\GDT_Enum;
use GDO\DB\GDT_UInt;
use GDO\File\GDT_Path;
use GDO\UI\GDT_Divider;
use GDO\UI\GDT_Link;
/**
 * The first module by priority, and it *HAS* to be installed for db driven sites,
 * simply because it installs the module table.
 * 
 * Also this module provides the default theme,
 * which is almost empty and is using the default tpl of the modules.
 * 
 * Config vars are kept here for ItemsPerPage and SuggestionsPerPage (ipp/spp).
 * 
 * @author gizmore
 * @since 6.00
 * @version 6.05
 */
final class Module_Core extends GDO_Module
{
    ##############
    ### Module ###
    ##############
    public $module_priority = 1;
    
    public function getThemes() { return ['default']; }
    
    public function onLoadLanguage() { $this->loadLanguage('lang/core'); }
    
    public function getClasses()
    {
        return array(
            'GDO\Core\GDO_Module',
            'GDO\Core\GDO_ModuleVar',
            'GDO\User\GDO_Permission'
        );
    }
    ##############
    ### Config ###
    ##############
    public function getConfig()
    {
        return array(
            GDT_Divider::make()->label('div_pagination'),
            GDT_UInt::make('ipp')->max(1000)->initial('20'),
            GDT_UInt::make('spp')->max(1000)->initial('10'),
            GDT_Divider::make()->label('div_javascript'),
            GDT_Enum::make('minify_js')->enumValues('no', 'yes', 'concat')->initial('no'),
            GDT_Path::make('nodejs_path')->initial('nodejs')->label('nodejs_path'),
            GDT_Path::make('uglifyjs_path')->initial('uglifyjs')->label('uglifyjs_path'),
            GDT_Path::make('ng_annotate_path')->initial('ng-annotate')->label('ng_annotate_path'),
            GDT_Link::make('link_node_detect')->href(href('GWF', 'DetectNode')),
        );
    }
    public function cfgIPP() { return $this->getConfigVar('ipp'); }
    public function cfgSPP() { return $this->getConfigVar('spp'); }
    public function cfgMinifyJS() { return $this->getConfigVar('minify_js'); }
    public function cfgNodeJSPath() { return $this->getConfigVar('nodejs_path'); }
    public function cfgUglifyPath() { return $this->getConfigVar('uglifyjs_path'); }
    public function cfgAnnotatePath() { return $this->getConfigVar('ng_annotate_path'); }
}
