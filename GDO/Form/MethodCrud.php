<?php
namespace GDO\Form;
use GDO\Core\Website;
use GDO\Core\GDOError;
use GDO\Core\GDO;
use GDO\User\PermissionException;
use GDO\Util\Common;
use GDO\File\GDT_File;
use GDO\Core\WithFields;
use GDO\Core\GDT;
/**
 * Abstract CReate|Update|Delete for a GDO.
 * @author gizmore
 * @since 5.0
 */
abstract class MethodCrud extends MethodForm
{
    const ERROR = 0;
    const CREATED = 1;
    const EDITED = 2;
    const DELETED = 3;
    
	/**
	 * @return GDO
	 */
	public abstract function gdoTable();
	
	public abstract function hrefList();
	
	public function isUserRequired() { return true; }
	
	public function canCreate(GDO $table) { return true; }
	public function canUpdate(GDO $gdo) { return true; }
	public function canDelete(GDO $gdo) { return true; }
	
	public function afterCreate(GDT_Form $form, GDO $gdo) {}
	public function afterUpdate(GDT_Form $form, GDO $gdo) {}
	public function afterDelete(GDT_Form $form, GDO $gdo) {}
	
	public function getCRUDID() { return Common::getRequestString('id'); }
	
	/**
	 * @var GDO
	 */
	protected $gdo;
	
	/**
	 * @var int
	 */
	protected $crudMode = self::ERROR;
	
	public function execute()
	{
	    $this->crudMode = self::CREATED;
	    $table = $this->gdoTable();
		if ($id = $this->getCRUDID())
		{
			$this->gdo = $table->find($id);
			$this->crudMode = self::EDITED;
			if (!$this->canUpdate($this->gdo))
			{
				throw new PermissionException('err_permission_update');
			}
			
			$this->executeEditMethods();
		}
		elseif (!$this->canCreate($table))
		{
		    throw new PermissionException('err_permission_create');
		}
		
		return parent::execute();
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
    	            if ( isset($_POST[$key]) && (is_array($ids = Common::getPostArray($key))) )
    	            {
    	                $this->onDeleteFiles($field, $ids);
    	            }
    	        }
    	    }
	    }
	}
	
	public function onDeleteFiles(GDT_File $gdoType, array $ids)
	{
	    if (!$gdoType->multiple)
	    {
    	    foreach ($ids as $id)
    	    {
    	        $this->gdo->saveVar($gdoType->name, null);
    	        
    	    }
    	    $gdoType->initial(null);
	    }
	}
	
	
	public function createForm(GDT_Form $form)
	{
		$table = $this->gdoTable();
		foreach ($table->gdoColumnsCache() as $gdoType)
		{
			if ($gdoType->editable)
			{
				$form->addField($gdoType);
			}
		}
		$this->createFormButtons($form);
	}
	
	public function createFormButtons(GDT_Form $form)
	{
		$form->addFields(array(
			GDT_Submit::make(),
			GDT_AntiCSRF::make()
		));
		if ($this->gdo && $this->canDelete($this->gdo))
		{
			$form->addField(GDT_Submit::make('delete')->icon('delete'));
		}
		
		$gdo = $this->gdo ? $this->gdo : $this->blank();
// 		if ($this->gdo)
// 		{
			$form->withGDOValuesFrom($gdo);
// 		}
		
		if ($this->gdo)
		{
			$this->title(t('ft_crud_update', [sitename(), $this->gdoTable()->gdoHumanName()]));
		}
		else
		{
			$this->blank();
		    $this->crudCreateTitle();
		}
	}
	
	private function blank()
	{
		$this->getForm()->withFields(function(GDT $gdoType){
			$gdoType->initial($gdoType->initial);
		});
	}
	
	protected function crudCreateTitle()
	{
	    $this->title(t('ft_crud_create', [sitename(), $this->gdoTable()->gdoHumanName()]));
	}
	
	##############
	### Bridge ###
	##############
	public function formValidated(GDT_Form $form)
	{
		$table = $this->gdoTable();
		return $this->gdo ? $this->onUpdate($form) : $this->onCreate($form);
	}
	
	public function onSubmit_delete(GDT_Form $form)
	{
		if (!$this->canDelete($this->gdo))
		{
			throw new GDOError('err_permission_delete');
		}
		return $this->onDelete($form);
	}
	
	###############
	### Actions ###
	###############
	public function onCreate(GDT_Form $form)
	{
		$table = $this->gdoTable();
		$gdo = $table->blank($form->getFormData())->insert();
		$this->resetForm();
		return
			$this->message('msg_crud_created', [$gdo->gdoClassName()])->
			addField($this->afterCreate($form, $gdo))->
			add(Website::redirectMessage($this->hrefList()));
	}
	
	public function onUpdate(GDT_Form $form)
	{
	    $this->gdo->saveVars($form->getFormData());
	    $this->resetForm();
		return
			$this->message('msg_crud_updated', [$this->gdo->gdoClassName()])->
			add($this->afterUpdate($form, $this->gdo))->
			add($this->renderPage());
	}
	
	public function onDelete(GDT_Form $form)
	{
	    $this->crudMode = self::DELETED;
		$this->gdo->delete();
		return $this->message('msg_crud_deleted', [$this->gdo->gdoClassName()])->
			add($this->afterDelete($form, $this->gdo))->
			add(Website::redirectMessage($this->hrefList()));
	}
}
