<?php
namespace GDO\UI;

use GDO\Util\Random;
use GDO\DB\GDT_DeletedAt;

/**
 * View a single random item from a gdo table as card.
 * @author gizmore
 */
abstract class MethodRandomCard extends MethodCard
{
    public function gdoParameters()
    {
    }
    
    public function getObject()
    {
        $table = $this->gdoTable();
        $id = $table->gdoPrimaryKeyColumn()->name;
        $query = $table->select('MAX(' . $id . ')');
        if ($delete = $table->gdoColumnOf(GDT_DeletedAt::class))
        {
            $query->where($delete->name . ' IS NULL');
        }
        $max = $query->exec()->fetchValue();
        if (!$max)
        {
            return null;
        }
        $max = Random::mrand(1, $max);
        return $table->findWhere($id . ' >= ' . $max);
    }
    
}
