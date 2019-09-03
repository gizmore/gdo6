<?php
namespace GDO\Form;
use GDO\Core\GDT_Response;
use GDO\Core\Method;
use GDO\Util\Common;
use GDO\File\GDT_File;
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
	
	public abstract function createForm(GDT_Form $form);
	
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
		$response = null;
		$form = $this->getForm();
		if ($flowField = Common::getRequestString('flowField'))
		{
			return $form->getField($flowField)->flowUpload();
		}
		
		foreach ($form->getFieldsRec() as $field)
		{
			if ($field instanceof GDT_Submit)
			{
				if (isset($_POST[$field->name]))
				{
					if ($form->validateForm())
					{
						unset($_POST['nojs']);
						$response = call_user_func([$this, "onSubmit_{$field->name}"], $form);
						$form->onValidated();
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
			$this->form = GDT_Form::make();
			$this->defaultTitle();
			$this->createForm($this->form);
		}
		return $this->form;
	}
	
	public function resetForm()
	{
		unset($_GET['form']);
		unset($_POST['form']);
		unset($_POST['nojs']);
		unset($_REQUEST['form']);
		unset($this->form);
	}
	
	public function title($title=null)
	{
		$this->getForm()->title($title);
		return parent::title($title);
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
		return $this->error('err_form_invalid');
	}
	
}
