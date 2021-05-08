<?php
namespace GDO\Core;

use GDO\UI\GDT_HTML;

/**
 * The response class renders fields according to the request content-type.
 * You can control the content type with the &fmt=json|html|cli|xml GET parameter.
 * The &ajax=1 parameter will drop the gdo6 site around it so you can focus on the data.
 * 
 * @author gizmore
 * @version 6.10.3
 * @since 6.0.0
 */
class GDT_Response extends GDT
{
	use WithFields;
	
	public static $CODE = 200;
	
	/**
	 * @var self
	 */
	public static $INSTANCE = null;
	
	public static function instance() { return self::$INSTANCE; }
	
	public static function globalError() { return self::$CODE >= 400; }
	
	public function defaultName() { return 'response'; }
	
	##################
	### Error code ###
	##################
	public $code = 200;
	public function code($code) { $this->code = $code; return $this; }
	public function errorCode($code=405) { return $this->code($code); }
	public function isError()
	{
	    return $this->code >= 400;
// 		if ($this->code >= 400)
// 		{
// 			return true;
// 		}
// 		foreach ($this->fields as $gdt)
// 		{
// 			if ($gdt->hasError())
// 			{
// 				return true;
// 			}
// 		}
// 		return false;
	}
	
	###############
	### Factory ###
	###############
	/**
	 * Create a response from plain html by adding a GDT_HTML field containing your html.
	 * @param string $html
	 * @return \GDO\Core\GDT_Response
	 */
	public static function makeWithHTML($html)
	{
		return self::make()->addHTML($html);
	}
	
	public static function make($name=null)
	{
	    if (self::$INSTANCE === null)
	    {
	        self::$INSTANCE = parent::make($name);
	    }
	    return self::$INSTANCE;
	}
	
	##############
	### Render ###
	##############
	public function render()
	{
	    return $this->renderCell();
	}
	
	public function renderCell()
	{
	    self::$INSTANCE = null;
	    switch (Application::instance()->getFormat())
		{
		    case Application::CLI: return $this->renderCLI();
		    case Application::HTML: return $this->renderHTML();
		    case Application::JSON: return $this->renderJSON();
		    case Application::XML: return $this->renderXML();
		}
	}
	
	public function renderHTML()
	{
	    $html = '';
	    if ($fields = $this->getFields())
	    {
    	    foreach ($fields as $field)
    	    {
	            $html .= $field->renderCell();
    	    }
	    }
	    return $html;
	}
	
	public function renderJSON()
	{
		return [
			'code' => $this->code,
			'json' => $this->renderJSONFields(),
		];
	}
	
	public function renderCLI()
	{
	    return $this->renderCLIFields();
	}

	private function renderCLIFields()
	{
	    $back = '';
	    foreach ($this->fields as $field)
	    {
	        $back .= $field->renderCLI();
	    }
	    return trim($back);
	}
	
	private function renderJSONFields()
	{
		$back = [];
		foreach ($this->getFieldsRec() as $field)
		{
		    if ($field->name)
		    {
		        $json = $field->renderJSON();
		        $back[$field->name] = $json;
		    }
		}
		return $back;
	}
	
	################
	### Chaining ###
	################
	public function add(GDT $response=null)
	{
	    if (!($response instanceof GDT_Response))
	    {
	        $this->addField($response);
	    }
	    return $this;
// 	    if ($response && $response->code != 200)
// 	    {
// 	        self::$CODE = $this->code = $response->code;
// 	    }
// 		return $response ? $this->addFields($response->getFields()) : $this;
	}
	
	public function addHTML($html)
	{
		return $html ? $this->addField(GDT_HTML::withHTML($html)) : $this;
	}
	
}
