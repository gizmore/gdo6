<?php
namespace GDO\UI;
use GDO\Core\Method;
/**
 * Default method that simply loads a template.
 * @author gizmore
 */
class MethodPage extends Method
{
	public function execute()
	{
		$name = strtolower($this->gdoShortName());
		return $this->templatePHP("page/{$name}.php");
	}
}
