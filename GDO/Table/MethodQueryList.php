<?php
namespace GDO\Table;

use GDO\Core\GDT;
use GDO\Core\GDO;
use GDO\Core\GDT_Response;
use GDO\DB\Query;
use GDO\Core\GDT_Fields;

/**
 * Abstract class that renders a list.
 *
 * @author gizmore
 * @version 6.10
 * @since 5.0
 */
abstract class MethodQueryList extends MethodQuery
{
	/**
	 * @return \GDO\Core\GDO
	 */
	public abstract function gdoTable();
	
	public function gdoListMode() { return GDT_List::MODE_LIST; }
	
	public function isOrdered() { return true; }
	public function isPaginated() { return true; }
	public function isFiltered() { return false; }
	public function isQuicksorted() { return false; }
	public function isQuicksearchable() { return false; }
	public function defaultOrderField() { return null; }
	public function defaultOrderDirAsc() { return true; }
	
	public function headersName() { return 'o'; }
	public function listName() { return 'list'; }
	
	public function gdoDecorateList(GDT_List $list) {}
	
	/**
	 * @return Query
	 */
	public function gdoQuery() { return $this->gdoTable()->select(); }
	
	/**
	 * @return Query
	 */
	public function gdoCountQuery() { return $this->gdoQuery()->copy()->selectOnly('COUNT(*)'); }
	
	/**
	 * @return GDT[]
	 */
	public function gdoParameters()
	{
		return array(
			GDT_PageMenu::make('page')->initial('1'),
		    GDT_PageMenu::make('ipp')->initial(Module_Table::instance()->cfgItemsPerPage()),
		);
	}
	
	public function getPage() { return $this->gdoParameterVar('page'); }
	
	/**
	 * Override for quicksearch and order fields.
	 * @return GDT[]
	 */
	public function gdoFilters()
	{
	}
	
	/**
	 * @return GDT_Response
	 */
	public function execute()
	{
		return $this->renderPage();
	}
	
	/**
	 * @return GDT_Response
	 */
	public function renderPage()
	{
		$list = GDT_List::make($this->listName());
		$headers = GDT_Fields::make($this->headersName())->addFields($this->gdoFilters())->addFields($this->gdoParameters());
		$list->headers($headers);
		$list->quicksort($this->isQuicksorted());
		$list->searchable($this->isQuicksearchable());
		$query = $this->gdoQuery();
		$list->query($query);
		$countQuery = $this->gdoCountQuery();
		$list->countQuery($countQuery);
		$this->setupTitle($list);
		$list->listMode($this->gdoListMode());
		
		if ($this->isFiltered())
		{
    		foreach ($this->gdoFilters() as $gdt)
    		{
    		    $gdt->filterQuery($query);
    		    $gdt->filterQuery($countQuery);
    		}
		}

		if ($this->isOrdered())
		{
		    $list->ordered(true, $this->defaultOrderField(), $this->defaultOrderDirAsc());
		}
		if ($this->isPaginated())
		{
		    $list->paginate(true, null, $this->gdoParameterValue('ipp'));
		}
		$this->gdoDecorateList($list);
		return GDT_Response::makeWith($list);
	}
	
	protected function setupTitle(GDT_List $list)
	{
		$list->title(t('list_'.strtolower($this->gdoShortName()), [$list->countItems()]));
	}
	
}

