<?php
use GDO\Language\GDO_Language;
$language instanceof GDO_Language;
?>
<div>
<?php if ($language) : ?>
<img
 class="gdo-language"
 alt="<?= $language->displayName(); ?>"
 src="GDO/Language/img/<?= $language->getID(); ?>.png" />
<?= $language->displayName(); ?>
<?php else : ?>
<img
 class="gdo-language"
 alt="<?= t('unknown_language'); ?>"
 src="GDO/Language/img/zz.png" />
<?= t('unknown_language'); ?>
<?php endif;?>
</div>

