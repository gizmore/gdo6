<?php
namespace GDO\Core\Method;
use GDO\Core\GDT_Response;
use GDO\Core\Method;
use GDO\Core\Website;
use GDO\Core\MethodAdmin;
use GDO\Core\Module_Core;
use GDO\Util\Process;
/**
 * Auto-detect nodejs_path, uglifyjs_path and ng_annotate_path.
 * @author gizmore
 */
final class DetectNode extends Method
{
	use MethodAdmin;
	
	public function getPermission() { return 'staff'; }

	public function showInSitemap() { return false; }
	
	public function execute()
	{
		$response = $this->detectNodeJS();
		$response->add($this->detectAnnotate());
		$response->add($this->detectUglify());
		
		$url = href('Admin', 'Configure', '&module=Core');
		return $this->renderNavBar('GWF')->add($response)->add(Website::redirectMessage($url));
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
		Module_Core::instance()->saveConfigVar('nodejs_path', $path);
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
		    $path = Process::commandPath("ng-annotate", '.cmd');
		}
		if ($path === null)
		{
			return $this->error('err_annotate_not_found');
		}
		Module_Core::instance()->saveConfigVar('ng_annotate_path', $path);
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
		    $path = Process::commandPath("uglifyjs", '.cmd');
		}
		if ($path === null)
		{
			return $this->error('err_uglify_not_found');
		}
		Module_Core::instance()->saveConfigVar('uglifyjs_path', $path);
		return $this->message('msg_uglify_detected', [htmlspecialchars($path)]);
	}
	
}
