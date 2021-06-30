<?php
namespace GDO\UI;

/**
 * Flex class handling trait for containers.
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 6.3.0
 * 
 * @see GDT_Bar
 * @see GDT_Container
 */
trait WithFlex
{
    public static $FLEX_HORIZONTAL = 1;
    public static $FLEX_VERTICAL = 2;
    
    #################
    ### Paramters ###
    #################
    public $flex = false;
    public $flexCollapse = false;
    public $flexDirection = 0;
    
    /**
     * Enable flex for this container.
     * 
     * @param boolean $flex
     * @return self
     */
    public function flex($flex=true, $collapse=false)
    {
        $this->flex = $flex;
        $this->flexCollapse = $collapse;
        return $this;
    }
    
    #################
    ### Direction ###
    #################
    public function vertical($collapse=false)
    {
        $this->flexDirection = self::$FLEX_VERTICAL;
        return $this->flex(true, $collapse);
    }
    
    public function horizontal($collapse=false)
    {
        $this->flexDirection = self::$FLEX_HORIZONTAL;
        return $this->flex(true, $collapse);
    }
    
    ##############
    ### Render ###
    ##############
    /**
     * Render classname for flex classes.
     * @return string
     */
    public function htmlDirection()
    {
        return $this->flexDirection === self::$FLEX_HORIZONTAL ?
        'row' : 'column';
    }

}
