<?phpuse GDO\File\GDO_File;
use GDO\File\GDT_File;
/** @var $field GDT_File **//** @var $file GDO_File **/$file = $field->getValue();?>
<label><?=$field->displayLabel()?></label><?php if ($file) : ?><a href="<?=$field->displayPreviewHref($file)?>"><?=$file->displayName()?></a>
<?php else : ?><?=t('none')?><?php endif; ?>