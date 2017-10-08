<?php
namespace GDO\DB;
use GDO\Core\GDO;
use GDO\Core\GDOError;
use GDO\Core\GDT;
/**
 * You would expect this to be GDT_Object,
 * but this is also mixed into GDT_ObjectSelect.
 * @author gizmore
 * @see GDT_Object
 * @see GDT_ObjectSelect
 */
trait WithObject
{
    ###################
    ### With Object ###
    ###################
    public $table;
    public function table(GDO $table) { $this->table = $table; return $this; }
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
            if (isset($_POST['nojs']))
            {
                return $this->findByName($var);
            }
            return $this->table->findById(...explode(':', $var));
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
        return $this->table->findById($name);
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
            return [$this->name => $object->getID()];
        }
    }
    
    ################
    ### Validate ###
    ################
    public function validate($value)
    {
        if ($value === null)
        {
            if (null !== ($var = $this->getVar()))
            {
                return $this->error('err_gdo_not_found', [$this->table->gdoHumanName(), html($var)]);
            }
            elseif ($this->isRequired())
            {
                return $this->errorNotNull();
            }
        }
        return true;
    }
    
    ###############
    ### Cascade ###
    ###############
    public $cascade = 'CASCADE';
    public function cascadeNull()
    {
        $this->notNull = false;
        $this->cascade = 'SET NULL';
        return $this;
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
        $define = preg_replace('#,FOREIGN KEY .* ON UPDATE CASCADE#', '', $define);
        $on = $this->fkOn ? $this->fkOn : $primaryKey->identifier();
        return "$define{$this->gdoNullDefine()}".
            ",FOREIGN KEY ({$this->identifier()}) REFERENCES $tableName($on) ON DELETE {$this->cascade} ON UPDATE CASCADE";
    }

}
