<?php
namespace GDO\Javascript\Method;

use GDO\Core\MethodAjax;
use GDO\DB\GDT_String;
use GDO\DB\GDT_Text;
use GDO\Mail\Mail;

/**
 * Send js error mail.
 * @TODO: There is a possible exploit lurking, if your mail client renders html.
 * 
 * @author gizmore
 * @since 6.11.1
 */
final class Error extends MethodAjax
{
	public function gdoParameters()
	{
		return [
			GDT_String::make('url'),
			GDT_String::make('message'),
			GDT_Text::make('stack'),
		];
	}
	
	public function execute()
	{
		if (GDO_ERROR_MAIL)
		{
			$url = $this->gdoParameterVar('url');
			$message = $this->gdoParameterVar('message');
			$stack = $this->gdoParameterVar('stack');
			$stack = "<pre>{$stack}</pre>";
			$message = tiso(GDO_LANGUAGE, 'mailb_js_error', [
				$url, $message, $stack, sitename()]);
			Mail::sendDebugMail(': JS Error', $message);
		}
	}
	
}
