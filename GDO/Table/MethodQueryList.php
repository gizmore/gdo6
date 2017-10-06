<?php
namespace GDO\Table;
use GDO\Core\GDT;
use GDO\Core\GDO;
use GDO\Core\GDT_Response;
use GDO\DB\Query;

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
     * @return GDO
     */
    public abstract function gdoTable();
    
    public function gdoListMode() { return GDT_List::MODE_LIST; }
    
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
        );
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
        $list = GDT_List::make();
        $list->label('list_'.strtolower($this->gdoShortName()), [sitename()]);
        $list->addFields($this->gdoFilters());
        $list->addFields($this->gdoParameters());
        
        $list->query($this->gdoFilteredQuery());
        $list->listMode($this->gdoListMode());
        $list->paginate();
        $list->href($this->href());
        $this->gdoDecorateList($list);
        return GDT_Response::make($list->renderCell());
    }
}

