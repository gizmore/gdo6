<?php
/**
 * A non-operation memcached shim, for when you don't have memcached.
 * @author gizmore
 */
class Memcached
{
    public function addServer() {}
    public function get() { return false; }
    public function set() {}
    public function flush() {}
    public function delete() {}
    public function replace() {}
}
