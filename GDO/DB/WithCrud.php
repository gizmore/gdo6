<?php
namespace GDO\DB;

/**
 * Add 4 CRUD boolean flags to a GDT.
 * 
 * @author gizmore
 * @since 6.11.4
 */
trait WithCrud
{
	public $creatable = false;
	public function creatable($creatable=true)
	{
		$this->creatable = $creatable;
		return $this;
	}
	
	public $readable = false;
	public function readable($readable=true)
	{
		$this->readable = $readable;
		return $this;
	}
	
	public $updatable = false;
	public function updatable($updatable=true)
	{
		$this->updatable = $updatable;
		return $this;
	}
	
	public $deletable = false;
	public function deletable($deletable=true)
	{
		$this->deletable = $deletable;
		return $this;
	}
	
}
