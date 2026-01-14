<?php
if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

// Validates the "school completion year"
add_filter( 'gform_field_validation_' . SHORT_COURSE_APPLICATION_FORM_ACCREDITED . '_49', 'completion_year_validation', 10, 4 );
//add_filter( 'gform_field_validation_' . APPRENTICE_APPLICATION_FORM . '_105', 'completion_year_validation', 10, 4 );
//add_filter( 'gform_field_validation_' . PROJECT_MANAGEMENT_CERTIV_APPLICATION_FORM . '_105', 'completion_year_validation', 10, 4 );
add_filter( 'gform_field_validation_' . PROJECT_MANAGEMENT_DIPLOMA_APPLICATION_FORM . '_105', 'completion_year_validation', 10, 4 );
add_filter( 'gform_field_validation_' . PRE_APPRENTICE_APPLICATION_FORM. '_49', 'completion_year_validation', 10, 4 );

function completion_year_validation($result, $value, $form, $field)
{
	$completion_year = intval($value);
	$year_check = ((int) date('Y')) - intval($value);
	
	if ( $result['is_valid'] && $year_check < 0 && $year_check > 100 )
	{
		$result['is_valid'] = false;
		$result['message'] = 'Please enter a valid year (no future year and less than 100 years ago)';
	}
	return $result;
}


// Validates the "VSN number"
add_filter( 'gform_field_validation_' . SHORT_COURSE_APPLICATION_FORM_ACCREDITED . '_56', 'vsn_validation', 10, 4 );
//add_filter( 'gform_field_validation_' . APPRENTICE_APPLICATION_FORM . '_56', 'vsn_validation', 10, 4 );
//add_filter( 'gform_field_validation_' . PROJECT_MANAGEMENT_CERTIV_APPLICATION_FORM . '_56', 'vsn_validation', 10, 4 );
add_filter( 'gform_field_validation_' . PROJECT_MANAGEMENT_DIPLOMA_APPLICATION_FORM . '_56', 'vsn_validation', 10, 4 );
add_filter( 'gform_field_validation_' . PRE_APPRENTICE_APPLICATION_FORM. '_56', 'vsn_validation', 10, 4 );

function vsn_validation($result, $value, $form, $field)
{
	// Reproduces the Job Ready VSN validation
	// victorian_student_number.scan(/./).collect(&:to_i).zip(weights).map { |d, w| d * w }.sum.multiple_of?(11)
	$vsn_valid = validate_vsn($value);
	
	// If VSN is not valid, mark it as invalid
	if ( $result['is_valid'] && $vsn_valid == false )
	{
		$result['is_valid'] = false;
		$result['message'] = 'Please enter a valid VSN number or leave blank if unsure';
	}
	return $result;
}

add_filter( 'gform_field_validation_' . JOB_REGISTRATION_FORM . '_15', 'dob_validation', 10, 4 );
add_filter( 'gform_field_validation_' . PROJECT_MANAGEMENT_DIPLOMA_APPLICATION_FORM . '_11', 'dob_validation', 10, 4 );
add_filter( 'gform_field_validation_' . SHORT_COURSE_APPLICATION_FORM_ACCREDITED . '_11', 'dob_validation', 10, 4 );
add_filter( 'gform_field_validation_' . SHORT_COURSE_APPLICATION_FORM_NON_ACCREDITED . '_11', 'dob_validation', 10, 4 );
add_filter( 'gform_field_validation_' . PRE_APPRENTICE_APPLICATION_FORM . '_11', 'dob_validation', 10, 4 );
add_filter( 'gform_field_validation_' . UEE30820_APPLICATION_FORM . '_11', 'dob_validation', 10, 4 );
//add_filter( 'gform_field_validation_' . PROJECT_MANAGEMENT_CERTIV_APPLICATION_FORM . '_11', 'dob_validation', 10, 4 );
//add_filter( 'gform_field_validation_' . SHORT_COURSE_APPLICATION_NECCLV004 . '_11', 'dob_validation', 10, 4 );

function dob_validation( $result, $value, $form, $field )
{
	if ( $result['is_valid'] )
	{
		if ( is_array( $value ) ) {
			$value = array_values( $value );
		}
		
		$date_value = GFFormsModel::prepare_date( $field->dateFormat, $value );
		
		$today = new DateTime();
		$diff  = $today->diff( new DateTime( $date_value ) );
		$age   = $diff->y;
		
		if ( $age < 14 || $age > 120 )
		{
			$result['is_valid'] = false;
			$result['message']  = 'Please enter a valid Birth Date';
		}
	}
	
	return $result;
}



function validate_vsn($vsn)
{
	if(count($vsn) > 0)
	{
		$vsn_valid = false;
		$weights = array(1,4,3,7,5,8,6,9,10);
		
		// Convert VSN string to an array of integers
		$vsn_array= vsn_scan_and_collect($vsn);
	// 	echo "VSN Array: <br/>";
	// 	var_dump($vsn_array);
	// 	echo "<br/><br/>";
		
		// Returns an array of mapped values - 1st value from vsn array x 1st value from weights, etc)
		$vsn_map = array_map("vsn_map", $vsn_array, $weights);
	// 	echo "VSN Map: <br/>";
	// 	var_dump($vsn_map);
	// 	echo "<br/><br/>";
		
		// Calculates the total sum of the $vsn_map array
		$vsn_sum = array_sum($vsn_map);
	// 	echo "VSN Sum: <br/>";
	// 	var_dump($vsn_sum);
	// 	echo "<br/><br/>";
		
		// Check if the number is divisible by 11
		if($vsn_sum % 11 == 0)
		{
			$vsn_valid = true;
		}
	// 	echo "VSN Valid: " . $vsn_valid . "<br/>";
		
		return $vsn_valid;
	}
	else
	{
		return true;
	}
}

function vsn_map($d, $w)
{
	return $d*$w;
}

function vsn_scan_and_collect($theString)
{
	$j = mb_strlen($theString);
	for ($k = 0; $k < $j; $k++)
	{
		$char = mb_substr($theString, $k, 1);
		$vsn_array[$k] =  intval($char);
	}
	return $vsn_array;
}

?>