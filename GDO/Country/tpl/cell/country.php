<?php
use GDO\Country\GDO_Country;
/**
 * @var GDT_Country $field
 */
$country = $field->getValue();
?>
<?php if ($country instanceof GDO_Country) : ?>
<img
 class="gdo-country"
 alt="<?= $country->displayName(); ?>"
 title="<?= $country->displayName(); ?>"
 src="GDO/Country/img/<?= $country->getID(); ?>.png" />
<span><?= $country->displayName(); ?></span>
<?php else : ?>
<img
 class="gdo-country"
 title="<?= t('unknown_country'); ?>"
 alt="<?= t('unknown_country'); ?>"
 src="GDO/Country/img/zz.png" />
<span><?= t('unknown_country'); ?></span>
<?php endif;?>
