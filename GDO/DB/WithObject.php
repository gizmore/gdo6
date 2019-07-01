<?php
namespace GDO\DB;

use GDO\Core\GDO;
use GDO\Core\GDOError;
use GDO\Core\GDT;
use GDO\Core\GDT_Template;

/**
 * You would expect this to be GDT_Object,
 * but this is also mixed into GDT_ObjectSelect, hence it is a trait.
 * 
 * @author gizmore
 * @see GDT_Object
 * @see GDT_ObjectSelect
 */
trait WithObject
{
	###################
	### With Object ###
	###################
	/**
	 * @var GDO
	 */
	public $table;
	/**
	 * @param GDO $table
	 * @return self
	 */
	public function table(GDO $table) { $this->table = $table; return $this; }
	/**
	 * @return GDO
	 */
	public function foreignTable() { return $this->table; }
	
	###################
	### Var / Value ###
	###################
	public function getVar()
	{
		$var = $this->getRequestVar('form', $this->var);
		return empty($var) ? null : $var;
	}

	public function getValue()
	{
		return $this->toValue($this->getVar());
	}
	
	public function toVar($value)
	{
		return $value !== null ? $value->getID() : null;
	}
	
	public function toValue($var)
	{
		if (!empty($var))
		{
			# Without javascript, convert the name input
			if (isset($_REQUEST['nocompletion_'.$this->name]))
			{
			 	unset($_REQUEST['nocompletion_'.$this->name]);
			 	if ($user = $this->findByName($var))
			 	{
			 		$_REQUEST['form'][$this->name] = $user->getID();
					return $user;
			 	}
			}
			if ($user = $this->table->getById(...explode(':', $var)))
			{
				return $user;
			}
		}
	}
	
	/**
	 * Override this with a real byName method.
	 * @param string $name
	 * @return \GDO\Core\GDO
	 */
	public function findByName($name)
	{
		if ($column = $this->table->gdoNameColumn())
		{
			return $this->table->getBy($column->name, $name);
		}
	}
	
	/**
	 * @return \GDO\Core\GDO
	 */
	public function getObject()
	{
		return $this->getValue();
	}
	
	public function getGDOData()
	{
		if ($object = $this->getObject())
		{
			# Array for multiple select. ignore. 
			if (is_array($object))
			{
				return null ;
			}
			
			return [$this->name => $object->getID()];
		}
		else
		{
			return [$this->name => $this->getVar()];
		}
	}
	
	################
	### Validate ###
	################
	public function validate($value)
	{
		# Weird using getVar. but works with completion hack.
		if ($this->var)
		{
			return $value ? true : $this->error('err_gdo_not_found', [$this->table->gdoHumanName(), html($this->var)]);
		}
		return $this->notNull ? $this->errorNotNull() : true;
	}
	
	###############
	### Cascade ###
	###############
	public $cascade = 'SET NULL';
	public function cascadeNull()
	{
		$this->cascade = 'SET NULL';
		return $this;
	}
	
	public function cascade()
	{
		$this->cascade = 'CASCADE';
		return $this;
	}
	
	public function cascadeRestrict()
	{
		$this->cascade = 'RESTRICT';
		return $this;
	}
	
	/**
	 * If object columns are not null, they cascade upon deletion.
	 */
	public function notNull($notNull=true)
	{
		$this->notNull = $notNull;
		return $this->cascade();
	}
	
	/**
	 * If object columns are primary, they cascade upon deletion.
	 */
	public function primary($primary=true)
	{
		$this->primary = $primary;
		return $this->cascade();
	}
	
	########################
	### Custom ON clause ###
	########################
	public $fkOn;
	public function fkOn($on)
	{
		$this->fkOn = $on;
		return $this;
	}

	################
	### Database ###
	################
	/**
	 * Take the foreign key primary key definition and str_replace to convert to foreign key definition.
	 *
	 * {@inheritDoc}
	 * @see GDT::gdoColumnDefine()
	 */
	public function gdoColumnDefine()
	{
		if (!($table = $this->foreignTable()))
		{
			throw new GDOError('err_gdo_object_no_table', [$this->identifier()]);
		}
		$tableName = $table->gdoTableIdentifier();
		if (!($primaryKey = $table->gdoPrimaryKeyColumn()))
		{
			throw new GDOError('err_gdo_no_primary_key', [$tableName, $this->identifier()]);
		}
		$define = $primaryKey->gdoColumnDefine();
		$define = str_replace($primaryKey->identifier(), $this->identifier(), $define);
		$define = str_replace(' NOT NULL', '', $define);
		$define = str_replace(' PRIMARY KEY', '', $define);
		$define = str_replace(' AUTO_INCREMENT', '', $define);
		$define = preg_replace('#,FOREIGN KEY .* ON UPDATE (?:CASCADE|RESTRICT|SET NULL)#', '', $define);
		$on = $this->fkOn ? $this->fkOn : $primaryKey->identifier();
		return "$define{$this->gdoNullDefine()}".
			",FOREIGN KEY ({$this->identifier()}) REFERENCES $tableName($on) ON DELETE {$this->cascade} ON UPDATE {$this->cascade}";
	}

	##############
	### Filter ###
	##############
	public function renderFilter()
	{
		return GDT_Template::php('DB', 'filter/object.php', ['field'=>$this]);
	}
	
	/**
	 * Proxy filter to the filterColumn.
	 * {@inheritDoc}
	 * @see \GDO\DB\GDT_Int::filterQuery()
	 * @see \GDO\DB\GDT_String::filterQuery()
	 */
	public function filterQuery(Query $query)
	{
		if ($field = $this->filterField)
		{
			$this->table->gdoColumn($field)->filterQuery($query);
			return $this;
		}
		else
		{
			return parent::filterQuery($query);
		}
	}
	
}
