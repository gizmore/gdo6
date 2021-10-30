<?php
namespace GDO\Javascript;

use GDO\Core\GDO_Module;
use GDO\DB\GDT_Enum;
use GDO\File\GDT_Path;
use GDO\UI\GDT_Link;
use GDO\DB\GDT_Checkbox;

/**
 * Configure Javascript options and binaries.
 * 
 * @author gizmore
 * @version 6.10.6
 * @since 6.10.1
 */
final class Module_Javascript extends GDO_Module
{
    public $module_priority = 10;
    
    public function isCoreModule() { return true; }
    public function onLoadLanguage() { return $this->loadLanguage('lang/js'); }

    public function getConfig()
    {
        return [
            GDT_Enum::make('minify_js')->enumValues('no', 'yes', 'concat')->initial($this->env('minify_js', 'no')),
            GDT_Checkbox::make('compress_js')->initial('0'),
            GDT_Path::make('nodejs_path')->initial($this->env('nodejs_path', 'nodejs'))->label('nodejs_path'),
            GDT_Path::make('uglifyjs_path')->initial($this->env('uglifyjs_path', 'uglifyjs'))->label('uglifyjs_path'),
            GDT_Path::make('ng_annotate_path')->initial($this->env('ng_annotate_path', 'ng-annotate'))->label('ng_annotate_path'),
            GDT_Link::make('link_node_detect')->href(href('Javascript', 'DetectNode')),
        ];
    }
    public function cfgMinifyJS() { return $this->getConfigVar('minify_js'); }
    public function cfgCompressJS() { return $this->getConfigVar('compress_js'); }
    public function cfgNodeJSPath() { return $this->getConfigVar('nodejs_path'); }
    public function cfgUglifyPath() { return $this->getConfigVar('uglifyjs_path'); }
    public function cfgAnnotatePath() { return $this->getConfigVar('ng_annotate_path'); }
    public function jsMinAppend() { return $this->cfgMinifyJS() === 'no' ? '' : '.min'; }
    
}
