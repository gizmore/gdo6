<?php
use GDO\Language\GDO_Language;
use GDO\Language\Module_Language;
use GDO\Language\Trans;

$languages = Module_Language::instance()->cfgSupported();
?>
<div class="gdo-lang-switch">
 <form method="get">
  <input type="hidden" name="mo" value="Language" />
  <input type="hidden" name="me" value="SwitchLanguage" />
  <input type="hidden" name="ref" value="<?=html(urldecode($_SERVER['REQUEST_URI']))?>" />
  <label><?php echo t('lbl_langswitch'); ?></label>
  <select name="_lang">
<?php
foreach ($languages as $language)
{
	$language instanceof GDO_Language;
	$sel = Trans::$ISO === $language->getISO() ? ' selected="selected"' : '';
	printf("<option value=\"%s\"%s>%s</option>", $language->getISO(), $sel, $language->displayName());
}
?>  
  </select>
  <input type="submit" value="<?=t('btn_set')?>" />
 </form>
</div>
