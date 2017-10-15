<?php
namespace GDO\Form;
use GDO\Core\GDT_Response;
use GDO\Core\Method;
use GDO\Util\Common;
use GDO\Core\GDT;
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
        return $this->executeForm();
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
        return $this->title(t("ft_{$module}_{$method}", [sitename()]));
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
        
        foreach ($form->fields as $field)
        {
            if ($field instanceof GDT_Submit)
            {
                if (isset($_POST[$field->name]))
                {
                    if ($form->validateForm())
                    {
                     	unset($_POST['nojs']);
                    	$response = call_user_func([$this, "onSubmit_{$field->name}"], $form);
                        $form->formValidated();
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
     * @return GDT_Form
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
        $this->form->withFields(function(GDT $field){
            $field->var = null;
        });
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
