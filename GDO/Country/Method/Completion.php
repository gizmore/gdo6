<?php
namespace GDO\Country\Method;
use GDO\Core\GDO;
use GDO\Core\MethodCompletion;
use GDO\Country\GDO_Country;
use GDO\Country\GDT_Country;
/**
 * Autocomplete adapter for countries.
 * @author gizmore
 * @since 6.00
 * @version 6.05
 */
final class Completion extends MethodCompletion
{
    public function gdoTable()
    {
        return GDO_Country::table();
    }

    public function gdoHeaderColumns()
    {
        $t = $this->gdoTable();
        return array(
            $t->gdoColumn('c_iso'),
        );
    }

    /**
     * @var $gdo GDO_Country
     */
    public function renderJSON(GDO $gdo)
    {
        $cell = GDT_Country::make('c_iso');
        return array(
            'id' => $gdo->getID(),
            'text' => $gdo->displayName(),
            'display' => $cell->gdo($gdo)->renderCell(),
        );
    }
    
}
