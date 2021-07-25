<?php
namespace GDO\Core;

use GDO\UI\GDT_HTML;

/**
 * The response class renders fields according to the request content-type.
 * You can control the content type with the &fmt=json|html|cli|xml GET parameter.
 * The &ajax=1 parameter will drop the gdo6 site around it so you can focus on the data.
 * There is only one global Response used by the rendering, which can be stacked via GDT_Response::newWith().
 * 
 * @author gizmore
 * @version 6.10.4
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
	
	/**
	 * 
	 * @param string $name
	 * @return self
	 */
	public static function make($name=null)
	{
	    if (self::$INSTANCE === null)
	    {
	        self::$INSTANCE = parent::make($name);
	    }
	    return self::$INSTANCE;
	}
	
	/**
	 * Make a new response. Has to be called for any execute within an execute.
	 * @param GDT ...$fields
	 * @return \GDO\Core\GDT_Response
	 */
	public static function newWith(GDT ...$fields)
	{
	    self::$INSTANCE = null;
	    return self::makeWith(...$fields);
	}
	
	/**
	 * Create a new response with html
	 * @param string $html
	 * @return self
	 */
	public static function newWithHTML($html)
	{
	    return self::newWith()->addHTML($html);
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
		    'top' => Website::renderTopResponse(),
			'json' => $this->renderJSONFields(),
		];
	}
	
	################
	### Chaining ###
	################
	public function addField(GDT $field=null)
	{
	    if ( (!$field) || ($field === $this) )
	    {
	        return $this;
	    }
	    if ($field instanceof GDT_Response)
	    {
	        $this->code($field->code);
	        return $this->addFields($field->fields);
	    }
	    else
	    {
	        $this->_addField($field);
    	    return $this;
	    }
	}
	
	public function addFields(array $fields=null)
	{
	    if ($fields)
	    {
	        foreach ($fields as $gdt)
	        {
	            $this->addField($gdt);
	        }
	    }
	    return $this;
	}
	
	/**
	 * @param string $html
	 * @return self
	 */
	public function addHTML($html)
	{
		return $html ? $this->addField(GDT_HTML::withHTML($html)) : $this;
	}
	
}
