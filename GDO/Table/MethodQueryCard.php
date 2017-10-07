<?php
namespace GDO\Table;
use GDO\Core\Method;
use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\DB\GDT_AutoInc;

abstract class MethodQueryCard extends Method
{
	/**
	 * @return GDO
	 */
	public abstract function gdoTable();
	
	/**
	 * @return GDT[]
	 */
	public function gdoParameters()
	{
	    return [GDT_AutoInc::make('id')];
	}
	
	public function gdoQueryCard()
	{
		$params = $this->gdoParameters();
		return $this->gdoTable()->find(array_shift($params)->getParameterVar());
	}
	
	public function execute()
	{
		return $this->renderCard();
	}
	
	public function renderCard()
	{
		if ($object = $this->gdoQueryCard())
		{
		    return $object->responseCard();
		}
	}
}
