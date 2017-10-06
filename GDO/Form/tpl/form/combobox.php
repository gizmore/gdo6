<?php
use GDO\Form\GDT_ComboBox;
$field instanceof GDT_ComboBox;
?>
<md-input-container class="md-block md-float md-icon-left<?= $field->classError(); ?>" flex ng-app="gdo-combobox">
  <md-autocomplete
          ng-disabled="ctrl.isDisabled"
          md-no-cache="ctrl.noCache"
          md-selected-item="ctrl.selectedItem"
          md-search-text-change="ctrl.searchTextChange(ctrl.searchText)"
          md-search-text="ctrl.searchText"
          md-selected-item-change="ctrl.selectedItemChange(item)"
          md-items="item in ctrl.querySearch(ctrl.searchText)"
          md-item-text="item.display"
          md-min-length="0"
          placeholder="What is your favorite US state?">
        <md-item-template>
          <span md-highlight-text="ctrl.searchText" md-highlight-flags="^i">{{item.display}}</span>
        </md-item-template>
        <md-not-found>
          No states matching "{{ctrl.searchText}}" were found.
          <a ng-click="ctrl.newState(ctrl.searchText)">Create a new one!</a>
        </md-not-found>
      </md-autocomplete>

</md-input-container>