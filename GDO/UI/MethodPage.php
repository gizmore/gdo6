<?php
namespace GDO\UI;

use GDO\Core\Method;

/**
 * Default method that simply loads a template.
 * Uses gdoParameters to populate template vars.
 * 
 * @author gizmore
 * @version 6.10.3
 * @since 6.4.0
 */
abstract class MethodPage extends Method
{
    /**
     * {@inheritDoc}
     * @see \GDO\Core\Method::execute()
     */
	public function execute()
	{
		$name = strtolower($this->gdoShortName());
		return $this->templatePHP("page/{$name}.php",
		  $this->getTemplateVars());
	}
	
	protected function getTemplateVars()
	{
		$tVars = [];
		foreach ($this->gdoParameters() as $param)
		{
			$tVars[$param->name] = $this->gdoParameterValue($param->name);
		}
		return $tVars;
	}

}
