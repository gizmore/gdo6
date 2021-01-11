<?php
namespace GDO\DB;

use GDO\Core\GDO;
use GDO\Core\GDOError;
use GDO\Core\GDT;
use GDO\Core\GDT_Template;

/**
 * You would expect this to be in GDT_Object,
 * but this is also mixed into GDT_ObjectSelect, hence it is a trait.
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.00
 * 
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
	
	public $composition = false;
	public function composition($composition=true) { $this->composition = $composition; return $this; }
	
	###################
	### Var / Value ###
	###################
	public function getVar()
	{
		$var = $this->getRequestVar($this->formVariable(), $this->var);
		return empty($var) ? null : $var;
	}

	/**
     * @return GDO
	 */
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
		if ($var !== null)
		{
			# Without javascript, convert the name input
			if (isset($_REQUEST['nocompletion_'.$this->name]))
			{
			 	unset($_REQUEST['nocompletion_'.$this->name]);
			 	if ($user = $this->findByName($var))
			 	{
			 		$_REQUEST[$this->formVariable()][$this->name] = $user->getID();
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
	 * @return GDO
	 */
	public function getGDO()
	{
		return $this->gdo;
	}
	
	public function displayVar()
	{
		if ($gdo = $this->getValue())
		{
			if ($column = $gdo->gdoNameColumn())
			{
				return html($column->getVar());
			}
			else
			{
			    return $gdo->getID();
			}
		}
	}
	
// 	/**
// 	 * @return \GDO\Core\GDO
// 	 */
// 	public function getObject()
// 	{
// 		return $this->getValue();
// 	}
	
	public function getGDOData()
	{
		if ($object = $this->getValue())
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
// 		    return [$this->name => $this->var ? $this->var : null]; # use value anyway
		    return [$this->name => $this->getVar()]; # bug in import tbs forum
		}
	}
	
	################
	### Validate ###
	################
	public function validate($value)
	{
		if ($value)
		{
			return true;
		}
		elseif ($var = $this->getVar())
		{
			return $this->error('err_gdo_not_found', [$this->table->gdoHumanName(), html($var)]);
		}
		elseif ($this->notNull)
		{
			return $this->errorNotNull();
		}
		else
		{
			return true;
		}
	}
	
	###############
	### Cascade ###
	###############
	public $cascade = 'SET NULL';

	public function cascade()
	{
		$this->cascade = 'CASCADE';
		return $this;
	}
	
	public function cascadeNull()
	{
		$this->cascade = 'SET NULL';
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
		return $this->notNull();
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
	public function renderFilter($f)
	{
		return GDT_Template::php('DB', 'filter/object.php', ['field' => $this, 'f' => $f]);
	}
	
	/**
	 * Proxy filter to the filterColumn.
	 * @see \GDO\DB\GDT_Int::filterQuery()
	 * @see \GDO\DB\GDT_String::filterQuery()
	 */
	public function filterQuery(Query $query, $rq=null)
	{
		if ($field = $this->filterField)
		{
		    $this->table->gdoColumn($field)->filterQuery($query, $rq);
			return $this;
		}
		else
		{
		    return parent::filterQuery($query, $rq);
		}
	}
	
	##############
	### Search ###
	##############
	/**
	 * Build huge quicksearch query.
	 * @param Query $query
	 * @param string $searchTerm
	 * @param boolean $first
	 * @return string
	 */
	public function searchQuery(Query $query, $searchTerm, $first)
	{
        $table = $this->foreignTable();
	    $nameT = GDO::escapeIdentifierS('t_' . $this->name);
	    
	    if ($first)
	    {
	        $name = GDO::escapeIdentifierS($this->name);
	        $fk = $table->gdoPrimaryKeyColumn()->name;
	        $fkI = GDO::escapeIdentifierS($fk);
	        $myT = $this->gdtTable->gdoTableName();
	        $query->join("LEFT JOIN {$table->gdoTableName()} {$nameT} ON {$myT}.{$name} = {$nameT}.{$fkI}");
	    }
	    
	    $where = [];
	    foreach ($table->gdoColumnsCache() as $gdt)
	    {
	        if ($gdt->searchable)
	        {
	            if ($condition = $gdt->searchCondition($searchTerm, $nameT))
	            {
	                $where[] = $condition;
	            }
	        }
	    }
	    return implode(' OR ', $where);
	}
	
}
