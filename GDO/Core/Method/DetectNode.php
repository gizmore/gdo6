<?php
namespace GDO\Core\Method;
use GDO\Core\GDT_Response;
use GDO\Core\Method;
use GDO\Core\Website;
use GDO\Core\MethodAdmin;
use GDO\Core\Module_Core;
/**
 * Auto-detect nodejs_path, uglifyjs_path and ng_annotate_path.
 * @author gizmore
 */
final class DetectNode extends Method
{
    use MethodAdmin;
    
	public function getPermission() { return 'staff'; }

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
			$output = []; $return = null;
			exec("which nodejs", $output, $return);
			if ($return === 0)
			{
				$path = realpath($output[0]);
			}
			
		}
		
		if ($path === null)
		{
			$output = []; $return = null;
			exec("which node", $output, $return);
			if ($return === 0)
			{
				$path = realpath($output[0]);
			}
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
			$output = []; $return = null;
			exec("which ng-annotate", $output, $return);
			if ($return === 0)
			{
				$path = realpath($output[0]);
			}
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
			$output = []; $return = null;
			exec("which uglifyjs", $output, $return);
			if ($return === 0)
			{
				$path = realpath($output[0]);
			}
		}
		
		if ($path === null)
		{
			return $this->error('err_uglify_not_found');
		}
		
		Module_Core::instance()->saveConfigVar('uglifyjs_path', $path);
		return $this->message('msg_uglify_detected', [htmlspecialchars($path)]);
	}
	
}
