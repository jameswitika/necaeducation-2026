<?php
/* ************** *
 * PRE-PROCESSING *
 * ************** */

// Job Ready Application (GTO)
add_filter("gform_pre_render_" . JOB_REGISTRATION_FORM, 'job_ready_application_form_prepopulate');
add_filter("gform_pre_validation_" . JOB_REGISTRATION_FORM, 'job_ready_application_form_prepopulate');
add_filter("gform_pre_submission_filter_" . JOB_REGISTRATION_FORM, 'job_ready_application_form_prepopulate');
add_filter("gform_admin_pre_render" . JOB_REGISTRATION_FORM, 'job_ready_application_form_prepopulate');

function job_ready_application_form_prepopulate($form)
{
	if(JR_DEBUG_MODE)
	{
		echo "Prefill: <br/>";
		var_dump($_SESSION['prefill']);
		echo "<br/><br/>";
	}
	
	if(isset($_SESSION['prefill']))
	{
		$prefill_fields['4'] = $_SESSION['prefill']->first_name;
		$prefill_fields['5'] = $_SESSION['prefill']->middle_name;
		$prefill_fields['6'] = $_SESSION['prefill']->surname;
		$prefill_fields['7'] = $_SESSION['prefill']->known_by;

		$prefill_fields['79'] = $_SESSION['prefill']->street_address1;
		$prefill_fields['9'] = $_SESSION['prefill']->suburb;
		$prefill_fields['10'] = $_SESSION['prefill']->state;
		$prefill_fields['11'] = $_SESSION['prefill']->postcode;
		
		$prefill_fields['80'] = $_SESSION['prefill']->mobile_phone;
		$prefill_fields['81'] = $_SESSION['prefill']->home_phone;

		/*
		$prefill_fields['14'] = array(	'14' => $_SESSION['prefill']->email,
										'14.2' => $_SESSION['prefill']->email );
		*/
		
		$prefill_fields['15'] = $_SESSION['prefill']->birth_date;
		
		$prefill_fields['16'] = $_SESSION['prefill']->citizenship_status;
		$prefill_fields['17'] = $_SESSION['prefill']->indigenous_status;
		$prefill_fields['18'] = $_SESSION['prefill']->how_did_you_hear;
		$prefill_fields['19'] = $_SESSION['prefill']->how_did_you_hear_other;
		
		$prefill_fields['23'] = $_SESSION['prefill']->applying_for;
		
		if($_SESSION['prefill']->applying_for== 'Electrical Apprenticeship')
		{
			$prefill_fields['26'] = $_SESSION['prefill']->highest_school_level;
		}
		
		if($_SESSION['prefill']->applying_for == 'Trainee / Other')
		{
			$prefill_fields['39'] = $_SESSION['prefill']->highest_school_level;
		}
	}
	
	// Loops through form fields
	foreach($form["fields"] as &$field)
	{
		// Qualification Year Selection
		if($field->id == 34 || $field->id == 50 || $field->id == 54 || $field->id == 51 || $field->id == 56 || $field->id == 52 || $field->id == 58)
		{
			$years = get_years_for_select();
			$field->choices = $years;
		}
					
		// Set predefined values if person logged in
		if(isset($prefill_fields))
		{
			if(array_key_exists($field->id, $prefill_fields))
			{
				$prefill_value = $prefill_fields[$field->id];
				
				// Multiple values to be used to pre-populate
				if(is_array($prefill_value))
				{
					$new_inputs = array();
					foreach($field->inputs as $input)
					{
						$input_id = $input['id'];
						$input['defaultValue'] = $prefill_value[$input_id];
						array_push($new_inputs, $input);
					}
					$field->inputs = $new_inputs;
				}
				else
				{
					$field->defaultValue = $prefill_fields[$field->id];
				}
			}
		}
	}
	
	return $form;
	
}



/* *************** *
 * PRE-SUBMISSIONS *
 * *************** */
// Job Ready Application (GTO)
add_action( "gform_pre_submission_" . JOB_REGISTRATION_FORM, 'job_ready_application_form_presubmission');

function job_ready_application_form_presubmission()
{
	if(JR_DEBUG_MODE)
	{
		echo "POST Variables: <br/>";
		var_dump($_POST);
		echo "<br/><br/>";
	}
	
	if(isset($_SESSION['prefill']))
	{
		$prefill = $_SESSION['prefill'];
	}
	else
	{
		$prefill = new stdClass();
	}
	
	$prefill->first_name = $_POST['input_4'];
	$prefill->middle_name = $_POST['input_5'];
	$prefill->surname = $_POST['input_6'];
	$prefill->known_by = $_POST['input_7'];
	
	$prefill->street_address1 = $_POST['input_79'];
	$prefill->suburb = $_POST['input_9'];
	$prefill->state = $_POST['input_10'];
	$prefill->postcode = $_POST['input_11'];
	
	$prefill->mobile_phone = $_POST['input_80'];
	$prefill->home_phone = $_POST['input_81'];
	$prefill->email = $_POST['input_14'];
	
	$prefill->birth_date = $_POST['input_15'];
	$prefill->citizenship_status = $_POST['input_16'];
	$prefill->indigenous_status = $_POST['input_17'];
	$prefill->how_did_you_hear = $_POST['input_18'];
	$prefill->how_did_you_hear_other = $_POST['input_19'];
	
	$prefill->applying_for = $_POST['input_23'];
	
	if($prefill->applying_for== 'Electrical Apprenticeship')
	{
		$prefill->highest_school_level = $_POST['input_26'];
	}
	
	if($prefill->applying_for == 'Trainee / Other')
	{
		$prefill->highest_school_level = $_POST['input_39'];
	}
	
	// Set some default values for other forms
	$prefill->country_of_birth = 'Australia';
	$prefill->main_language = 'English';
	
	$_SESSION['prefill'] = $prefill;
	
	if(JR_DEBUG_MODE)
	{
		echo "Prefill set: <br/>";
		var_dump($_SESSION['prefill']);
		echo "<br/><br/>";
	}
}



/* *************** *
 * POST-PROCESSING *
 * *************** */

// Calls the fuction "job_ready_application_form_submission_process" after "Form #18: 2017 Job Ready Application Form" has been submitted
add_action ( 'gform_after_submission_' . JOB_REGISTRATION_FORM, 'job_ready_application_form_submission_process', 10, 2 );

function job_ready_application_form_submission_process($entry, $form_data)
{
	if (JR_DEBUG_MODE)
	{
		echo "Entry: <br/>";
		var_dump ( $entry );
		echo "<br/><br/>";
		
		// echo "Form Data: <br/>";
		// var_dump($form_data);
		// echo "<br/><br/>";
	}
	
	// Gravity Form
	$gform_id = $entry ['id'];
	$gform_form_id = $entry ['form_id'];
	
	$JRG = new JobReadyGTO ();
	$JRG->txtFirstName = ucwords ( strtolower ( rgar ( $entry, '4' ) ) );
	$JRG->txtMiddleName = ucwords ( strtolower ( rgar ( $entry, '5' ) ) );
	$JRG->txtSurname = ucwords ( strtolower ( rgar ( $entry, '6' ) ) );
	$JRG->txtPreferredName = ucwords ( strtolower ( rgar ( $entry, '7' ) ) );
	$JRG->txtAddress = ucwords ( strtolower ( rgar ( $entry, '79' ) ) );
	$JRG->txtSuburb = ucwords ( strtolower ( rgar ( $entry, '9' ) ) );
	$JRG->drpState = ucwords ( strtolower ( rgar ( $entry, '10' ) ) );
	$JRG->txtPostcode = rgar ( $entry, '11' );
	$JRG->txtMobilePhone = rgar ( $entry, '80' );
	$JRG->txtMobilePhone = str_replace ( ' ', '', $JRG->txtMobilePhone );
	$JRG->txtHomePhone = rgar ( $entry, '81' );
	$JRG->txtEmail = strtolower ( rgar ( $entry, '14' ) );
	
	// Birth Date
	$dob_raw = rgar ( $entry, '15' );
	$dob = date_create_from_format ( 'Y-m-d', $dob_raw );
	$JRG->txtDateOfBirth = date_format ( $dob, 'j F Y' );
	
	$JRG->rdoResidencyStatusList = rgar ( $entry, '16' );
	$JRG->rdoAboriginalTorresStraitIslandDescent = rgar ( $entry, '17' );
	$JRG->rdoHeardAboutUs = ucwords ( strtolower ( rgar ( $entry, '18' ) ) );
	$JRG->txtHeardAboutUsOther = ucwords ( strtolower ( rgar ( $entry, '19' ) ) );
	$JRG->have_you_attended_our_open_day = rgar ( $entry, '85' ); // Previously: have-you-attended-our-open-day
	$JRG->txtJobRef = rgar ( $entry, '22' );
	$JRG->txtSiteCode = rgar ( $entry, '23' );
	
	// Electrical Apprenticeship
	$JRG->va_drpHighestQualification = rgar ( $entry, '26' );
	$JRG->va_rdoPreviouslyEmployed = rgar ( $entry, '27' );
	$JRG->va_rdoPreviouslyCompleted = rgar ( $entry, '28' );
	$JRG->va_drpCurrentlyEnrolled = rgar ( $entry, '29' );
	$JRG->va_rdoPreApprenticeship = rgar ( $entry, '76' );
	$JRG->va_drpCurrentlyStudyingCompletionMonth = rgar ( $entry, '33' );
	$JRG->va_drpCurrentlyStudyingCompletionYear = rgar ( $entry, '34' );
	$JRG->va_rdoNECAAptitudeTest = rgar ( $entry, '35' );
	$JRG->va_txtNECAAptitudeTestScore = rgar ( $entry, '36' );
	
	// Trainee / Other
	$JRG->x370t_drpHighestQualification = rgar ( $entry, '39' );
	$JRG->x370t_rdoPreviouslyEmployed = rgar ( $entry, '40' );
	$JRG->x370t_rdoPreviouslyCompleted = rgar ( $entry, '41' );
	$JRG->x370t_rdoOtherQualifications = rgar ( $entry, '42' );
	$JRG->x370t_txtCertificateIIIName = ucwords ( strtolower ( rgar ( $entry, '44' ) ) );
	$JRG->x370t_drpCertificateIIIStartMonth = rgar ( $entry, '45' );
	$JRG->x370t_drpCertificateIIIStartYear = rgar ( $entry, '50' );
	$JRG->x370t_drpCertificateIIIEndMonth = rgar ( $entry, '53' );
	$JRG->x370t_drpCertificateIIIEndYear = rgar ( $entry, '54' );
	$JRG->x370t_txtCertificateIVName = ucwords ( strtolower ( rgar ( $entry, '47' ) ) );
	$JRG->x370t_drpCertificateIVStartMonth = rgar ( $entry, '49' );
	$JRG->x370t_drpCertificateIVStartYear = rgar ( $entry, '51' );
	$JRG->x370t_drpCertificateIVEndMonth = rgar ( $entry, '55' );
	$JRG->x370t_drpCertificateIVEndYear = rgar ( $entry, '56' );
	$JRG->x370t_txtDiplomaDegree = ucwords ( strtolower ( rgar ( $entry, '46' ) ) );
	$JRG->x370t_drpDiplomaDegreeStartMonth = rgar ( $entry, '48' );
	$JRG->x370t_drpDiplomaDegreeStartYear = rgar ( $entry, '52' );
	$JRG->x370t_drpDiplomaDegreeEndMonth = rgar ( $entry, '57' );
	$JRG->x370t_drpDiplomaDegreeEndYear = rgar ( $entry, '58' );
	
	// File uploads
	$JRG->fileCoverLetter = rgar ( $entry, '61' );
	$JRG->fileResume = rgar ( $entry, '62' );
	
	$JRG->cbPositionDescription = rgar ( $entry, '65.1' );
	$JRG->cbInjuryOrDisease = rgar ( $entry, '67.1' );
	$JRG->cbDiscloseToThirdParties = rgar ( $entry, '70.1' );
	$JRG->cbDeclarationAgreement = rgar ( $entry, '71.1' );
	
	foreach ( $JRG as $k => $v ) 
	{
		// Fix issue with Please Select appearing in form values
		if ($v == '- Please Select -') 
		{
			$JRG->$k = '';
		}
	}
	
	if (JR_DEBUG_MODE) 
	{
		echo "JRG: <br/>";
		var_dump ( $JRG );
		echo "<br/><br/>";
	}
	
	$fields = array ();
	$site_code = '';
	$job_ref = '';
	
	// Add Create Date
	$now = DateTime::createFromFormat ( 'U', current_time ( 'timestamp' ) );
	$fields ['registration_date'] = date_format ( $now, 'Y-m-d' );
	
	foreach ( $JRG as $k => $v ) 
	{
		if ($k == 'txtJobRef') 
		{
			$job_ref = $v;
		}
		
		if ($k == 'txtSiteCode') 
		{
			$site_code = $v;
		}
		
		if (strpos ( $k, 'rdo' ) !== false || strpos ( $k, 'txt' ) !== false || strpos ( $k, 'drp' ) !== false) 
		{
			if ($site_code == '370T' && strpos ( $k, 'va_' ) !== false) 
			{
				// Don't add to field array
			}
			elseif ($site_code == 'VA' && strpos ( $k, '370t_' ) !== false) 
			{
				// Don't add to field array
			}
			else 
			{
				$fields [$k] = $v;
			}
		}
		
		// File Handling
		if (strpos ( $k, 'file' ) !== false) 
		{
			$url = $v; // $v stores the 'uploads module' get file url
			
			if (strpos ( $url, 'http' ) !== false) 
			{
				$fields [$k] = $url;
			}
		}
	}
	
	if (JR_DEBUG_MODE) 
	{
		$default_date = date_default_timezone_get ();
		echo "The current server timezone is: " . $default_date . "<br/><br/>";
		
		$fields_string = http_build_query ( $fields );
		
		echo "Fields String: <br/>";
		var_dump ( $fields_string );
		echo "<br/><br/>";
	}
	
	// Pulls the Job Ready Server URL from the /includes/settings.php
	$url = JOB_READY_APPLICATION_URL;
	
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_HEADER, 0 );
	curl_setopt ( $ch, CURLOPT_VERBOSE, 0 );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_POST, 1 );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields ); // Passes the fields as an array
	// curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields) ); // Passes the fields as a string
	
	$response = curl_exec ( $ch );
	
	if (JR_DEBUG_MODE) 
	{
		echo "CURL Response: <br/>";
		var_dump ( $response );
		echo "<br/><br/>";
	}
}




/* ************* *
 * MISCELLANEOUS *
 * ************* */

// Job Registration custom javascript
// Only loads this javascript for the JOB_REGISTRATION_FORM
add_filter("gform_init_scripts_footer", "init_scripts");
function init_scripts()
{
	return true;
}

// GRAVITY FORM: 18 - Job Ready Application (GTO)
// Disables the "job reference" dynamically populated field
// Only loads this javascript for form 5: Job Registration
add_action( 'gform_enqueue_scripts_'.JOB_REGISTRATION_FORM, 'job_ready_application_custom_js', 10, 2 );

function job_ready_application_custom_js()
{
	?>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            $("input#input_5_22.medium").attr("readonly", "readonly");
        });
    </script>
<?php
}


 
 
/* ************** *
 * GOOGLE RELATED *
 * ************** */

// Add Job Ready Application Google Tracking
add_action( 'gform_post_paging_' . JOB_REGISTRATION_FORM, 'job_ready_application_google_tracking', 10, 3 );

function job_ready_application_google_tracking( $form, $source_page_number, $current_page_number )
{
	?>
	<script>
		function track_ga_pageview(page, title)
		{
			console.log('Google Analytics Page View');
			ga(	'send', 'pageview', {
				'page': page,
				'title': title });
		}
	</script>
	<?php
	
	if ( $current_page_number == 1 )
	{
		?>
        <script type="text/javascript">
        	track_ga_pageview('/job-application-form/step1/', 'Job Application Form - Step 1');
        </script>
        <?php
    }
    if ( $current_page_number == 2 || $current_page_number == 3)
    {
    	?>
        <script type="text/javascript">
        	track_ga_pageview('/job-application-form/step2/', 'Job Application Form - Step 2');
        </script>
        <?php
    }
    if ( $current_page_number == 4 )
    {
    	?>
        <script type="text/javascript">
        	track_ga_pageview('/job-application-form/step3/', 'Job Application Form - Step 3');
        </script>
        <?php
    }
    if ( $current_page_number == 5 )
    {
    	?>
        <script type="text/javascript">
        	track_ga_pageview('/job-application-form/step4/', 'Job Application Form - Step 4');
        </script>
        <?php
    }
    if ( $current_page_number == 6 )
    {
    	?>
        <script type="text/javascript">
        	track_ga_pageview('/job-application-form/step5/', 'Job Application Form - Step 5');
        </script>
        <?php
    }
    
}

