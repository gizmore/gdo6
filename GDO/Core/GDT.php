<?php
namespace GDO\Core;
use GDO\DB\Query;
use GDO\Table\GDT_Table;
use GDO\Util\Strings;
abstract class GDT
{
	use WithName;
	
	###############
	### Factory ###
	###############
	private static $nameNr = 1;
	public static function nextName() { return 'gdo-'.(self::$nameNr++); }
	/**
	 * Create a GDT instance.
	 * @param string $name
	 * @return self
	 */
	public static function make($name=null)
	{
		$type = get_called_class();
		$obj = new $type;
		return $obj->name($name);
	}
	
	############
	### Name ###
	############
	public $name;
	public function name($name=null) { $this->name = $name ? $name : self::nextName(); return $this; }
	public function htmlClass() { return " gdo-".strtolower(Strings::rsubstrFrom(get_called_class(), '\\')); }

	###########
	### RWE ###
	###########
	public $readable = true;
	public function readable($readable) { $this->readable = $readable; return $this; }
	public $writable = true;
	public function writable($writable) { $this->writable = $writable; return $this; }
	public $editable = true;
	public function editable($editable) { $this->editable = $editable; return $this->writable($editable); }
	
	#############
	### Error ###
	#############
	public $error;
	public function error($key, array $args=null) { return $this->rawError(t($key, $args)); }
	public function rawError($html=null) { $this->error = $html; return false; }
	public function hasError() { return is_string($this->error); }
	public function classError()
	{
		$class = ' '.str_replace('_', '-', strtolower($this->gdoShortName()));
		if ($this->isRequired()) $class .= ' gdo-required';
		if ($this->hasError()) $class .= ' gdo-has-error';
		return $class;
	}
	
	##############
	### Events ###
	##############
	public function gdoBeforeCreate() {}
	public function gdoBeforeUpdate(Query $query) {}
	public function gdoBeforeDelete() {}
	public function gdoAfterCreate() {}
	public function gdoAfterUpdate() {}
	public function gdoAfterDelete() {}
	
	#################
	### Var/Value ###
	#################
	/**
	 * @var GDO - Current row / gdo
	 */
	public $gdo;
	public $var;
	public $initial;
	public function gdo(GDO $gdo=null){ $this->gdo = $gdo; return $this->val($gdo->getVar($this->name)); }
	public function val($var=null) { $this->var = $var === null ? null : (string)$var; return $this; }
	public function value($value) { $this->var = $this->toVar($value); return $this; }
	public function toVar($value) { return $value === null ? null : (string) $value; }
	public function toValue($var) { return $var === null ? null : (string) $var; }
	public function getVar() { return $this->getRequestVar('form', $this->var); }
	public function getValue() { return $this->toValue($this->getVar()); }
	public function initial($var=null) { $this->initial = $var === null ? null : (string)$var; return $this->val($var); }
	public function initialValue($value) { return $this->initial($this->toVar($value)); }
	public function displayVar() { return html($this->getVar()); }
	public function displayJSON() { return json_encode($this->renderJSON()); }

	public function getFields() {}
	public function hasChanged() { return $this->initial !== $this->getVar(); }
	public function getValidationValue() { return $this->getValue(); }
	
	#################
	### GDO Value ###
	#################
	public function blankData() {}
	public function getGDOData() {}
	public function setGDOData(GDO $gdo=null) { return $this; }
	public function setGDOVar($var) { $this->gdo->setVar($this->name, $var); return $this; }
	public function setGDOValue($value) { return $this->setGDOVar($this->toVar($value)); }
	
	public function getRequestVar($firstLevel=null, $default=null, $name=null)
	{
	    $name = $name === null ? $this->name : $name;
	    $path = '';
	    if ($firstLevel)
	    {
	        if (!isset($_REQUEST[$firstLevel]))
	        {
	            return $default;
	        }
	        $path = $firstLevel.']';
	    }
	    $arr = $_REQUEST;
	    # Allow nested form checkboxes and stuff
	    $path .= '['.$name;
	    $path = explode('][', $path);
	    foreach ($path as $child)
	    {
	        $child = trim($child, '[]');
	        if (isset($arr[$child]))
	        {
	            $arr = $arr[$child];
	        }
	        else
	        {
	            return $default;
	        }
	    }
	    return $arr;
	}
	
	##############
	### Render ###
	##############
	public function render() { return $this->renderCell(); }
	public function renderCard() { return $this->renderDebug(); }
	public function renderCell() { return html($this->getVar()); }
	public function renderChoice() { return $this->render(); }
	public function renderFilter() {}
	public function renderForm() { return $this->render(); }
	public function renderHeader() {}
	public function renderJSON() {}
	public function renderList() { return $this->render(); }
	public function renderOrder() { return 'aa'; }

	# Render debug data by default.
	private function renderDebug() { return print_r($this, true); }
	
	################
	### Validate ###
	################
	public function isRequired() { return false; }
	public function errorNotNull() { return $this->error('err_not_null'); }
	public function onValidated() {}
	public function validate($value)
	{
		if ( ($value === null) && ($this->isRequired()) )
		{
			return $this->errorNotNull();
		}
		return true;
	}
	
	############
	### Sort ###
	############
	public function displayTableOrder(GDT_Table $table) {}
	private static $SORT_COLUMN;
	public function sort(array &$array, $ascending=true)
	{
		self::$SORT_COLUMN = $this;
		uasort($array, function(GDO $a, GDO $b) {
			return self::$SORT_COLUMN->gdoCompare($a, $b);
		});
		return $ascending ? $array : array_reverse($array, true);
	}
	
	public function gdoCompare(GDO $a, GDO $b)
	{
		return strcasecmp($a->getVar($this->name), $b->getVar($this->name));
	}
	
	##############
	### Filter ###
	##############
	public function filterValue() { return $this->getRequestVar('f'); }
	public function filterQuery(Query $query) {}
	public function filterGDO(GDO $gdo) {}
	
	################
	### Database ###
	################
	public $unique;
	public $primary;
	public function gdoColumnDefine() {}
}