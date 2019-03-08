<?php
use GDO\Language\GDO_Language;
/**
 * @var $language GDO_Language
 */
?>
<img
class="gdo-language"
	alt="<?= $language->displayName(); ?>"
	title="<?= $language->displayName(); ?>"
	src="GDO/Language/img/<?= $language->getID(); ?>.png" />
	