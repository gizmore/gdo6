<?php
namespace GDO\Table;

use GDO\Core\Method;
use GDO\Core\GDO;
use GDO\Util\Common;
/**
 * Ajax adapter that swaps two items using their GDT_Sort column.
 * 
 * @author gizmore
 * @since 5.0
 * @version 5.0
 */
abstract class MethodSort extends Method
{
	/**
	 * @return GDO
	 */
	public abstract function gdoSortObjects();
	
	public function canSort(GDO $gdo) { return true; }
	
	############
	### Exec ###
	############
	/**
	 * Method is ajax and always a write / transaction.
	 * {@inheritDoc}
	 * @see Method::isAlwaysTransactional()
	 */
	public function isAlwaysTransactional() { return true; }

	/**
	 * Force ajax and JSON rendering.
	 * {@inheritDoc}
	 * @see Method::isAjax()
	 */
	public function isAjax() { return true; }
	
	/**
	 * Find the sort column name and swap item sorting.
	 * {@inheritDoc}
	 * @see Method::execute()
	 */
	public function execute()
	{
		$table = $this->gdoSortObjects();
		if (!($name = $this->getSortingColumnName($table)))
		{
			return $this->error('err_table_not_sortable', [$table->gdoHumanName()]);
		}
		$a = $table->find(Common::getRequestString('a'));
		$b = $table->find(Common::getRequestString('b'));
		if ( (!$this->canSort($a)) || (!$this->canSort($b)) )
		{
			return $this->error('err_table_not_sortable', [$table->gdoHumanName()]);
		}
		$sortA = $a->getVar($name);
		$sortB = $b->getVar($name);
		$a->saveVar($name, $sortB);
		$b->saveVar($name, $sortA);
		return $this->message('msg_sort_success');
	}
	
	/**
	 * Determine the sort column.
	 * @param GDO $table
	 * @return string
	 */
	protected function getSortingColumnName(GDO $table)
	{
		foreach ($table->gdoColumnsCache() as $gdoType)
		{
			if ($gdoType instanceof GDT_Sort)
			{
				return $gdoType->name;
			}
		}
	}
}
