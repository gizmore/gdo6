<?php
namespace GDO\Crypto;

use GDO\DB\GDT_Token;

/**
 * Sha1 hash.
 * 
 * @author gizmore
 * @version 6.11.3
 * @since 6.11.3
 */
final class GDT_SHA1 extends GDT_Token
{
	protected function __construct()
	{
		parent::__construct();
		$this->length(40);
	}
	
}
