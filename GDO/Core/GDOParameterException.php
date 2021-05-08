<?php
namespace GDO\Core;

/**
 * Thrown in Method for gdoParameter()
 * 
 * @author gizmore
 * @version 6.10.3
 * @since 6.10.3
 */
final class GDOParameterException extends GDOError
{
    public function __construct(GDT $field, $value)
    {
        parent::__construct('err_parameter_exception', [$field->name, $field->error, $field->displayValue($value)]);
    }
    
}
