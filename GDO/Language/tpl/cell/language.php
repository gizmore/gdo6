<?php
use GDO\Language\GDO_Language;
/**
 * @var $language GDO_Language
 */
$href = GDO_WEB_ROOT . 'GDO/Language/img/' . $language->getID() . '.png';
?>
<img
class="gdo-language"
	alt="<?= $language->displayName(); ?>"
	title="<?= $language->displayName(); ?>"
	src="<?=html($href)?>" />
	