<?php
namespace GDO\Core;
use GDO\UI\GDT_HTML;
use GDO\UI\WithHTML;

/**
 * The response class renders fields according to the request content-type.
 * You can control the content type with the &fmt=json|html GET parameter.
 * 
 * @author gizmore
 * @version 6.05
 */
final class GDT_Response extends GDT
{
	use WithFields;
	
	##################
	### Error code ###
	##################
	public $code = 200;
	public function code($code) { $this->code = $code; return $this; }
	public function errorCode($code=405) { return $this->code($code); }
	public function isError() { return $this->code >= 400; }
	
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
	
	##############
	### Render ###
	##############
	public function renderCell()
	{
	    switch (Application::instance()->getFormat())
	    {
	        case Application::HTML: return $this->renderHTML();
	        case Application::JSON: return $this->renderJSON();
	    }
	}
	
	public function renderHTML()
	{
	    $html = '';
	    foreach ($this->getFields() as $field)
	    {
	        $html .= $field->render();
	    }
	    return $html;
	}
	
	public function displayJSON()
	{
		$back = [];
		foreach ($this->getFields() as $field)
		{
			if ($json = $field->renderJSON())
			{
				$back = array_merge($back, $json);
			}
		}
		return $back;
	}

	public function renderJSON()
	{
		Website::renderJSON($this->displayJSON());
	}
	
	################
	### Chaining ###
	################
	public function add(GDT_Response $response=null)
	{
	    return $response ? $this->addFields($response->getFields()) : $this;
	}
	
	public function addHTML($html)
	{
	    return $html ? $this->addField(GDT_HTML::withHTML($html)) : $this;
	}
}
