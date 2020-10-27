<?php
namespace GDO\Date;

use GDO\Core\GDT;
use GDO\UI\WithPHPJQuery;
use GDO\Core\GDT_Template;

/**
 * Display a date either as age or date
 * @author gizmore
 */
final class GDT_DateDisplay extends GDT
{
    use WithPHPJQuery;
    
    public $showDateAfterSeconds = 172800; # 2 days
    
    public $dateformat = 'short';
    public function dateformat($dateformat) { $this->dateformat = $dateformat; return $this; }
    
    public function renderCell()
    {
        $date = $this->getVar();
        $diff = Time::getDiff($date);
        if ($diff > $this->showDateAfterSeconds)
        {
            $display = Time::displayDate($date, $this->dateformat);
        }
        else
        {
            $display = t('ago', [Time::displayAge($date)]);
        }
        return GDT_Template::php('Date', 'cell/datedisplay.php', ['field' => $this, 'display' => $display]);
    }

}
