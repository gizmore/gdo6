<?php
use GDO\Date\Time;
use GDO\Form\GDT_Form;
use GDO\UI\GDT_Divider;
use GDO\Form\GDT_Submit;
use GDO\Util\Numeric;
use GDO\Core\Module_Core;
/**
 * @var $form GDT_Form
 */
echo '<';echo '?';echo "php\n";
?>
###############################
### GDO6 Configuration File ###
###############################
if (defined('GDO_CONFIGURED')) return; // double include

/**
 * Please work down each section carefully.
 * Common pitfall is that there are 2 domains to set: GDO_DOMAIN and GDO_SESS_DOMAIN.
 * GDO <?=Module_Core::$GDO_REVISION; ?>
 **/

<?php
// $tz = $form->getField('timezone')->var;
$created = Time::getDate(microtime(true));
$form->getField('sitecreated')->var($created);
?>
<?php foreach ($form->fields as $field) : ?>
<?php
if ($field instanceof GDT_Divider)
{
	echo "\n";
	echo str_repeat('#', mb_strlen($field->displayLabel()) + 8) . "\n";
	echo "### {$field->displayLabel()} ###\n";
	echo str_repeat('#', mb_strlen($field->displayLabel()) + 8) . "\n";
}
elseif ($field instanceof GDT_Submit)
{
}
else
{
    $name = $field->name;
    
	$value = $field->getValue();
	if (is_string($value))
	{
		if ($name === 'chmod')
		{
			$value = "0".Numeric::baseConvert($value, 10, 8);
		}
		elseif ($name === 'error_level')
		{
		    $value = "0x".Numeric::baseConvert($value, 10, 16);
		}
		else
		{
			$value = "'$value'";
		}
	}
	elseif ($value === null)
	{
		$value = 'null';
	}
	elseif (is_array($value))
	{
		$value = implode(',', $value);
		$value = "'$value'";
	}
	elseif (is_bool($value))
	{
		$value = $value ? 'true' : 'false';
	}
	
	printf("define('GDO_%1\$s', env('GDO_%1\$s', %2\$s));\n", strtoupper($name), $value);
}
?>
<?php endforeach; ?>
