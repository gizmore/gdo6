<?php
namespace GDO\DB;

class GDT_Float extends GDT_Int
{
    public function toValue($var) { return $var === null ? null : (float) $var; }
    
    public function gdoColumnDefine()
    {
        $unsigned = $this->unsigned ? " UNSIGNED" : "";
        return "{$this->identifier()} FLOAT{$unsigned}{$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
    }
        
    public function htmlClass()
    {
        return sprintf(' gdt-float %s', parent::htmlClass());
    }

}
