<?php
namespace GDO\Table;
use GDO\Core\GDT_Fields;
use GDO\DB\ArrayResult;
use GDO\Util\Common;

trait WithHeaders
{
    public $headers;
    public function headers(GDT_Fields $headers) { $this->headers = $headers; }
    
    public function multisort(ArrayResult $result)
    {
        $sort = $this->make_cmp(Common::getRequestArray($this->headers->name));
        usort($result->data, $sort);
    }
    
    private function make_cmp(array $sorting)
    {
        $headers = $this->headers;
        return function ($a, $b) use (&$sorting, &$headers)
        {
            foreach ($sorting as $column => $sortDir)
            {
                $diff = $headers->getField($column)->gdoCompare($a, $b);
                if ($diff !== 0)
                {
                    return $sortDir === '1' ? $diff : -$diff;
                }
            }
            return 0;
        };
    }
}
