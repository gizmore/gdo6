<?php
namespace GDO\Form;

use GDO\Core\GDO;
use GDO\Core\GDT_Response;
use GDO\Core\Method;
use GDO\Util\Common;
use GDO\File\GDT_File;
use GDO\Core\Application;
use GDO\Core\Website;

/**
 * Generic method that uses a GDT_Form.
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 6.0.0
 */
abstract class MethodForm extends Method
{
	/**
	 * @var GDT_Form
	 */
	private $form;
	
	protected $pressedButton = null;
	
	public function isTransactional() { return true; }
	
	public function isUserRequired() { return true; }
	
	public function formName() { return 'form'; }
	
	public abstract function createForm(GDT_Form $form);
	
	public function allParameters()
	{
	    return array_merge(
	        $this->gdoParameterCache(),
	        $this->getForm()->getFieldsRec());
	}
	
	############
	### Shim ###
	############
	public function formParametersWithButton(array $params)
	{
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
    	        $_REQUEST[$key] = $var;
    	        $_REQUEST[$form][$key] = $var;
    	    }
	    }
	    return $this;
	}
	
	public function requestParameters(array $params=null)
	{
	    $this->formParametersWithButton($params);
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
					if ( isset($_REQUEST[$this->formName()][$key]) && (is_array($ids = Common::getRequestArray($key))) )
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
		foreach ($form->actions()->getFieldsRec() as $field)
		{
			if ($field instanceof GDT_Submit)
			{
				if (isset($_REQUEST[$this->formName()][$field->name]))
				{
				    $this->pressedButton = $field->name;
				    
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
						$response = $this->formInvalid($form)->addField($this->renderPage());
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
			$this->form = GDT_Form::make($this->formName())->
			    titleRaw($this->getTitle())->
			    action($this->methodHref());
			$this->createForm($this->form);
		}
		return $this->form;
	}
	
	public function resetForm()
	{
// 	    $form = $this->formName();
// 	    unset($_GET[$form]);
// 	    unset($_POST[$form]);
// 		unset($_REQUEST[$form]);
// 		unset($_GET['nojs']);
// 		unset($_POST['nojs']);
// 		unset($_REQUEST['nojs']);
		unset($this->form);
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
	    $app = Application::instance();
	    if ($app->isCLI())
	    {
// 		    $this->getForm()->error = true;
		    return GDT_Response::make();
	    }
		if ($app->isAjax())
		{
		    return $this->error('err_form_invalid');
		}
		else
		{
		    return $this->error('err_form_invalid');
		}
	}
	
	/**
	 * Output a success creation message.
	 * Redirect via $redirect=href or $redirect=true.
	 * 
	 * @since 6.10.4
	 * @param GDO $gdo
	 * @param mixed $redirect
	 * @return \GDO\Core\GDT_Response
	 */
	public function messageCreated(GDO $gdo, $redirect=false, $time=0)
	{
	    if ($redirect)
	    {
	        if ($redirect === true)
	        {
	            $redirect = null;
	        }
	        Website::redirectMessage('msg_crud_created', [
	            $gdo->gdoHumanName(), $gdo->getID()], $redirect, $time);
	    }
	    return $this->message('msg_crud_created', [
	        $gdo->gdoHumanName(), $gdo->getID()]);
	}
	
	###########
	### SEO ###
	###########
	public function getTitleLangKey()
	{
	    return strtolower('ft_'.$this->getModuleName().'_'.$this->getMethodName());
	}
	
	###########
	### CLI ###
	###########
	public function renderCLIHelp()
	{
	    return $this->getForm()->renderCLIHelp($this);
	}
	
	public function getButtons()
	{
	    return $this->getForm()->actions()->getFieldsRec();
	}
	
}
