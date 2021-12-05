<?php
namespace GDO\UI;

/**
 * A simple code paragraph.
 * 
 * @author gizmore
 * @version 6.11.1
 * @since 6.11.0
 */
class GDT_CodeParagraph extends GDT_Paragraph
{
	public function renderCell()
	{
		return sprintf(
			"<div class=\"gdt-code\"><code><pre>%s\n</pre></code></div>\n",
			$this->renderText());
	}

}
