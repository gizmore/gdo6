<?php
use GDO\UI\GDT_Icon;
use GDO\User\GDT_LevelPopup;
use GDO\User\GDO_User;
$field instanceof GDT_LevelPopup;
$user = GDO_User::current();
$levelTooltip = '';
if ($field->level > 0)
{
    $access = $user->getLevel() >= $field->level;
    $accessClass = $access ? 'access-granted' : 'access-denied';
    $levelHtml = '';
    $levelHtml .= t('lvlpopup_item_level', [$field->level])."\n";
    $levelHtml .= t('lvlpopup_your_level', [$user->getLevel()])."\n";
    $levelHtml .= $access ? t('lvlpopup_ok') : t('lvlpopup_too_low');
    $levelHtml .= "\n";
    $levelIcon = GDT_Icon::iconS('security');
    $levelTooltip = <<<EOT
<md-button class="md-icon-button $accessClass">
  {$levelIcon}
  {$field->level}
  <md-tooltip md-direction="right">{$levelHtml}</md-tooltip>
</md-button>
EOT;
}
# Output the shit out of it
echo $levelTooltip;