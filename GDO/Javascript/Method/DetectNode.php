<?php
namespace GDO\Javascript\Method;

use GDO\Core\GDT_Response;
use GDO\Core\Website;
use GDO\Core\MethodAdmin;
use GDO\Util\Process;
use GDO\Javascript\Module_Javascript;
use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\Form\GDT_Submit;

/**
 * Auto-detect nodejs_path, uglifyjs_path and ng_annotate_path.
 * @author gizmore
 */
final class DetectNode extends MethodForm
{
	use MethodAdmin;
	
	public function getPermission() { return 'staff'; }
	public function showInSitemap() { return false; }
	public function getTitleLangKey() { return 'cfg_link_node_detect'; }
	
	public function createForm(GDT_Form $form)
	{
	    $form->info(t('info_detect_node_js'));
	    $form->actions()->addField(GDT_Submit::make());
	}
	
	public function formValidated(GDT_Form $form)
	{
		$response = $this->detectNodeJS();
		$response->addField($this->detectAnnotate());
		$response->addField($this->detectUglify());
		
		$url = href('Admin', 'Configure', '&module=Javascript');
		return $response->addField(Website::redirect($url, 12));
	}
	
	/**
	 * Detect node/nodejs binary and save to config.
	 * @return GDT_Response
	 */
	public function detectNodeJS()
	{
		$path = null;
		if ($path === null)
		{
			$path = Process::commandPath("nodejs");
		}
		if ($path === null)
		{
		    $path = Process::commandPath("node");
		}
		if ($path === null)
		{
			return $this->error('err_nodejs_not_found');
		}
		Module_Javascript::instance()->saveConfigVar('nodejs_path', $path);
		return $this->message('msg_nodejs_detected', [htmlspecialchars($path)]);
	}
	
	/**
	 * Detect node/nodejs binary and save to config.
	 * @return GDT_Response
	 */
	public function detectAnnotate()
	{
		$path = null;
		if ($path === null)
		{
		    $path = Process::commandPath("ng-annotate-patched", '.cmd');
		}
		if ($path === null)
		{
		    $path = Process::commandPath("ng-annotate", '.cmd');
		}
		if ($path === null)
		{
			return $this->error('err_annotate_not_found');
		}
		Module_Javascript::instance()->saveConfigVar('ng_annotate_path', $path);
		return $this->message('msg_annotate_detected', [htmlspecialchars($path)]);
	}
	
	/**
	 * Detect node/nodejs binary and save to config.
	 * @return GDT_Response
	 */
	public function detectUglify()
	{
		$path = null;
		if ($path === null)
		{
		    $path = Process::commandPath("uglify-js", '.cmd');
		}
		if ($path === null)
		{
		    $path = Process::commandPath("uglifyjs", '.cmd');
		}
		if ($path === null)
		{
		    $path = Process::commandPath("uglify", '.cmd');
		}
		if ($path === null)
		{
			return $this->error('err_uglify_not_found');
		}
		Module_Javascript::instance()->saveConfigVar('uglifyjs_path', $path);
		return $this->message('msg_uglify_detected', [htmlspecialchars($path)]);
	}
   
}
