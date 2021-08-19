<?php
namespace GDO\Core;

/**
 * This GDT holds a method and executes it directly before rendering.
 * @author gizmore
 * @version 6.10.4
 * @since 6.10.0
 */
class GDT_Method extends GDT
{
    public static function with(Method $method)
    {
        return self::make()->method($method);
    }

    public $method;
    public function method(Method $method)
    {
        $this->method = $method;
        return $this;
    }

    public function renderCell()
    {
        return $this->execute()->renderCell();
    }

    public function renderJSON()
    {
        return [
            'method' => $this->method->gdoShortName(),
        ];
    }

    public function execute()
    {
        $response = GDT_Response::newWith();
        return $response->addField($this->method->execute());
    }

}
