<?php
use GDO\Table\GDT_RowNum;
$field instanceof GDT_RowNum;
?>
<input
 name="<?=$field->name?>[0]"
 type="checkbox"
 gdo-toggle-class="rbxall-<?=$field->name?>"
 onclick="window.GDO.toggleAll(this)" />
