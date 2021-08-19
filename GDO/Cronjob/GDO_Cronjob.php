<?php
namespace GDO\Cronjob;

use GDO\Core\GDO;
use GDO\DB\GDT_String;
use GDO\DB\GDT_AutoInc;
use GDO\DB\GDT_CreatedAt;
use GDO\Date\GDT_DateTime;
use GDO\DB\GDT_Checkbox;

/**
 * This table holds info about the cronjob runnings.
 * @author gizmore
 * @since 6.10.4
 */
final class GDO_Cronjob extends GDO
{
    public function gdoColumns()
    {
        return [
            GDT_AutoInc::make('cron_id'),
            GDT_String::make('cron_method')->notNull()->ascii()->caseS()->max(128),
            GDT_CreatedAt::make('cron_started'),
            GDT_DateTime::make('cron_finished'),
            GDT_Checkbox::make('cron_success')->notNull()->initial('0'),
        ];
    }

}
