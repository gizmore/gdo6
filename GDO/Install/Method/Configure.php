<?php
namespace GDO\Install\Method;
use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\Form\GDT_Submit;
use GDO\Install\Config;
use GDO\File\FileUtil;
use GDO\Core\GDT_Template;
use GDO\DB\Database;
use GDO\Core\GDOException;
use GDO\Core\Website;
/**
 * Create a GDO config with this form.
 * @author gizmore
 * @since 3.00
 * @version 6.09
 */
class Configure extends MethodForm
{
	public function configPath()
	{
		return GWF_PATH . 'protected/config.php';
	}
	
	public function createForm(GDT_Form $form)
	{
		$form->addFields(Config::fields());
		$form->addField(GDT_Submit::make('save_config'));
		if (FileUtil::isFile($this->configPath()))
		{
			$form->addField(GDT_Submit::make('test_config'));
		}
	}

	public function onSubmit_save_config(GDT_Form $form)
	{
		$content = GDT_Template::php('Install', 'config.php', ['form' => $form]);
		FileUtil::createDir(dirname($this->configPath()));
		file_put_contents($this->configPath(), $content);
		return Website::redirectMessage(Config::hrefStep(3), 2);
	}
	
	public function onSubmit_test_config(GDT_Form $form)
	{
		$db = new Database(GWF_DB_HOST, GWF_DB_USER, GWF_DB_PASS, GWF_DB_NAME, false);
		try
		{
			$db->getLink();
			return $this->message('install_config_boxinfo_success', [Config::linkStep(4)]);
		}
		catch (GDOException $ex)
		{
			return $this->error('err_db_connect')->add($this->renderPage());
		}
	}
	
}
