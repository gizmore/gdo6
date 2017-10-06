<?php
use GDO\UI\GDT_Icon;
?>
    <md-menu>
      <md-button aria-label="Open phone interactions menu" class="md-icon-button" ng-click="$mdMenu.open($event)">
        <?= GDT_Icon::iconS('done'); ?></md-icon>
      </md-button>
      <md-menu-content width="4">
        <md-menu-item>
          <md-button ng-click="ctrl.redial($event)">
             <?= GDT_Icon::iconS('done'); ?></md-icon>
            Redial
          </md-button>
        </md-menu-item>
        <md-menu-item>
          <md-button disabled="disabled" ng-click="ctrl.checkVoicemail()">
        <?= GDT_Icon::iconS('done'); ?></md-icon>
            Check voicemail
          </md-button>
        </md-menu-item>
        <md-menu-divider></md-menu-divider>
        <md-menu-item>
          <md-button ng-click="ctrl.toggleNotifications()">
        <?= GDT_Icon::iconS('done'); ?></md-icon>
           notifications
          </md-button>
        </md-menu-item>
      </md-menu-content>
    </md-menu>
