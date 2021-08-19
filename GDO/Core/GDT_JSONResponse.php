<?php
namespace GDO\Core;

final class GDT_JSONResponse extends GDT
{
	public $json;
	public function json(array $json) { $this->json = $json; return $this; }

	public function defaultName() { return 'data'; }

	public function renderJSON() { return $this->json; }

}
