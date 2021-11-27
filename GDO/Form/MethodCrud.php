<?php
namespace GDO\Form;

use GDO\Core\GDO;
use GDO\User\PermissionException;
use GDO\User\GDO_User;
use GDO\Captcha\GDT_Captcha;
use GDO\DB\GDT_Object;
use GDO\DB\GDT_ObjectSelect;
use GDO\Core\GDT;
use GDO\DB\GDT_DeletedAt;
use GDO\DB\GDT_DeletedBy;
use GDO\Date\Time;
use GDO\Core\Website;
use GDO\Util\Common;
use GDO\DB\GDT_CreatedBy;
use GDO\Core\Application;

/**
 * Abstract Create|Update|Delete for a GDO using MethodForm.
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 5.1.0
 */
abstract class MethodCrud extends MethodForm
{
    # modes
	const ERROR = 0;
	const CREATED = 1;
	const READ = 2;
	const EDITED = 3;
	const DELETED = 4;
	
	/**
	 * The gdo to edit
	 * @var GDO
	 */
	protected $gdo;
	
	/**
	 * mode
	 * @var int
	 */
	protected $crudMode = self::ERROR;
	
	/**
	 * The GDO table to operate on.
	 * @return GDO
	 */
	public abstract function gdoTable();
	
	/**
	 * @return string href
	 * @see href()
	 */
	public abstract function hrefList();
	
	################
	### Override ###
	################
	public function isUserRequired() { return true; }
	public function isCaptchaRequired() { return !GDO_User::current()->isMember(); }
	public function showInSitemap() { return false; }
	
	public function canRead(GDO $gdo)
	{
	    return true;
	}
	
	public function canCreate(GDO $table)
	{
	    $user = GDO_User::current();
	    if ($user->isMember())
	    {
	        return true;
	    }
	    if ($user->isAuthenticated())
	    {
	        return $this->isGuestAllowed();
	    }
	    return false;
	}
	
	public function canUpdate(GDO $gdo)
	{
	    $user = GDO_User::current();
	    if ($gdt = $gdo->gdoColumnOf(GDT_CreatedBy::class))
	    {
	        if ($user === $gdt->getValue())
	        {
	            return true;
	        }
	    }
	    if ($user->isStaff())
	    {
	        return true;
	    }
	    return false;
	}
	
	public function canDelete(GDO $gdo)
	{
	    return $this->canUpdate($gdo);
	}
	
	public function beforeCreate(GDT_Form $form, GDO $gdo) {}
	public function beforeUpdate(GDT_Form $form, GDO $gdo) {}
	public function beforeDelete(GDT_Form $form, GDO $gdo) {}
	
	public function afterCreate(GDT_Form $form, GDO $gdo) {}
	public function afterUpdate(GDT_Form $form, GDO $gdo) {}
	public function afterDelete(GDT_Form $form, GDO $gdo) {}

	/**
	 * The parameter name for the GDO id column(s)
	 * @return string
	 */
	public function crudName() { return 'id'; }
	public function getCRUDID()
	{
	    return Common::getRequestString($this->crudName());
	}

	##############
	### Method ###
	##############
	public function gdoParameters()
	{
	    $p = [
	        GDT_Object::make($this->crudName())->table($this->gdoTable())->positional(),
	    ];
	    return array_merge($p, parent::gdoParameters());
	}
	
	public function init()
	{
	    $this->crudMode = self::CREATED;
	    $table = $this->gdoTable();
	    if ($id = $this->getCRUDID())
	    {
	        $this->gdo = $table->find($id);
	        $this->crudMode = self::EDITED;
	        if (!$this->canRead($this->gdo))
	        {
	            throw new PermissionException('err_permission_read');
	        }
	        elseif (!$this->canUpdate($this->gdo))
	        {
	            $this->crudMode = self::READ;
	        }
	        else
	        {
	            $this->crudMode = self::EDITED;
	        }
// 	        if (!$this->canUpdate($this->gdo))
// 	        {
// 	            throw new PermissionException('err_permission_update');
// 	        }
	    }
	    elseif (!$this->canCreate($table))
	    {
	        throw new PermissionException('err_permission_create');
	    }
	    
	    $this->getForm();
// 	    $this->resetForm();
	}
	
	##############
	### Create ###
	##############
	public function createForm(GDT_Form $form)
	{
	    $table = $this->gdoTable();
	    $form->gdo($this->gdo);
		foreach ($table->gdoColumnsCopyExcept() as $gdt)
		{
		    $gdo = $this->gdo ? $this->gdo : $table;
	        $this->createFormRec($form, $gdt->gdo($gdo));
		}
		$this->createCaptcha($form);
		$this->createFormButtons($form);
	}
	
	public function createFormRec(GDT_Form $form, GDT $gdt)
	{
		if ($gdt->editable)
		{
	        $gdt->writable = $this->crudMode !== self::READ;

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
				    $form->addField($gdt);
				}
			}
			elseif (!$gdt->virtual)
			{
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
		$form->addField(GDT_AntiCSRF::make());
		
		if (!$this->gdo)
		{
		    $form->actions()->addField(GDT_Submit::make('create')->label('btn_create')->icon('create'));
		}

		if ($this->gdo && $this->canUpdate($this->gdo))
		{
    		$form->actions()->addField(GDT_Submit::make('edit')->label('btn_edit')->icon('edit'));
		}

		if ($this->gdo && $this->canDelete($this->gdo))
		{
			$form->actions()->addField(GDT_DeleteButton::make());
		}
		
		if ($this->gdo)
		{
    	    $form->withGDOValuesFrom($this->gdo);
		}
		else
		{
		    $form->withGDOValuesFrom($this->gdoTable());
		}
	}
	
	public function getTitle()
	{
	    return $this->gdo ? $this->getUpdateTitle() : $this->getCreateTitle();
	}
	
	protected function getCreateTitle()
	{
		return t('ft_crud_create', [$this->gdoTable()->gdoHumanName()]);
	}
	
	protected function getUpdateTitle()
	{
        return t('ft_crud_update', [$this->gdo->gdoHumanName()]);
	}
	
	##############
	### Bridge ###
	##############
	public function formValidated(GDT_Form $form)
	{
		return $this->renderPage();
	}

	public function onSubmit_create(GDT_Form $form)
	{
// 	    if (!$this->canCreate($this->gdoTable()))
// 	    {
// 	        throw new GDOError('err_permission_create');
// 	    }
	    return $this->onCreate($form);
	}
	
	public function onSubmit_edit(GDT_Form $form)
	{
// 	    if (!$this->canUpdate($this->gdo))
// 	    {
// 	        throw new GDOError('err_permission_update');
// 	    }
	    return $this->onUpdate($form);
	}
	
	public function onSubmit_delete(GDT_Form $form)
	{
// 		if (!$this->canDelete($this->gdo))
// 		{
// 			throw new GDOError('err_permission_delete');
// 		}
		return $this->onDelete($form);
	}
	
	####################
	### CRUD Actions ###
	####################
	public function onCreate(GDT_Form $form)
	{
		$table = $this->gdoTable(); # object table
		$data = $form->getFormData();
		$gdo = $table->blank($data); # object with files gdt
		$this->beforeCreate($form, $gdo);
		$gdo->insert();
        Website::redirectMessage('msg_crud_created',
            [$gdo->gdoHumanName(), $gdo->getID()],
            $this->href('&'.$this->crudName().'='.$gdo->getID()));
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
		    $this->gdo->setVar($delAt->name, Application::$MICROTIME);
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
		$this->messageRedirect('msg_crud_deleted', [$this->gdo->gdoHumanName()], $this->hrefList());
		return $this->afterDelete($form, $this->gdo);
	}
	
}
