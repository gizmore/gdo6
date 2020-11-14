<?php
namespace GDO\Table;
use GDO\Core\Method;
use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\DB\GDT_AutoInc;
use GDO\Util\Common;

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
	
	/**
	 * @return \GDO\Core\GDO
	 */
	public function getQueryCard()
	{
		return $this->gdoTable()->find(Common::getGetString('id'));
	}
	
	public function execute()
	{
		return $this->renderCard();
	}
	
	public function renderCard()
	{
		if ($object = $this->getQueryCard())
		{
			return $object->responseCard();
		}
	}
}
