<?php
use GDO\UI\GDT_Panel;
use GDO\UI\GDT_Paragraph;
$pane = GDT_Panel::makeWith(GDT_Paragraph::make()->text('err_no_permission'));
echo $pane->render();
