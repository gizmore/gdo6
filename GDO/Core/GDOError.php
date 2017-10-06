<?php
namespace GDO\Core;
class GDOError extends GDOException
{
    public function __construct($key, array $args=null, $code=500)
    {
        parent::__construct(t($key, $args), $code);
    }
}
