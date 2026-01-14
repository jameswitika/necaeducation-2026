<?php
include("../wp-load.php");

$course_number = 'Grid Connect 28FEB2025';
$neca_member = true;

$choices = jrar_invoice_options2($course_number, $neca_member);

echo "Choices: <br/>";
var_dump($choices);
echo "<br/><br/>";

echo "-- END --";

// Invoice Options
function jrar_invoice_options2($course_number, $neca_member)
{
	$choices = array();

	$course = JRACourseOperations::loadJRACourseByCourseNumber($course_number);
	
	echo "Course: <br/>";
	var_dump($course);
	echo "<br/><br/>";
	
	foreach($course->invoice_options as $invoice_option)
	{
		
		echo "Invoice Option: <br/>";
		var_dump($invoice_option);
		echo "<br/><br/>";
		
		if($invoice_option->neca_member == $neca_member && $invoice_option->internal == false)
		{
			$choice = array(	'text'			=> $invoice_option->name,
								'value'			=> $invoice_option->name,
								'price'			=> $invoice_option->total,
								'isSelected'	=> (count($choices) == 0) ? true : false );
			array_push($choices, $choice);
		}
	}

	if(count($choices) == 0)
	{
		foreach($course->invoice_options as $invoice_option)
		{
			if($invoice_option->neca_member == false && $invoice_option->internal == false)
			{
				$choice = array(	'text'			=> $invoice_option->name,
									'value'			=> $invoice_option->name,
									'price'			=> $invoice_option->total,
									'isSelected'	=> (count($choices) == 0) ? true : false );
				array_push($choices, $choice);
			}
		}
	}
	
	return $choices;
}