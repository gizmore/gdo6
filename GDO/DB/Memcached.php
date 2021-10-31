<?php
/**
 * A non-operation memcached shim, for when you don't have memcached.
 * @author gizmore
 */
class Memcached
{
	public function addServer() {}
	public function get() { return false; }
	public function set() { return false; }
	public function replace() { return false; }
	public function delete() { return false; }
	public function flush() { return false; }

}
