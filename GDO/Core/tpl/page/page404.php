<?php
use GDO\UI\GDT_Panel;
use GDO\UI\GDT_Paragraph;
use GDO\Core\GDT_Error;
$pane = GDT_Panel::makeWith(GDT_Error::make()->text('info_page_not_found'));
echo $pane->render();
