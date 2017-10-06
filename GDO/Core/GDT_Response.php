<?php
namespace GDO\Core;
/**
 * The response class renders fields according to the request content-type.
 * You can control the content type with the &fmt=json|html GET parameter.
 * 
 * @author gizmore
 * @version 7.00
 */
final class GDT_Response extends GDT
{
	use WithFields;

	###############
	### Factory ###
	###############
	/**
	 * @param GDT ...$fields
	 * @return self
	 */
	public static function makeWith(GDT ...$fields)
	{
	    return self::make()->addFields($fields);
	}
	
	##############
	### Render ###
	##############
	public function render()
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

	public function renderJSON()
	{
	    $back = [];
	    foreach ($this->getFields() as $field)
	    {
            if ($json = $field->renderJSON())
            {
                $back = array_merge($back, $json);
            }
	    }
	    die(json_encode($back));
	}
	
	public function add(GDT_Response $response)
	{
	    return $this->addFields($response->getFields());
	}
}
