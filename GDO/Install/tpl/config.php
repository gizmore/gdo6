<?php
use GDO\Form\GDT_Form;
use GDO\UI\GDT_Divider;
use GDO\Form\GDT_Submit;
use GDO\Util\Numeric;
$form instanceof GDT_Form;
echo '<';echo '?';echo "php\n";
?>
###############################
### GDO6 Configuration File ###
###############################
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
	$value = $field->getValue();
	if (is_string($value))
	{
		if ($field->name === 'chmod')
		{
			$value = "0".Numeric::baseConvert($value, 10, 8);
		}
		elseif ($field->name === 'error_level')
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
	printf("define('GWF_%s', %s);\n", strtoupper($field->name), $value);
}
?>
<?php endforeach; ?>
