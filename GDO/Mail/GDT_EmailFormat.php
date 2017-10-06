<?php
namespace GDO\Mail;
use GDO\DB\GDT_Enum;

/**
 * Enum that switches between text und html format.
 * @author gizmore
 * @since 5.0
 */
final class GDT_EmailFormat extends GDT_Enum
{
	const TEXT = 'text';
	const HTML = 'html';
	
	public function __construct()
	{
		$this->enumValues(self::TEXT, self::HTML);
	}

	public function defaultLabel() { return $this->label('email_fmt'); }
	
}
