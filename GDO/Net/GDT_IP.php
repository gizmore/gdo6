<?php
namespace GDO\Net;
use GDO\DB\GDT_String;
/**
 * IP column and rendering.
 * @author gizmore
 */
final class GDT_IP extends GDT_String
{
    public function defaultLabel() { return $this->label('ip'); }
    
    public static $CURRENT = null;
    public static function current() { return self::$CURRENT; }
    
    public $min = 3;
    public $max = 45;
    public $encoding = self::ASCII;
    public $caseSensitive = false;
    public $pattern = "/^[.:0-9a-fA-F]{3,45}$/";
}

# Assign current IP.
GDT_IP::$CURRENT = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
