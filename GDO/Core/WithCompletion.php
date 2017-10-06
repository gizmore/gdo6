<?php
namespace GDO\Core;
trait WithCompletion
{
    public $completionHref;
    public function completionHref($completionHref)
    {
        $this->completionHref = $completionHref;
        return $this;
    }
}
