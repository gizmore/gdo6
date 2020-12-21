<?php
namespace GDO\Form;

use GDO\Core\GDO;
use GDO\Core\GDT_Response;
use GDO\Core\Method;
use GDO\Util\Common;
use GDO\File\GDT_File;
use GDO\Core\Application;

/**
 * Generic method that uses a GDT_Form.
 * @author gizmore
 * @since 6.00
 * @version 6.05
 */
abstract class MethodForm extends Method
{
	/**
	 * @var GDT_Form
	 */
	private $form;
	
	public function isTransactional() { return true; }
	
	public function isUserRequired() { return true; }
	
	public function formName() { return 'form'; }
	
	public abstract function createForm(GDT_Form $form);
	
	############
	### Shim ###
	############
	public function formParametersWithButton(array $params, $button)
	{
	    $params[$button] = 'submit';
	    return $this->formParameters($params);
	}
	
	public function formParameters(array $params=null)
	{
	    $form = $this->formName();
	    $_REQUEST[$form] = [];
	    if ($params)
	    {
    	    foreach ($params as $key => $var)
    	    {
    	        $_REQUEST[$form][$key] = $var;
    	    }
	    }
	    return $this;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \GDO\Core\Method::execute()
	 * @return \GDO\Core\GDT_Response
	 */
	public function execute()
	{
		$this->executeEditMethods();
		return $this->executeForm();
	}
	
	public function executeEditMethods()
	{
		if (count($_POST))
		{
			foreach ($this->getForm()->getFields() as $field)
			{
				if ($field instanceof GDT_File)
				{
					$key = 'delete_' . $field->name;
					if ( isset($_REQUEST[$key]) && (is_array($ids = Common::getRequestArray($key))) )
					{
						$field->onDeleteFiles(array_keys($ids));
					}
				}
			}
		}
	}
	
	/**
	 * Render this form as response.
	 * @return \GDO\Core\GDT_Response
	 */
	public function renderPage()
	{
	    return GDT_Response::makeWith($this->getForm());
	}
	
	public function defaultTitle()
	{
		$module = strtolower($this->getModuleName());
		$method = strtolower($this->getMethodName());
		return $this->title(t("ft_{$module}_{$method}"));
	}
	
	/**
	 * Validate the form and execute it.
	 * @return \GDO\Core\GDT_Response
	 */
	public function executeForm()
	{
	    $form = $this->getForm();
	    
	    $response = null;
		
		### Flow upload
		if ($flowField = Common::getRequestString('flowField'))
		{
		    /** @var $formField GDT_File **/
		    if ($formField = $form->getField($flowField))
		    {
    			return $formField->flowUpload();
		    }
		}
		
		### buttons
		foreach ($form->getFieldsRec() as $field)
		{
			if ($field instanceof GDT_Submit)
			{
				if (isset($_REQUEST[$this->formName()][$field->name]))
				{
					if ($form->validateForm())
					{
					    GDT_Form::$CURRENT = $form;
					    unset($_REQUEST['nojs']);
						$response = call_user_func([$this, "onSubmit_{$field->name}"], $form);
						$form->onValidated();
						GDT_Form::$CURRENT = null;
					}
					else
					{
						$response = $this->formInvalid($form)->add($this->renderPage());
					}
					break;
				}
			}
		}
	
		return $response ? $response : $this->renderPage();
	}
	
	/**
	 * @return \GDO\Form\GDT_Form
	 */
	public function getForm()
	{
		if (!isset($this->form))
		{
			$this->form = GDT_Form::make($this->formName());
			$this->defaultTitle();
			$this->createForm($this->form);
		}
		return $this->form;
	}
	
	public function resetForm()
	{
	    $form = $this->formName();
	    unset($_GET[$form]);
	    unset($_POST[$form]);
		unset($_REQUEST[$form]);
		unset($_GET['nojs']);
		unset($_POST['nojs']);
		unset($_REQUEST['nojs']);
		unset($this->form);
	}
	
	public function title($key=null, array $args=null)
	{
	    $this->getForm()->title($key, $args);
	    return parent::title($key, $args);
	}
	
	public function titleRaw($title)
	{
	    $this->getForm()->titleRaw($title);
	    return parent::titleRaw($title);
	}
	
	###
	public function onSubmit_submit(GDT_Form $form)
	{
		return $this->formValidated($form);
	}
	
   /**
	 * @param GDT_Form $form
	 * @return GDT_Response
	 */
	public function formValidated(GDT_Form $form)
	{
		return $this->message('msg_form_saved');
	}
	
	/**
	 * @param GDT_Form $form
	 * @return GDT_Response
	 */
	public function formInvalid(GDT_Form $form)
	{
		$error = $this->error('err_form_invalid');
		if (Application::instance()->isAjax() || Application::instance()->isCLI())
		{
			$error->addFields($form->getFields());
		}
		return $error;
	}
	
}
