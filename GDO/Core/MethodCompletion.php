<?php
namespace GDO\Core;
use GDO\Util\Common;
abstract class MethodCompletion extends MethodAjax
{
	public function getMaxSuggestions() { return Module_Core::instance()->cfgSPP(); }
	public function getSearchTerm() { return Common::getRequestString('query'); }
}
