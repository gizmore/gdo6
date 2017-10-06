<h2><?= t('install_title_3'); ?></h2>
<?php
use GDO\Table\GDT_Table;
use GDO\DB\ArrayResult;
use GDO\Core\GDO_Module;
use GDO\Core\GDT_Template;
use GDO\Form\GDT_Submit;
use GDO\Form\GDT_Hidden;
use GDO\UI\GDT_Panel;

echo GDT_Panel::make()->html(t('install_modules_info_text'))->render();

$table = GDT_Table::make()->result(new ArrayResult($modules, GDO_Module::table()));
$table->addField(GDT_Template::make()->template('Install', 'cell/installcbx.php'));
$table->addField(GDO_Module::table()->gdoColumn('module_name'));
$table->addField(GDO_Module::table()->gdoColumn('module_priority'));

$install = GDT_Submit::make('btn_install');
$hiddenStep = GDT_Hidden::make('step')->val('4');
$table->actions()->addFields([$install, $hiddenStep]);

echo $table->render();
