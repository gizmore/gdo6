<?php
namespace GDO\Core;
abstract class MethodAjax extends Method
{
    public function isAjax() { return true; }
}
