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
 * @since 5.0
 * @version 5.0
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
	
	public function gdoDecorateList(GDT_List $list) {}
	
	/**
	 * @return Query
	 */
	public function gdoQuery() { return $this->gdoTable()->select(); }
	
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
	
	public function getListName()
	{
		return 'list';
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
		$list = GDT_List::make($this->getListName());
		$list->query($this->gdoFilteredQuery());
		$this->setupTitle($list);
		$headers = GDT_Fields::make('o')->addFields($this->gdoFilters())->addFields($this->gdoParameters());
		$list->headers($headers);
		$list->listMode($this->gdoListMode());
		if ($this->isPaginated())
		{
			$list->paginate(true, null, $this->gdoParameterValue('ipp'));
		}
		if ($this->isOrdered())
		{
			$list->ordered();
		}
// 		$list->href($this->href());
		$this->gdoDecorateList($list);
		return GDT_Response::makeWith($list);
	}
	
	protected function setupTitle(GDT_List $list)
	{
		$list->title(t('list_'.strtolower($this->gdoShortName()), [$list->countItems()]));
	}
	
}

