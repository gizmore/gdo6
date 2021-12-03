<?php
namespace GDO\Javascript;

use GDO\Core\GDO_Module;
use GDO\DB\GDT_Enum;
use GDO\File\GDT_Path;
use GDO\UI\GDT_Link;
use GDO\DB\GDT_Checkbox;
use GDO\Javascript\Method\DetectNode;
use GDO\UI\GDT_Divider;

/**
 * Configure Javascript options and binaries.
 * 
 * @author gizmore
 * @version 6.11.0
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
        	GDT_Divider::make('div_binaries'),
            GDT_Link::make('link_node_detect')->href(href('Javascript', 'DetectNode')),
            GDT_Path::make('nodejs_path')->label('nodejs_path'),
            GDT_Path::make('uglifyjs_path')->label('uglifyjs_path'),
            GDT_Path::make('ng_annotate_path')->label('ng_annotate_path'),
        ];
    }
    public function cfgMinifyJS() { return $this->getConfigVar('minify_js'); }
    public function cfgCompressJS() { return $this->getConfigVar('compress_js'); }
    public function cfgNodeJSPath() { return $this->getConfigVar('nodejs_path'); }
    public function cfgUglifyPath() { return $this->getConfigVar('uglifyjs_path'); }
    public function cfgAnnotatePath() { return $this->getConfigVar('ng_annotate_path'); }
    public function jsMinAppend() { return $this->cfgMinifyJS() === 'no' ? '' : '.min'; }
    
    public function onInstall()
    {
    	$detect = DetectNode::make();
    	if (!$this->cfgNodeJSPath())
    	{
	    	$detect->detectNodeJS();
    	}
    	if (!$this->cfgAnnotatePath())
    	{
    		$detect->detectAnnotate();
    	}
    	if (!$this->cfgUglifyPath())
    	{
	    	$detect->detectUglify();
    	}
    }

}
