<?php
namespace GDO\Core;

/**
 * This GDT holds a method and executes it upon rendering.
 * 
 * @author gizmore
 * @version 6.11.4
 * @since 6.10.0
 */
class GDT_Method extends GDT
{
	###############
	### Factory ###
	###############
    public static function with(Method $method)
    {
        return self::make()->method($method);
    }
    
    ############
    ### Exec ###
    ############
    public function execute()
    {
    	$response = GDT_Response::newWith();
    	return $response->addField($this->method->execute());
    }
    
    #############
    ### Param ###
    #############
    public $method;
    public function method(Method $method)
    {
        $this->method = $method;
        return $this;
    }
    
    ##############
    ### Render ###
    ##############
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

}
