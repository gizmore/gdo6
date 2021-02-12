<?php
namespace GDO\Form;

use GDO\Core\GDOError;
use GDO\Core\GDO;
use GDO\User\PermissionException;
use GDO\Util\Common;
use GDO\User\GDO_User;
use GDO\Captcha\GDT_Captcha;
use GDO\DB\GDT_Object;
use GDO\DB\GDT_ObjectSelect;
use GDO\Core\GDT;
use GDO\DB\GDT_DeletedAt;
use GDO\DB\GDT_DeletedBy;
use GDO\Date\Time;
use GDO\Core\Website;

/**
 * Abstract Create|Update|Delete for a GDO.
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
	public function isCaptchaRequired() { return !GDO_User::current()->isMember(); }
	
	public function canCreate(GDO $table) { return true; }
	public function canUpdate(GDO $gdo) { return true; }
	public function canDelete(GDO $gdo) { return true; }
	
	public function beforeCreate(GDT_Form $form, GDO $gdo) {}
	public function beforeUpdate(GDT_Form $form, GDO $gdo) {}
	public function beforeDelete(GDT_Form $form, GDO $gdo) {}
	
	public function afterCreate(GDT_Form $form, GDO $gdo) {}
	public function afterUpdate(GDT_Form $form, GDO $gdo) {}
	public function afterDelete(GDT_Form $form, GDO $gdo) {}
	
	public function crudName() { return 'id'; }
	public function getCRUDID() { return Common::getRequestString($this->crudName()); }
	
	/**
	 * @var GDO
	 */
	protected $gdo;
	
	/**
	 * @var int
	 */
	protected $crudMode = self::ERROR;
	
	public function init()
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
	    }
	    elseif (!$this->canCreate($table))
	    {
	        throw new PermissionException('err_permission_create');
	    }
	}
	
// 	##############
// 	### Render ###
// 	##############
// 	public function renderPage()
// 	{
// 	    return parent::renderPage();
// 	}
	
	##############
	### Create ###
	##############
	public function createForm(GDT_Form $form)
	{
	    $table = $this->gdoTable();
	    $form->gdo($this->gdo);
		foreach ($table->gdoColumnsCache() as $gdt)
		{
			$this->createFormRec($form, $gdt->gdo($this->gdoTable()));
		}
		$this->createCaptcha($form);
		$this->createFormButtons($form);
	}
	
	public function createFormRec(GDT_Form $form, GDT $gdt)
	{
		if ($gdt->editable)
		{
			if ( ($gdt instanceof GDT_Object) ||
				 ($gdt instanceof GDT_ObjectSelect) )
			{
				if ($gdt->composition)
				{
					foreach ($gdt->table->gdoColumnsCache() as $gdt2)
					{
						$this->createFormRec($form, $gdt2);
					}
				}
				else
				{
// 				    $form->addField($gdt->table->gdoColumnCopy($gdt->name));
				    $form->addField($gdt);
				}
			}
			elseif (!$gdt->virtual)
			{
// 			    $form->addField($gdt->table->gdoColumnCopy($gdt->name));
			    $form->addField($gdt);
			}
		}
	}

	public function createCaptcha(GDT_Form $form)
	{
		if (module_enabled('Captcha'))
		{
			if ($this->isCaptchaRequired())
			{
				$form->addField(GDT_Captcha::make());
			}
		}
	}
	
	public function createFormButtons(GDT_Form $form)
	{
		$form->actions()->addField(GDT_Submit::make());

		$form->addFields(array(
			GDT_AntiCSRF::make()
		));

		if ($this->gdo && $this->canDelete($this->gdo))
		{
			$form->actions()->addField(GDT_DeleteButton::make());
		}
		
		if ($this->gdo)
		{
    	    $form->withGDOValuesFrom($this->gdo);
		    $this->crudEditTitle();
		}
		else
		{
		    $form->withGDOValuesFrom($this->gdoTable());
		    $this->crudCreateTitle();
		}
	}
	
	protected function crudCreateTitle()
	{
		$this->title(t('ft_crud_create', [$this->gdoTable()->gdoHumanName()]));
	}
	
	protected function crudEditTitle()
	{
		$this->title(t('ft_crud_update', [$this->gdoTable()->gdoHumanName()]));
	}
	
	##############
	### Bridge ###
	##############
	public function formValidated(GDT_Form $form)
	{
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
		$table = $this->gdoTable(); # object table
		$data = $form->getFormData();
		$gdo = $table->blank($data); # object with files gdt
		$this->beforeCreate($form, $gdo);
		$gdo->insert();
        Website::redirectMessage('msg_crud_created', [$gdo->gdoHumanName()], $this->href('&'.$this->crudName().'='.$gdo->getID()));
        return $this->afterCreate($form, $gdo);
	}
	
	public function onUpdate(GDT_Form $form)
	{
	    $this->beforeUpdate($form, $this->gdo);
		$this->gdo->saveVars($form->getFormData());
		$this->message('msg_crud_updated', [$this->gdo->gdoHumanName()]);
		return $this->afterUpdate($form, $this->gdo);
	}
	
	public function onDelete(GDT_Form $form)
	{
		$this->crudMode = self::DELETED;
		
		$this->beforeDelete($form, $this->gdo);
		
		# Mark deleted
		if ($delAt = $this->gdo->gdoColumnOf(GDT_DeletedAt::class))
		{
		    $this->gdo->setVar($delAt->name, Time::getDate());
		    if ($delBy = $this->gdo->gdoColumnOf(GDT_DeletedBy::class))
		    {
		        $this->gdo->setVar($delBy->name, GDO_User::current()->getID());
		    }
		    $this->gdo->save();
		}
		else # Really delete
		{
    		$this->gdo->delete();
		}
		
		$this->gdo->table()->clearCache();
		$this->message('msg_crud_deleted', [$this->gdo->gdoHumanName()]);
		return $this->afterDelete($form, $this->gdo);
	}
	
}
