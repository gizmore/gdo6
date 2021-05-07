<h2><?= t('install_title_9'); ?></h2>
<?php
use GDO\UI\GDT_Panel;
use GDO\Install\Config;

/** @var $form \GDO\Form\GDT_Form **/
echo $form->render();

echo GDT_Panel::make()->text('copy_htaccess_info', [Config::linkStep(10)])->render();
