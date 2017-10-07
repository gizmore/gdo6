<?php
namespace GDO\Net;
use GDO\DB\GDT_String;
/**
 * Hostname datatype.
 * Optionally validate reachability.
 * @author gizmore
 * @since 6.0.3
 * @version 6.0.3
 */
final class GDT_Hostname extends GDT_String
{
    ###############
    ### Resolve ###
    ###############
    public static function resolve( $hostname) { return gethostbyname($hostname); }
    public function getIP() { return self::resolve($this->getVar()); }
    
    ##################
    ### GDT_String ###
    ##################
    public $min = 1;
    public $max = 128;
    
    #################
    ### Reachable ###
    #################
    public $reachable;
    public function reachable($reachable=true) { $this->reachable = $reachable; return $this; }
    
    ################
    ### Validate ###
    ################
    public function validate($value)
    {
        if (parent::validate($value))
        {
            if ( ($value !== null) && ($this->reachable) )
            {
                return $this->validateReachable($value);
            }
            return true;
        }
    }
    
    public function validateReachable($value)
    {
        return self::resolve($value) ? true : $this->error('err_unknown_host');
    }
}
