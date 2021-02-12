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
use GDO\File\GDT_Path;

/**
 * Create a GDO config with this form.
 * @author gizmore
 * @version 6.10
 * @since 3.00
 */
class Configure extends MethodForm
{
    public function gdoParameters()
    {
        return [
            GDT_Path::make('filename')->initial('config.php'),
        ];
    }
    
    public function cfgConfigName() { return $this->gdoParameterVar('filename'); }
    
	public function configPath()
	{
		return GDO_PATH . 'protected/' . $this->cfgConfigName();
	}
	
	public function createForm(GDT_Form $form)
	{
		foreach (Config::fields() as $gdt)
		{
			$form->addField($gdt);
		}
		$form->actions()->addField(GDT_Submit::make('save_config'));
		if (FileUtil::isFile($this->configPath()))
		{
			$form->actions()->addField(GDT_Submit::make('test_config'));
		}
	}

	public function onSubmit_save_config(GDT_Form $form)
	{
		$content = GDT_Template::php('Install', 'config.php', ['form' => $form]);
		FileUtil::createDir(dirname($this->configPath()));
		file_put_contents($this->configPath(), $content);
		return Website::redirectMessage('msg_config_written', [html($this->cfgConfigName())], Config::hrefStep(3));
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
