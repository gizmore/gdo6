<?php
use GDO\Country\GDO_Country;
use GDO\Country\GDT_Country;
/**
 * @var GDT_Country $field
 */
$country = $field->gdo;
$root = GDO_WEB_ROOT;
?>
<?php if ($country instanceof GDO_Country) : ?>
<img
 class="gdo-country"
 alt="<?$country->displayName()?>"
 title="<?$country->displayName()?>"
 src="<?=$root?>GDO/Country/img/<?=$country->getID()?>.png" />
<?php else : ?>
<img
 class="gdo-country"
 title="<?=t('unknown_country')?>"
 alt="<?=t('unknown_country')?>"
 src="<?=$root?>GDO/Country/img/zz.png" />
<?php endif; ?>
