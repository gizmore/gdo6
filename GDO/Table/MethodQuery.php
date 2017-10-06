<?php
namespace GDO\Table;

use GDO\Core\Method;
use GDO\DB\Query;
use GDO\Core\GDT;

abstract class MethodQuery extends Method
{
	/**
	 * @return Query
	 */
	public abstract function gdoQuery();

	/**
	 * @return GDT[]
	 */
	public function gdoParameters()
	{
		return $this->gdoQuery()->table->gdoPrimaryKeyColumns();
	}

	/**
	 * @return GDT[]
	 */
	public function gdoFilters()
	{
	}

	/**
	 * @return Query
	 */
	public function gdoFilteredQuery()
	{
		$query = $this->gdoQuery();
		if ($filters = $this->gdoFilters())
		{
			foreach ($filters as $gdoType)
			{
				$gdoType->filterQuery($query);
			}
		}
		if ($filters = $this->gdoParameters())
		{
			foreach ($filters as $gdoType)
			{
				$gdoType->filterQuery($query);
			}
		}
		return $query;
	}

}
