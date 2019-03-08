<h2><?= t('install_title_4'); ?></h2>
<?php
use GDO\Table\GDT_Table;
use GDO\DB\ArrayResult;
use GDO\Core\GDO_Module;
use GDO\Core\GDT_Template;
use GDO\Form\GDT_Submit;
use GDO\Form\GDT_Hidden;
use GDO\UI\GDT_Panel;
use GDO\Install\GDT_ModuleFeature;
use GDO\UI\GDT_Link;
use GDO\Install\Module_Install;
use GDO\Install\Config;

echo GDT_Panel::make()->html(t('install_modules_info_text'))->render();

/**
 * @var array $modules
 */
$table = GDT_Table::make()->result(new ArrayResult($modules, GDO_Module::table()));
$table->addHeader(GDT_Template::make()->template('Install', 'cell/installcbx.php'));
$table->addHeader(GDO_Module::table()->gdoColumn('module_name'));
$table->addHeader(GDO_Module::table()->gdoColumn('module_priority'));
$table->addHeader(GDT_ModuleFeature::make('module_features'));

$install = GDT_Submit::make('btn_install');
$skip = Config::linkStepGDT('5');
$hiddenStep = GDT_Hidden::make('step')->val('4');
$table->actions()->addFields([$install, $skip, $hiddenStep]);

echo $table->render();
