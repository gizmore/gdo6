<?php
namespace GDO\Core;

use GDO\Util\Random;

/**
 * Add autocompletion variables to a GDT.
 * @author gizmore
 * @version 6.10
 * @since 6.01
 */
trait WithCompletion
{
	public $completionHref;
	public function completionHref($completionHref)
	{
		$this->completionHref = $completionHref;
		return $this;
	}

	public function htmlAutocompleteOff()
	{
	    return sprintf('autocomplete="harrambe_%s"', Random::mrandomKey(rand(2,6)));
	}

}
