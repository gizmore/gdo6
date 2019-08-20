<?php
use GDO\Country\GDO_Country;
/** @var $field \GDO\Country\GDT_Country **/
$country = $field->gdo;
?>
<?php if ($country instanceof GDO_Country) : ?>
<img
 class="gdo-country"
 alt="<?= $country->displayName(); ?>"
 title="<?= $country->displayName(); ?>"
 src="GDO/Country/img/<?= $country->getID(); ?>.png" />
<?php if ($field->withName) : ?>
<span><?= $country->displayName(); ?></span>
<?php endif; ?>
<?php else : ?>
<img
 class="gdo-country"
 title="<?= t('unknown_country'); ?>"
 alt="<?= t('unknown_country'); ?>"
 src="GDO/Country/img/zz.png" />
<?php if ($field->withName) : ?>
<span><?= t('unknown_country'); ?></span>
<?php endif;?>
<?php endif;?>
