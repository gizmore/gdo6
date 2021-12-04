<?php
namespace GDO\UI;

/**
 * A simple code paragraph.
 * 
 * @author gizmore
 * @version 6.11.0
 * @since 6.11.0
 */
class GDT_CodeParagraph extends GDT_Paragraph
{
	public function renderCell()
	{
		return sprintf(
			"<code class=\"gdt-code\">%s</code>\n",
			$this->renderText());
	}

}
