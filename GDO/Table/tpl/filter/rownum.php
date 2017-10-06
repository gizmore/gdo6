<?php
use GDO\Table\GDT_RowNum;
$field instanceof GDT_RowNum;
?>
<div ng-controller="GDOTableToggleCtrl">
  <md-checkbox
   md-indeterminate="cbxAll === undefined"
   ng-model="cbxAll"
   ng-click="cbxToggleAll($event)"></md-checkbox>
</div>
