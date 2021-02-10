<?php
use GDO\UI\GDT_Panel;
use GDO\UI\GDT_Paragraph;
$pane = GDT_Panel::makeWith(GDT_Paragraph::make()->text('info_page_not_found'));
echo $pane->render();
