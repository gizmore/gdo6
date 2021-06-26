<?php
namespace GDO\Core;

use GDO\DB\Query;
use GDO\Table\GDT_Table;
use GDO\Form\GDT_Form;
use GDO\DB\GDT_String;
use GDO\UI\WithIcon;
use GDO\Util\Strings;
use GDO\Form\GDT_Validator;
use GDO\DB\GDT_Int;
use GDO\Form\GDT_Select;
use GDO\Form\GDT_ComboBox;
use GDO\DB\GDT_Enum;

/**
 * Base class for all GDT.
 * 
 * To implement a new GDT inherit this class and override rendering methods and validation.
 * 
 * There are a few traits that offer features like completion, html attributes or tooltips.
 * Most GDT either are Database enabled (GDT_String, GDT_Int, GDT_Enum) or mostly used for rendering like (GDT_Title, GDT_Link, etc...)
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 6.0.0
 * 
 * @see \GDO\DB\GDT_Int - Database supporting integer baseclass
 * @see \GDO\DB\GDT_String - Database supporting string baseclass
 * @see \GDO\DB\GDT_Enum - Database supporting enum class
 * @see \GDO\UI\GDT_Paragraph - Simple text rendering
 * @see \GDO\Table\MethodQueryList - highest class in table methods.
 */
abstract class GDT
{
	use WithName;
	use WithIcon;
	
	# Same as $gdo but always set and always the table.
	/** @var $gdtTable GDO **/
	public $gdtTable;
	/** @var GDO **/
	public $gdo; # current row / gdo
	
	public $name; # html id
	public $var; # String representation of current var
	private $valueConverted = false;
	public $value; # Object representation of current var
	public $initial; # Initial var
	public $unique = false; # DB
	public $primary = false; # DB
	public $readable = false; # can see
	public $writable = false; # can change
	public $editable = false; # user can change
	public $hidden = false; # hide in tables, forms, lists and cards.
	public $notNull = false; # adds cascade when used with a primary key.
	public $orderable = false; # GDT_Table
	public $filterable = false; # GDT_Table
	public $searchable = false; # GDT_Table
	public $positional = null; # CLI
	public $focusable = false;
	public $cli = true;
	
	###############
	### Factory ###
	###############
	/**
	 * Make constructor private, so GDT::make() has to be used.
	 * This makes sure that we can safely put advanced init stuff in the make() function.
	 */
	protected function __construct() {}
	
	public static $DEBUG = GDO_GDT_DEBUG;
	public static $COUNT = 0; # Total GDT created

	public function hasName() { return $this->name !== null; }
	public function defaultName() {}
	
	/**
	 * Create a GDT instance.
	 * @param string $name
	 * @return static
	 */
	public static function make($name=null)
	{
	    self::$COUNT++;
	    if (self::$DEBUG)
	    {
	        self::logDebug();
	    }
		$obj = new static();
		return $obj->name($name ? $name : $obj->defaultName());
	}
	
	### stats
	public function __wakeup()
	{
	    self::$COUNT++;
	    if (self::$DEBUG)
	    {
	        self::logDebug();
	    }
	}
	
	private static function logDebug()
	{
	    Logger::log('gdt', sprintf('%d: %s', self::$COUNT, self::gdoClassNameS()));
	    if (self::$DEBUG >= 2)
	    {
	        Logger::log('gdt', Debug::backtrace('Backtrace', false));
	    }
	}
	
	############
	### Name ###
	############
	public function name($name=null) { $this->name = $name; return $this; }
    public function htmlClass() { return ' ' . strtolower($this->gdoShortName()); }
	
	##############
	### FormID ###
	##############
	public function id() { return (GDT_Form::$CURRENT?GDT_Form::$CURRENT->name."_":'').$this->name; }
	public function htmlID() { return $this->name ? sprintf('id="%s"', $this->id()) : ''; }
	public function htmlForID() { return $this->name ? sprintf('for="%s"', $this->id()) : ''; }
	
	###########
	### RWE ###
	###########
	public function readable($readable) { $this->readable = $readable; return $this; }
	public function writable($writable) { $this->writable = $this->editable = $writable; return $this; }
	public function editable($editable) { $this->editable = $editable; return $this->writable($editable); }
	public function hidden($hidden=true) { $this->hidden = $hidden; return $this;}

	#############
	### Error ###
	#############
	public $error;
	public function error($key, array $args=null, $code=405) { return $this->rawError(t($key, $args), $code); }
	public function rawError($html=null, $code=405) { if (!$this->error) $this->error = $html; GDT_Response::$CODE = $code;  return false; }
	public function hasError() { return is_string($this->error); }
	public function htmlError() { return $this->error ? ('<div class="gdo-form-error">' . $this->error . '</div>') : ''; }
	public function classError()
	{
	    $class = $this->htmlClass();
	    if ($this->notNull) $class .= ' gdo-required';
		if ($this->hasError()) $class .= ' gdo-has-error';
		return $class;
	}
	
	###################
	### CRUD Events ###
	###################
	public function gdoBeforeCreate(Query $query) {}
	public function gdoBeforeRead(Query $query) {}
	public function gdoBeforeUpdate(Query $query) {}
	public function gdoBeforeDelete(Query $query) {}
	public function gdoAfterCreate() {}
	public function gdoAfterRead() {}
	public function gdoAfterUpdate() {}
	public function gdoAfterDelete() {}
	
	#############
	### Table ###
	#############
	public function gdtTable(GDO $table) { $this->gdtTable = $table; return $this; }
	
	#################
	### Var/Value ###
	#################
	public function gdo(GDO $gdo=null)
	{
	    $this->gdo = $gdo;
	    if ($gdo)
	    {
	        if ($gdo->isTable())
	        {
                return $this->var($this->initial);
	        }
	        else
	        {
    	        return $this->setGDOData($gdo);
	        }
	    }
	    return $this;
	}
	
	/**
	 * Set the var.
	 * @param string $var
	 * @return self
	 */
	public function var($var=null)
	{
	    if ($this->var !== $var)
	    {
    	    $this->var = ($var === null) || ($var === '') ? null : (string)$var;
    	    $this->value = null;
    	    $this->valueConverted = false;
	    }
	    return $this;
	}
	
	/**
	 * Set the var via value. Converted via toVar($value).
	 * @param mixed $value
	 * @return self
	 */
	public function value($value)
	{
	    return $this->var($this->toVar($value));
	}
	
	/**
	 * Set var and value in one step. Do not recompute value.
	 * @param string $var
	 * @param mixed $value
	 * @return self
	 */
	public function varval($var, $value)
	{
	    $this->var = $var;
	    $this->value = $value;
	    $this->valueConverted = true;
	    return $this;
	}
	
	/**
	 * Convert the value to var.
	 * @param mixed $value
	 * @return string
	 */
	public function toVar($value)
	{
	    return ($value === null) || ($value === '') ?
	       null : (string)$value;
	}
	
	public function inputToVar($input) { return $input; }
	public function toValue($var) { return ($var === null) || ($var === '') ? null : (string) $var; }
	public function hasVar() { return !!$this->getVar(); }
	public function getVar() { return $this->var; }
	public function getParameterVar() { return $this->getRequestVar(null, $this->var); }
	public function getParameterValue() { return $this->toValue($this->getParameterVar()); }
	public function getValue()
	{
	    if (!$this->valueConverted)
	    {
	        $this->value = $this->toValue($this->var);
	        $this->valueConverted = true;
	    }
	    return $this->value;
	}
	
	public function getInitialValue()
	{
	    return $this->toValue($this->initial);
	}
	
	public function initial($var=null)
	{
	    $this->initial = $this->var = $var === null ? 
	       null : (string)$var;
	    $this->valueConverted = false;
	    return $this;
	}
	public function initialValue($value) { return $this->initial($this->toVar($value)); }
	public function displayVar() { return html($this->getVar()); }
	public function displayValue($var) { return html($var); }
	public function displayJSON() { return json_encode($this->renderJSON()); }

	public function getFields() {}
	public function hasChanged() { return $this->initial !== $this->getVar(); }
	public function getValidationValue()
	{
	    $this->getRequestVar($this->formVariable(), $this->var);
	    return $this->getValue();
	}
	
	public function isSerializable() { return false; }
	public function isPrimary() { return false; }
	
	###################
	### Form Naming ###
	###################
	public function formVariable()
	{
	    return GDT_Form::$CURRENT ?
	       GDT_Form::$CURRENT->name : null;
	}

	/**
	 * Get the form name for postvars baselvl dictionary.
	 * @return string
	 */
	public function formName()
	{
	    return GDT_Form::$CURRENT ?
    	    sprintf('%s[%s]', $this->formVariable(), $this->name) :
    	    $this->name;
	}
	
	/**
	 * Get the form name as html name attribute.
	 * @return string
	 */
	public function htmlFormName() { return sprintf(" name=\"%s\"", $this->formName()); }
	
	/**
	 * Check if this paramter is required.
	 * In CLI this is a positional parameter.
	 * @return boolean
	 */
	public function isPositional()
	{
	    if (is_bool($this->positional))
	    {
	        return $this->positional;
	    }
	    return $this->notNull && ($this->initial === null);
	}
	
	/**
	 * Force the positional state of a GDT for CLI args.
	 * @param boolean $positional
	 * @return self
	 */
	public function positional($positional=true)
	{
	    $this->positional = $positional;
	    return $this;
	}
	
	public function cli($cli)
	{
	    $this->cli = $cli;
	    return $this;
	}
	
	public function displayCLILabel()
	{
	    return strtolower(str_replace(' ', '_', $this->displayLabel()));
	}
	
	#################
	### GDO Value ###
	#################
	public function blankData() { return $this->name ? [$this->name => $this->var] : null; }
	public function getGDOData() {}
	public function setGDOVar($var) { if ($this->gdo) $this->gdo->setVar($this->name, $var); return $this; }
	public function setGDOValue($value) { return $this->setGDOVar($this->toVar($value)); }
	
	/**
	 * @param GDO $gdo
	 * @return GDT
	 */
	public function setGDOData(GDO $gdo)
	{
	    if ($gdo->isTable())
	    {
	        return $this->var($this->initial);
	    }
		elseif ($gdo->hasVar($this->name))
		{
		    return $this->var($gdo->getVar($this->name));
		}
		return $this;
	}
	
	
	/**
	 * Get a param for this GDT from $_REQUEST.
	 * $firstlevel can be like o1[o][field]
	 * $name hackery can be like iso][en][field.
	 * 
	 * $firstLevel usually is [form]
	 * 
	 * @param string $firstLevel
	 * @param string $default
	 * @param string $name
	 * 
	 * @return string
	 */
	public function getRequestVar($firstLevel=null, $default=null, $name=null)
	{
	    $old = $this->var;
	    $new = $this->_getRequestVar($firstLevel, $default, $name);
        $new = $this->toVar($this->toValue($new)); # fix bug!
	    if ($old !== $new)
	    {
	        $this->var($new);
	    }
	    return $new;
	}
	
	public function _getRequestVar($firstLevel=null, $default=null, $name=null)
	{
		$name = $name === null ? $this->name : $name;
		
		# Bring hackery in the firstlevel format
		if (strpos($name, ']'))
		{
		    $parts = explode('][', $name);
		    $name = array_pop($parts);
		    foreach ($parts as $part)
		    {
		        $firstLevel .= "[{$part}]";
		    }
		}
		
		$arr = $_REQUEST;
		
		# Eat firstlevel and build new
		if ($firstLevel)
		{
		    $next = Strings::substrTo($firstLevel, '[', $firstLevel);
	        $next = trim($next, '[]');
	        if (!isset($arr[$next]))
	        {
	            return $default;
	        }
	        $arr = $arr[$next];
	        $firstLevel = '[' . Strings::substrFrom($firstLevel, '[');
		}
		
		# Now we can iterate the $_REQUEST vars.
		while ($firstLevel)
		{
    		if (isset($arr[$name]))
    		{
    		    return $this->inputToVar($arr[$name]);
    		}
    		if (is_array($arr))
    		{
    		    $next = Strings::substrTo($firstLevel, ']');
    		    $next = ltrim($next, '[');
    		    if (!isset($arr[$next]))
    		    {
    		        return $default;
    		    }
    		    $arr = $arr[$next];
    		    $firstLevel = Strings::substrFrom($firstLevel, ']');
    		}
    		else
    		{
    		    break;
    		}
		}
		return isset($arr[$name]) ? $this->inputToVar($arr[$name]) : $default;
	}
	
	##############
	### Render ###
	##############
	public function render() { return Application::instance()->isCLI() ? $this->renderCLI() : $this->renderCell(); }
	public function renderCLI() { return $this->renderJSON(); }
	public function renderPDF() { return $this->renderCard(); }
	public function renderXML() { return $this->name ? sprintf('<%1$s>%2$s</%1$s>'.PHP_EOL, $this->name, html($this->getVar())) : ''; }
	public function renderJSON() { return $this->var; }
	public function renderCell() { return $this->renderCellSpan($this->getVar()); }
	public function renderCellSpan($var) { return sprintf('<span class="%s">%s</span>', $this->htmlClass(), html($var)); }
	public function renderCard() { return sprintf('<label>%s</label><span>%s</span>', $this->displayLabel(), $this->displayValue($this->getVar())); }
	public function renderList() { return $this->render(); }
	public function renderForm() { return $this->render(); }
	public function renderFilter($f) {}
	public function renderHeader() {}
	public function renderSidebar() { return $this->renderCell(); }
	public function renderChoice($choice) { return is_object($choice) ? $choice->renderChoice() : $choice; }
	
	public $labelArgs;
	public function labelArgs(...$labelArgs) { $this->labelArgs = $labelArgs; return $this; }
	public function displayLabel() { return t($this->name, $this->labelArgs); }
	
	# Render debug data by default.
	private function renderDebug() { return print_r($this, true); }
	
	################
	### Validate ###
	################
	public function notNull($notNull=true) { $this->notNull = $notNull; return $this; }
	public function errorNotNull() { return $this->error('err_not_null'); }
	public function errorNotFound() { return $this->error('err_not_found'); }
	public function onValidated() {}
	
	/**
	 * Validation is a great experience in GDO6.
	 * 
	 * Almost all GDT have a quite decent validator. There is also a GDT to top that; The GDT_Validator.
	 * This GDT parameterizes the target GDT to validate, the value to validate, and the form to check for related fields.
	 * To indicate an error return false. Please use $gdt->error() to make the field in question blink and noting your error description.
	 * 
	 * The GDT base class only has a validator algorithm for notNull checks. "You need to fill out this field."
	 * GDT_String takes care of almost 65% of the rest of the input validation. Regex, Lengths, Charset, NotNull, Uniqueness.
	 * The rest is datetime and numbers. Then you almost got all validations figured out for free by object orientated programming paradigms.
	 * Well, to indicate an error to the form, you call an error method on the faulty GDT and give it an error message; "Your input needs to be at least 2 chars in length.".
	 * The UI is indicating the faulty field. Animations possibly help in identifying the problem.
	 * 
	 * @example where the terms of service have to be clicked.
	 * @example $gdt = GDT_Form::$CURRENT->getField('tos'); $tos = $gdt->getVar() return $tos === true ? true : $gdt->error('You need to acknowledge this checkbox and read the privacy guidelines first.');
	 * @example return $value ? true : $gdt->error('err_tos_needs_to_be_truthy'); 
	 * 
	 * The target $gdt field is an argument as well as the $value and the $form, if you really have to add validation rules.
	 * 
	 * @see GDT_Int for the integer validator.
	 * @see GDT_String for the string validator.
	 * @see GDT_Validator which is needed rarely. An example is the IP check in Register. 
	 * @see GDT_Select
	 * @see GDT_ComboBox
	 * @see GDT_Enum
	 * 
	 * @param mixed $value
	 * @return boolean 
	 */
	public function validate($value)
	{
		if ( ($value === null) && $this->notNull)
		{
			return $this->errorNotNull();
		}
		return true;
	}
	
	public function plugVar()
	{
	    return null;
	}
	
	public function gdoExampleVars()
	{
	    return $this->gdoHumanName();
	}
	
	############
	### Sort ###
	############
	public function displayTableOrder(GDT_Table $table) {}
	public function sort(array &$array, $ascending=true)
	{
	    $_this = $this;
		uasort($array, function(GDO $a, GDO $b) use ($_this) {
			return $_this->gdoCompare($a, $b);
		});
		return $ascending ? $array : array_reverse($array, true);
	}
	
	public function gdoCompare(GDO $a, GDO $b)
	{
		return strcasecmp($a->getVar($this->name), $b->getVar($this->name));
	}
	
	#############
	### Order ###
	#############
	public function orderable($orderable=true) { $this->orderable = $orderable; return $this; }
	
	public $orderField;
	public function orderField($orderField) { $this->orderField = $orderField; return $this; }
	public function orderFieldName() { return $this->orderField ? $this->orderField : $this->name; }
	
	public $orderDefaultAsc = true;
	public function orderDefaultAsc($defaultAsc=true) { $this->orderDefaultAsc = $defaultAsc; return $this; }
	public function orderDefaultDesc($defaultDesc=true) { $this->orderDefaultAsc = !$defaultDesc; return $this; }

	public function orderVar($rq=null)
	{
	    return $this->getRequestVar("$rq[o]", $this->initial,
	        $this->filterField ? $this->filterField : $this->name);
	}
	
	##############
	### Filter ###
	##############
	public $searchField;
	public function searchable($searchable=true)
	{
	    $this->searchable = $searchable;
	    return $this;
	}
	
	public function filterable($filterable=true)
	{
	    $this->filterable = $filterable;
	    return $this;
	}
	
	public $filterField;
	public function filterField($filterField)
	{
	    $this->filterField = $filterField;
	    return $this->searchable();
	}

	public function filterVar($rq=null)
	{
	    return $this->inputToVar(
	        $this->_getRequestVar("{$rq}[f]", null, 
	           $this->filterField ? $this->filterField : $this->name));
	}
	
	/**
	 * Filter decorator function for database queries.
	 * @see GDT_String
	 */
	public function filterQuery(Query $query, $rq=null) {}

	/**
	 * Extend query with searching for a term. Used in quicksearch.
	 * Search looks in all searchable columns for an OR match.
	 * Objects JOIN their foreign tables during this.
	 * @param Query $query
	 * @param string $searchTerm
	 */
	public function searchQuery(Query $query, $searchTerm, $first) {}

	/**
	 * Build a search condition.
	 * @param string $searchTerm
	 */
	public function searchCondition($searchTerm, $fkTable=null) {}
	
	/**
	 * Filter for entities.
	 * @see GDT_String
	 * @param GDO $gdo
	 */
	public function filterGDO(GDO $gdo, $rq) {}
	
	/**
	 * Filter static result entities for table-wide search.
	 * @param string $searchTerm
	 * @return boolean
	 */
	public function searchGDO($searchTerm) { return false; }
	
	################
	### Database ###
	################
	public function gdoColumnDefine() {}
	
	##############
	### Config ###
	##############
	public function displayConfigJSON() { return json_quote(json_encode($this->configJSON(), JSON_PRETTY_PRINT)); }

	/**
	 * Expose all fields to JSON config.
	 * @return array
	 */
	public function configJSON()
	{
	    return [
	        'id' => $this->id(),
	        'name' => $this->name,
	        'type' => $this->gdoClassName(),
	        'var' => $this->getVar(),
	        'icon' => $this->icon,
	        'error' => $this->error,
	        'initial' => $this->initial,
	        'unique' => $this->unique,
	        'primary' => $this->primary,
	        'notNull' => $this->notNull,
	        'readable' => $this->readable,
	        'writable' => $this->writable,
	        'editable' => $this->editable,
	        'orderable' => $this->orderable,
	        'filterable' => $this->filterable,
	        'searchable' => $this->searchable,
	    ];
	}

	public function focusable($focusable)
	{
	    $this->focusable = $focusable;
	    return $this;
	}
}

if (GDT::$DEBUG)
{
    Logger::log('gdt', '--- NEW RUN ---');
    Logger::log('gdo', '--- NEW RUN ---');
}
