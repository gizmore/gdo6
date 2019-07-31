<?php
namespace GDO\UI;

use GDO\Core\Method;

/**
 * Default method that simply loads a template.
 * Uses gdoParameters to populate template vars.
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.04
 */
class MethodPage extends Method
{
	public function execute()
	{
		$name = strtolower($this->gdoShortName());
		return $this->templatePHP("page/{$name}.php", $this->getTemplateVars());
	}
	
	private function getTemplateVars()
	{
		$tVars = [];
		foreach ($this->gdoParameters() as $param)
		{
			$tVars[$param->name] = $param->getValue();
		}
		return $tVars;
	}
}
