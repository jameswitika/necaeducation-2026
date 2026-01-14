<?php
/* ************** *
 * PRE-PROCESSING *
 * ************** */

// Non-Accredited Application Form (NASC)
add_filter("gform_pre_render_" . SHORT_COURSE_APPLICATION_FORM_NON_ACCREDITED, 'short_course_non_accredited_application_form_prepopulate');
add_filter("gform_pre_validation_" . SHORT_COURSE_APPLICATION_FORM_NON_ACCREDITED, 'short_course_non_accredited_application_form_prepopulate');
add_filter("gform_pre_submission_filter_" . SHORT_COURSE_APPLICATION_FORM_NON_ACCREDITED, 'short_course_non_accredited_application_form_prepopulate');
add_filter("gform_admin_pre_render" . SHORT_COURSE_APPLICATION_FORM_NON_ACCREDITED, 'short_course_non_accredited_application_form_prepopulate');

function short_course_non_accredited_application_form_prepopulate($form)
{
	$prefill_fields= array();
	
	if(NASC_DEBUG_MODE)
	{
		echo "Prefill: <br/>";
		var_dump($_SESSION['prefill']);
		echo "<br/><br/>";
	}
	
	// Set NECA Member to FALSE initially. This will be updated if the member is logged in and are a NECA Member
	// This flag is used later to determine whether to charge NECA Member fee or standard fee
	$neca_member = false;
	
	if(isset($_GET['course_scope_code']))
	{
		$course_scope_code = $_GET['course_scope_code'];
		$get_default_course_scope_code = false;
	}
	else
	{
		$get_default_course_scope_code = true;
	}
	
	// Get the Course Number (from the URL variable or the default field value)
	if(isset($_GET['course_number']))
	{
		$course_number = $_GET['course_number'];
		$get_default_course_number = false;
	}
	else
	{
		$get_default_course_number = true;
	}
	
	if($get_default_course_number || $get_default_course_scope_code)
	{
		// Loop through Form Fields to retrieve the course number
		foreach($form['fields'] as $field)
		{
			if($field->inputName == 'course_number')
			{
				$course_number = $field->defaultValue;
			}
			
			if($field->inputName == 'course_scope_code')
			{
				$course_scope_code = $field->defaultValue;
			}
		}
	}
	
	// Load JobReadyDate
	$jrd =JobReadyDateOperations::loadJobReadyDateByCourseNumber($course_number);
	
	// Check if there no more enrolment spots remaining
	if(strpos($course_number, 'Holding Bay') === false && $jrd->enrolments_remaining <= 0 )
	{
		// Exit or Redirect
		$form['limitEntries'] = true;
		$form['limitEntriesCount'] = 0;
		$form['limitEntriesMessage'] = "There are no more enrolment spots left for this course date. Please select another date.";
		// error_log('ffs_non_accredited_application_form - limit entries');
		// error_log('jrd->enrolments_remaining: ' . $jrd->enrolments_remaining);
		return $form;
	}
	
	if(isset($_SESSION['prefill']))
	{
		$prefill_fields['2'] = $_SESSION['prefill']->title;
		$prefill_fields['9'] = $_SESSION['prefill']->first_name;
		//$prefill_fields['28'] = $_SESSION['prefill']->middle_name;
		$prefill_fields['8'] = $_SESSION['prefill']->surname;
		$prefill_fields['10'] = $_SESSION['prefill']->known_by;
		$prefill_fields['11'] = $_SESSION['prefill']->birth_date;
		$prefill_fields['27'] = $_SESSION['prefill']->gender;
		// 2024.02.28 - Replaced street address1 with with street number and street name
		//$prefill_fields['30'] = $_SESSION['prefill']->street_address1;
		$prefill_fields['64'] = $_SESSION['prefill']->street_number;
		$prefill_fields['30'] = $_SESSION['prefill']->street_name;
		$prefill_fields['31'] = $_SESSION['prefill']->suburb;
		$prefill_fields['32'] = $_SESSION['prefill']->state;
		$prefill_fields['33'] = $_SESSION['prefill']->postcode;
		
		//$prefill_fields['43'] = $_SESSION['prefill']->postal_address_same;
		// 2024.02.28 - Replaced postal_street address1 with with street number and street name
		//$prefill_fields['44'] = $_SESSION['prefill']->postal_street_address1;
		$prefill_fields['65'] = $_SESSION['prefill']->postal_street_number;
		$prefill_fields['44'] = $_SESSION['prefill']->postal_street_name;
		
		$prefill_fields['47'] = $_SESSION['prefill']->postal_suburb;
		$prefill_fields['48'] = $_SESSION['prefill']->postal_state;
		$prefill_fields['46'] = $_SESSION['prefill']->postal_postcode;
		
		$prefill_fields['20'] = $_SESSION['prefill']->home_phone;
		$prefill_fields['19'] = $_SESSION['prefill']->mobile_phone;
		
		/*
		$prefill_fields['21'] = array(	'21' => $_SESSION['prefill']->email,
				'21.2' => $_SESSION['prefill']->email );
		*/
		
		$prefill_fields['58'] = $_SESSION['prefill']->emergency_contact_firstname;
		$prefill_fields['59'] = $_SESSION['prefill']->emergency_contact_surname;
		$prefill_fields['60'] = $_SESSION['prefill']->emergency_contact_number;
		$prefill_fields['61'] = $_SESSION['prefill']->emergency_contact_relationship;
	}
	
	// If the Employee Party Login session exists, load the Party from Job Ready and confirm it is valid by comparing it to the Employee Party ID (ID) also stored in session
	if(isset($_SESSION['employee_party_login']))
	{
		// Load the party by login
		$employee_party_xml_object = JRAPartyOperations::getJRAPartyByLogin( $_SESSION['employee_party_login'] );
		
		// Confirms a valid response from Job Ready
		if($employee_party_xml_object!== false)
		{
			// Confirms the Party ID matches the ID return by the LOGIN
			if($_SESSION['employee_party_id'] == $employee_party_xml_object->{'party'}->{'id'})
			{
				// Convert XMLObject to JRAParty
				$form_fields = JobReadyFormOperations::convertToJobReadyForm($employee_party_xml_object);
				
				$prefill_fields['2'] = $form_fields->title;
				$prefill_fields['9'] = $form_fields->first_name;
				//$prefill_fields['28'] = $form_fields->middle_name;
				$prefill_fields['8'] = $form_fields->surname;
				$prefill_fields['10'] = $form_fields->known_by;
				$prefill_fields['11'] = $form_fields->birth_date;
				$prefill_fields['27'] = $form_fields->gender;
				//$prefill_fields['30'] = $form_fields->street_address1;
				$prefill_fields['64'] = $form_fields->street_number;
				$prefill_fields['30'] = $form_fields->street_name;
				$prefill_fields['31'] = $form_fields->suburb;
				$prefill_fields['32'] = $form_fields->state;
				$prefill_fields['33'] = $form_fields->postcode;
				
				//$prefill_fields['43'] = $form_fields->postal_address_same;
				//$prefill_fields['44'] = $form_fields->postal_street_address1;
				$prefill_fields['65'] = $form_fields->postal_street_number;
				$prefill_fields['44'] = $form_fields->postal_street_name;
				$prefill_fields['47'] = $form_fields->postal_suburb;
				$prefill_fields['48'] = $form_fields->postal_state;
				$prefill_fields['46'] = $form_fields->postal_postcode;
				
				$prefill_fields['20'] = $form_fields->home_phone;
				$prefill_fields['19'] = $form_fields->mobile_phone;
				
				/*
				$prefill_fields['21'] = array(	'21' => $form_fields->email,
						'21.2' => $form_fields->email );
				*/
				
				$neca_member = $form_fields->neca_member == 'true' ? true : false;
			}
			else
			{
				unset($employee_party_xml_object);
			}
		}
	}
	
	
	// If the Party Login session exists and the Party Type is an "Employer", loads the Party from Job Ready and confirm it is valid by comparing it to the Party ID also stored in session
	// Checks if the EMPLOYER is a NECA Member and overrides the "prefill" employee record
	if(isset($_SESSION['party_login']) && isset($_SESSION['party_type']) && $_SESSION['party_type'] == 'Employer')
	{
		// Load the party by login
		$party_xml_object = JRAPartyOperations::getJRAPartyByLogin( $_SESSION['party_login'] );
		
		// Confirms a valid response from Job Ready
		if($party_xml_object !== false)
		{
			// Confirms the Party ID matches the ID return by the LOGIN
			if($_SESSION['party_id'] == $party_xml_object->{'party'}->{'id'})
			{
				// Convert XMLObject to JRAParty
				$form_fields = JobReadyFormOperations::convertToJobReadyForm($party_xml_object);
				
				$neca_member = $form_fields->neca_member == 'true' ? true : false;
			}
			else
			{
				unset($party_xml_object);
			}
		}
	}
	
	
	// Loops through form fields
	foreach($form["fields"] as &$field)
	{
		// Cost
		if($field->id == 26)
		{
			// If course is "LEP", "SWP" or "LET" then we need to let the user select their preferred choice
			// Retrieve the costs from Job Ready and display them in a drop down list for selection
			if($course_scope_code == "LEP" || $course_scope_code == "SWP" || $course_scope_code == "LET")
			{
				$choices = jrar_invoice_options($course_number, $neca_member);
			}
			else
			{
				$choices = jrar_invoice_options($course_number, $neca_member);
			}
			
			$field->choices = $choices;
		}
		
		// Title
		if($field->id == 2)
		{
			$field->choices = jrar_title();
		}
		
		// Gender
		if($field->id == 27)
		{
			$field->choices = jrar_gender();
		}
		
		// States
		if($field->id == 32 || $field->id == 48)
		{
			$field->choices = jrar_state();
		}
		
		// Course Cost (update label to be course name and date)
		if($field->id == 29)
		{
			$total_label = $jrd->course_name . " (" . $jrd->start_date_clean. " to " . $jrd->end_date_clean . ")";
			if($course_scope_code == "LEP" || $course_scope_code == "SWP" || $course_scope_code == "LET")
			{
				$field->label = 'Cost: ';
			}
			else
			{
				$field->label = $total_label;
			}
		}
		
		// Set predefined values if person logged in
		if(isset($prefill_fields))
		{
			if(array_key_exists($field->id, $prefill_fields))
			{
				$prefill_value = $prefill_fields[$field->id];
				
				//				Used for debugging specific pre-population fields
				// 				if($field->id == 20)
				// 				{
				// 					echo "Field: <br/>";
				// 					var_dump($field);
				// 					echo "<br/>";
				// 					echo "Prefill: " . $prefill_value . "<br/><br/>";
				// 				}
				
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
// Non-Accredited Application Form (NASC)
add_filter("gform_pre_submission_" . SHORT_COURSE_APPLICATION_FORM_NON_ACCREDITED, 'short_course_non_accredited_application_form_presubmission');

function short_course_non_accredited_application_form_presubmission()
{
	if(isset($_SESSION['prefill']))
	{
		$prefill = $_SESSION['prefill'];
	}
	else
	{
		$prefill = new stdClass();
	}
	
	$prefill->title = $_POST['input_2'];
	$prefill->first_name = $_POST['input_9'];
	//$prefill->middle_name= $_POST['input_28'];
	$prefill->surname = $_POST['input_8'];
	$prefill->known_by= $_POST['input_10'];
	$prefill->gender= $_POST['input_27'];
	$prefill->birth_date = $_POST['input_11'];
	$prefill->home_phone = $_POST['input_20'];
	$prefill->mobile_phone = $_POST['input_19'];
	$prefill->email = $_POST['input_21'];
	//$prefill->street_address1 = $_POST['input_30'];
	$prefill->street_number = $_POST['input_64'];
	$prefill->street_name = $_POST['input_30'];
	$prefill->suburb = $_POST['input_31'];
	$prefill->state = $_POST['input_32'];
	$prefill->postcode = $_POST['input_33'];
	$prefill->postal_address_same = isset($_POST['input_43_1']) ? "Yes" : "";
	//$prefill->postal_street_address1 = $_POST['input_44'];
	$prefill->postal_street_number = $_POST['input_65'];
	$prefill->postal_street_name = $_POST['input_44'];
	$prefill->postal_suburb = $_POST['input_47'];
	$prefill->postal_state = $_POST['input_48'];
	$prefill->postal_postcode = $_POST['input_46'];
	
	$prefill->emergency_contact_firstname = $_POST['input_58'];
	$prefill->emergency_contact_surname = $_POST['input_59'];
	$prefill->emergency_contact_number = $_POST['input_60'];
	$prefill->emergency_contact_relationship = $_POST['input_61'];
	
	// Set some default values for other forms
	$prefill->country_of_birth = 'Australia';
	$prefill->main_language = 'English';
	
	$_SESSION['prefill'] = $prefill;
}



/* *************** *
 * POST-PROCESSING *
 * *************** */

// ****************************************************** //
// NON ACCREDITED APPLICATION FORM PROCESS (NASC) - START //
// ****************************************************** //
function short_course_application_form_non_accredited_submission_process($form_data, $order, $item_cost) {
	if (NASC_DEBUG_MODE) 
	{
		echo "<h3>Non Accredited Application Form - Process</h3>";
	}
	
	// Set common variables used in the error emails
	$file = 'form_post_processing.php';
	$function = 'short_course_application_form_non_accredited_submission_process()';
	
	$form = new JobReadyForm ();
	
	// Course Details
	$form->course_scope_code = $form_data->{'22'};
	$form->course_number = $form_data->{'23'};
	$form->invoice_option = $form_data->{'26'};
	$form->cost = $item_cost;
	
	// 03.01.2024 - Previously enrolled at NECA
	$form->previously_enrolled_at_neca = $form_data->{'62'};
	
	// Personal Details
	$form->gender = $form_data->{'27'};
	$form->title = $form_data->{'2'};
	$form->first_name = ucwords ( strtolower ( $form_data->{'9'} ) );
	//$form->middle_name = ucwords ( strtolower ( $form_data->{'28'} ) );
	$form->surname = ucwords ( strtolower ( $form_data->{'8'} ) );
	$form->known_by = ucwords ( strtolower ( $form_data->{'10'} ) );
	$form->birth_date = $form_data->{'11'};
	
	// Contact Details
	$form->home_phone = $form_data->{'20'};
	$form->mobile_phone = $form_data->{'19'};
	$form->email = strtolower ( $form_data->{'21'} );
	
	// Address
	$form->street_number = ucwords ( strtolower ( $form_data->{'64'} ) );
	$form->street_name = ucwords ( strtolower ( $form_data->{'30'} ) );
	$form->street_address1 = $form->street_number . ' ' . $form->street_name;
	$form->suburb = ucwords ( strtolower ( $form_data->{'31'} ) );
	$form->state = ucwords ( strtolower ( $form_data->{'32'} ) );
	$form->postcode = $form_data->{'33'};
	
	$form->postal_address_same = $form_data->{'43.1'};
	
	if ($form->postal_address_same != 'Yes') 
	{
		// Address
		$form->postal_street_number = ucwords ( strtolower ( $form_data->{'65'} ) );
		$form->postal_street_name = ucwords ( strtolower ( $form_data->{'44'} ) );
		$form->postal_street_address1 = $form->postal_street_number . ' ' . $form->postal_street_name;
		$form->postal_suburb = ucwords ( strtolower ( $form_data->{'47'} ) );
		$form->postal_state = ucwords ( strtolower ( $form_data->{'48'} ) );
		$form->postal_postcode = $form_data->{'46'};
	}
	
	// Emergency Contact
	$form->emergency_contact_firstname = ucwords ( strtolower ( $form_data->{'58'} ) );
	$form->emergency_contact_surname = ucwords ( strtolower ( $form_data->{'59'} ) );
	$form->emergency_contact_number = ucwords ( strtolower ( $form_data->{'60'} ) );
	$form->emergency_contact_relationship = ucwords ( strtolower ( $form_data->{'61'} ) );
	
	$form->privacy_declaration = isset ( $form_data->{'41.1'} ) ? $form_data->{'41.1'} : '';
	
	// 03.01.2024 - Would you describe yourself as belonging to any of the following cohorts?
	/* 15.03.2024 - Remove requested by Cheryl Thompson
	$form->cohorts = array();
	for($i = 1; $i < 20; $i ++)
	{
		$ref = '67.' . $i;
		$cohort_value = $form_data->{$ref};
		if ($cohort_value != '' && $cohort_value != 'NNNNNN')
		{
			$form->cohorts [] = $cohort_value;
		}
	}
	*/	
	
	if (NASC_DEBUG_MODE) 
	{
		echo "Form Variable: <br/>";
		var_dump ( $form );
		echo "<br/><br/>";
	}
	
	/*
	 * Setup Job Ready Resources and Create Accordingly
	 */
	
	// Create PDF
	$scnaafp = short_course_non_accredited_application_form_pdf ( $form );
	if(NASC_DEBUG_MODE)
	{
		echo '<a href="' . JR_ROOT_URL . '/pdf/' . $scnaafp. '" target="_blank">Non Accredited Applcation Form (PDF)</a><br/><br/>';
	}
	
	// Setup "Party" Resource
	$party = new JRAParty ();
	$party->party_type = 'Person';
	$party->contact_method = 'Email';
	$party->first_name = $form->first_name;
	//$party->middle_name = $form->middle_name;
	$party->surname = $form->surname;
	$party->known_by = $form->known_by;
	$party->birth_date = date_create_from_format ( "Y-m-d", $form->birth_date, timezone_open ( "Australia/Melbourne" ) );
	$party->gender = $form->gender;
	$party->title = $form->title;
	
	// Setup "Party > Address" Child Resources
	$party_addresses = array ();
	$party_address = new JRAPartyAddress ();
	$party_address->primary = 'true';
	//$party_address->street_address1 = $form->street_address1;
	$party_address->street_number = $form->street_number;
	$party_address->street_name = $form->street_name;
	$party_address->suburb = $form->suburb;
	$party_address->state = $form->state;
	$party_address->country = 'Australia';
	$party_address->post_code = $form->postcode;
	$party_address->location = "Home";
	
	// Add to party_addresses array
	array_push ( $party_addresses, $party_address );
	
	// Add Postal Address?
	if ($form->postal_address_same != 'Yes') 
	{
		$postal_address = new JRAPartyAddress ();
		$postal_address->primary = '';
		//$postal_address->street_address1 = $form->postal_street_address1;
		$postal_address->street_number = $form->postal_street_number;
		$postal_address->street_name = $form->postal_street_name;
		$postal_address->suburb = $form->postal_suburb;
		$postal_address->state = $form->postal_state;
		$postal_address->country = 'Australia';
		$postal_address->post_code = $form->postal_postcode;
		$postal_address->location = "Postal";
		array_push ( $party_addresses, $postal_address );
	}
	
	$party->address_child = $party_addresses;
	
	// Setup "Party > Contact Detail" Child Resources
	$contact_details = array ();
	$contact_detail = new JRAPartyContactDetail ();
	$contact_detail->primary = 'true';
	$contact_detail->contact_type = 'Email';
	$contact_detail->value = $form->email;
	array_push ( $contact_details, $contact_detail );
	
	if (trim ( $form->home_phone != '' )) 
	{
		$contact_detail = new JRAPartyContactDetail ();
		$contact_detail->primary = '';
		$contact_detail->contact_type = 'Phone';
		$contact_detail->value = $form->home_phone;
		array_push ( $contact_details, $contact_detail );
	}
	
	if (trim ( $form->mobile_phone != '' )) 
	{
		$contact_detail = new JRAPartyContactDetail ();
		$contact_detail->primary = 'true';
		$contact_detail->contact_type = 'Mobile';
		$contact_detail->value = $form->mobile_phone;
		array_push ( $contact_details, $contact_detail );
	}
	
	$party->contact_detail_child = $contact_details;
	
	// Unset unused fields from the Party
	unset ( $party->avetmiss_child );
	unset ( $party->vet_free_help_child );
	unset ( $party->cricos_child );
	
	if (NASC_DEBUG_MODE) 
	{
		echo "Party Variable: <br/>";
		var_dump ( $party );
		echo "<br/><br/>";
	}
	
	// Check if the Party Exists
	$party_result = JRAPartyOperations::getJRAParty ( $party );
	
	$party_attributes = $party_result->attributes ();
	$count = ( int ) $party_attributes ['total'];
	
	if ($count > 0) 
	{
		// Set Party ID
		$party_id = ( string ) $party_result->party->{'party-identifier'};
		
		// Check if the existing party already has a middle name specified
		// If so, make the middle name "blank" so it does not update (workaround)
		/*
		if ($party_result->party->{'middle-name'} != '') 
		{
			$party->middle_name = '';
		}
		*/
		
		$new_party = false;
		
		if (NASC_DEBUG_MODE) 
		{
			echo "Party Exists - Party ID: " . $party_id . "<br/><br/>";
			echo "Result: <br/>";
			var_dump ( $party_result );
			echo "<br/><br/>";
		}
		
		// Update Party
		$update_party_xml = JRAPartyOperations::updateJRAPartyXML ( $party );
		
		if (NASC_DEBUG_MODE) 
		{
			// echo "Update Party XML: <br/>";
			// var_dump($update_party_xml);
			// echo "<br/><br/>";
		}
		
		$update_party_result = JRAPartyOperations::updateJRAParty ( $update_party_xml, $party_id );
		
		if (isset ( $update_party_result->{'party-identifier'} )) 
		{
			if (NASC_DEBUG_MODE) 
			{
				echo "Party Updated<br/><br/>";
				// echo "Update Party Result: <br/>";
				// var_dump($update_party_result);
				// echo "<br/><br/>";
			}
		}
	} 
	else
	{
		if (NASC_DEBUG_MODE) 
		{
			echo "Party does not exist. Create new Party<br/>";
		}
		
		// Create Party
		$party_xml = JRAPartyOperations::createJRAPartyXML ( $party );
		
		if (NASC_DEBUG_MODE) 
		{
			echo "Party XML: <br/>";
			var_dump ( $party_xml );
			echo "<br/><br/>";
		}
		
		$party_result = JRAPartyOperations::createJRAParty ( $party_xml );
		
		if (isset ( $party_result->{'party-identifier'} )) 
		{
			$party_id = ( string ) $party_result->{'party-identifier'};
			$new_party = true;
			
			if (NASC_DEBUG_MODE) 
			{
				echo "New Party Created - Party ID: " . $party_id . "<br/><br/>";
			}
		} 
		else 
		{
			if (NASC_DEBUG_MODE) 
			{
				echo "Error occurred when creating Party<br/><br/>";
			}
			return false;
		}
	}
	
	if (isset ( $party_id )) 
	{
		 // Create Enrolment
		 $enrolment = new JRAEnrolment ();
		 $enrolment->party_identifier = $party_id;
		 $enrolment->course_number = $form->course_number;
		 $enrolment->invoice_option = $form->invoice_option;
		 
		 // 18.01.2024 - Added as requested by Lyn Wang
		 // COHORT FIELD
		 /* 15.03.2024 - Remove requested by Cheryl Thompson
		 $cohort_fields = array ();
		 foreach($form->cohorts as $cohort)
		 {
		 	$cohort_field = new JRAEnrolmentCommencingProgramCohortIdentifier();
		 	$cohort_field->code = $cohort;
		 	array_push ( $cohort_fields, $cohort_field );
		 }
		 $enrolment->commencing_program_cohort_identifiers = $cohort_fields;
		 */
		 
		 if (NASC_DEBUG_MODE) 
		 {
		 	echo "Create New Enrolment<br/>";
		 }
		 
		 $enrolment_xml = JRAEnrolmentOperations::createJRAEnrolmentXML ( $enrolment );
		 $enrolment_result = JRAEnrolmentOperations::createJRAEnrolment ( $enrolment_xml );
		 
		 if (isset ( $enrolment_result->{'party-identifier'} )) 
		 {
		 	$enrolment_id = ( string ) $enrolment_result->{'enrolment-identifier'};
		 	if (NASC_DEBUG_MODE) 
		 	{
		 		echo "New Enrolment created - Enrolment ID: " . $enrolment_id . "<br/><br/>";
		 	}
		 } 
		 else 
		 {
		 	if (NASC_DEBUG_MODE) 
		 	{
		 		echo "Error occurred when creating Enrolment<br/><br/>";
		 	}
		 }
		 
		 if (isset ( $enrolment_id )) 
		 {
		 	// Update the VSN if it was specified
		 	if ($enrolment->victorian_student_number) 
		 	{
		 		$update_enrolment_vsn_result = JRAEnrolmentOperations::updateJRAEnrolmentVSN ( $enrolment, $enrolment_id );
		 	}
		 	
		 	// Load Invoice by Enrolment ID
		 	$invoices = JRAInvoiceOperations::loadInvoiceByEnrolmentID ( $enrolment_id );
		 	
		 	if (NASC_DEBUG_MODE) 
		 	{
		 		echo "Invoices retrieved from Job Ready: " . count ( $invoices ) . "<br/><br/>";
		 	}
		 	
		 	// Load the Payment Details
		 	$order_id = ( string ) $order->get_id ();
		 	$payment_details = getPaymentByOrderID ( $order_id );
		 	
		 	foreach ( $invoices as $invoice ) 
		 	{
		 		// Create Payment
		 		$payment = new JRAPayment ();
		 		$payment->invoice_number = $invoice->{'invoice_number'}; // Job Ready Invoice Number
		 		$payment->party_identifier = $party_id; // Job Ready Party ID
		 		$payment->type = 'Payment';
		 		$payment->description = 'Payment via NECA Website - Order #' . $order_id;
		 		$payment->source = 'Credit Card';
		 		$payment->enabled = 'true';
		 		
		 		$payment->payment_items = array ();
		 		$payment_item = new JRAPaymentItem ();
		 		$payment_item->payment_amount = $form->cost;
		 		array_push ( $payment->payment_items, $payment_item );
		 		
		 		$payment_xml = JRAPaymentOperations::createJRAPaymentXML ( $payment );
		 		$payment_result = JRAPaymentOperations::createJRAPayment ( $party_id, $payment->invoice_number, $payment_xml );
		 		
		 		if (isset ( $payment_result->{'invoice-number'} )) 
		 		{
		 			if (NASC_DEBUG_MODE) 
		 			{
		 				echo "Payment Created<br/><br/>";
		 			}
		 		} 
		 		else 
		 		{
		 			if (NASC_DEBUG_MODE) 
		 			{
		 				echo "Error occured when creating Payment<br/><br/>";
		 			}
		 		}
		 	}
		 }
		 
		 // } - The condition for creating a prospect or not has been commented out
		 
		 // Create "Party Contact" Resource for Emergency Contact Person
		 // Setup "Party Contact" Resource
		 $party_contact = new JRAPartyContact ();
		 $party_contact->contact_method = 'Phone';
		 $party_contact->first_name = $form->emergency_contact_firstname;
		 $party_contact->surname = $form->emergency_contact_surname;
		 $party_contact->phone = preg_replace ( '/\s/', '', $form->emergency_contact_number );
		 $party_contact->relationship = $form->emergency_contact_relationship;
		 
		 // Check if "Party Contact" exists already
		 $jra_party_contacts = JRAPartyContactOperations::getJRAPartyContacts ( $party_id );
		 
		 // Loops through all party contacts linked to the party_id
		 foreach ( $jra_party_contacts as $jra_party_contact ) 
		 {
		 	if ($jra_party_contact->first_name == $party_contact->first_name && $jra_party_contact->surname == $party_contact->surname) 
		 	{
		 		$party_contact_id = ( string ) $jra_party_contact->id;
		 		break;
		 	}
		 }
		 
		 if (isset ( $party_contact_id )) 
		 {
		 	if (NASC_DEBUG_MODE) 
		 	{
		 		echo "Party Contact Exists<br/><br/>";
		 	}
		 	
		 	// Update Party Contact (email + phone number)
		 	$party_contact_update_result = JRAPartyContactOperations::updateJRAPartyContact ( $party_id, $party_contact_id, $party_contact );
		 } 
		 else 
		 {
		 	if (NASC_DEBUG_MODE) 
		 	{
		 		echo "Create Party Contact<br/><br/>";
		 	}
		 	
		 	$party_contact_xml = JRAPartyContactOperations::createJRAPartyContactXML ( $party_contact );
		 	$party_contact_result = JRAPartyContactOperations::createJRAPartyContact ( $party_id, $party_contact_xml );
		 	
		 	if (isset ( $party_contact_result->id )) 
		 	{
		 		if (NASC_DEBUG_MODE) 
		 		{
		 			echo "Party Contact created - Party Contact ID: " . $party_contact->id . "<br/><br/>";
		 		}
		 	} 
		 	else 
		 	{
		 		if (NASC_DEBUG_MODE) 
		 		{
		 			echo "Error occured when creating Party Contact<br/><br/>";
		 		}
		 	}
		 }
		 
		 // Create "PartyDocument" and link the Application Form PDF to the Party
		 $pdf_file = JR_ROOT_PATH . '/pdf/' . $scnaafp;
		 $document = new JRAPartyDocument ();
		 $document->party_id = $party_id;
		 $document->name = 'Course Application Form (' . $form->course_scope_code . ' - ' . $form->course_number . ')';
		 $document->description = 'Non Accredited Application Form (' . $form->course_scope_code . ' - ' . $form->course_number . ')';
		 $document->filename = $pdf_file;
		 
		 $party_document_result = JRAPartyDocumentOperations::createJRAPartyDocument ( $party_id, $document );
		 
		 if (isset ( $party_document_result->id )) 
		 {
		 	if (NASC_DEBUG_MODE) 
		 	{
		 		echo "Party Document Created - Party Document ID: " . $party_document_result->id . "<br/>";
		 	}
		 	
		 	// Remove PDF from server (unless debugging)
		 	if(!NASC_DEBUG_MODE)
		 	{
			 	unlink ( $pdf_file );
		 	}
		 	
		 	if (NASC_DEBUG_MODE) 
		 	{
		 		echo "PDF file removed from web server<br/><br/>";
		 	}
		 } 
		 else 
		 {
		 	if (NASC_DEBUG_MODE) 
		 	{
		 		echo "Error occurred when created Party Document<br/><br/>";
		 	}
		 }
		 
		 // Check course enrolment availability and sync from Job Ready if less than 3 remaining
		 check_course_date_and_sync ( $form->course_number );
		 
		 return $party_id;
	} 
	else 
	{
		if (NASC_DEBUG_MODE) 
		{
			echo "Party was not created and submission process was stopped<br/><br/>";
		}
		return false;
	}
}




/* *********** *
 * PDF-RELATED *
 * *********** */

// Setup Non Accredited Application Form PDF
function short_course_non_accredited_application_form_pdf($form)
{
	// Start
	$content = neca_pdf_content_start ();
	
	// Add Heading
	$content .= neca_pdf_content_heading ( 'Non Accredited Application Form', $form );
	
	// Previously Enrolled
	// 28.02.2024 - Added Previously Enrolled - Requested by Lyn Wang
	$content .= neca_pdf_previously_enrolled($form);
	
	// Personal Details Content
	$content .= neca_pdf_content_personal_details ( $form );
	
	// Emergency Contact Details Content
	$content .= neca_pdf_content_emergency_content_details ( $form );
	
	// How did you hear Details Content
	//$content .= neca_pdf_content_cohort($form);
		
	// Page End
	$content .= neca_pdf_page_end ();
	
	// Page Start
	$content .= neca_pdf_page_start ();
	
	// All students must read, sign and date
	$content .= neca_pdf_content_all_students_must_read_sign_date_nasc ( $form );
	
	// Page End
	$content .= neca_pdf_page_end ();
	
	// Setup Filename
	$filename = urlencode ( $form->course_number . '_' . $form->first_name . '_' . $form->surname . '_' . current_time ( 'Ymd_hms' ) ) . '.pdf';
	
	// Create PDF
	neca_create_pdf ( $filename, $content );
	
	return $filename;
}


// All Students Must Read, Sign and Date
function neca_pdf_content_all_students_must_read_sign_date_nasc($form)
{
	$content = '<div class="heading3">ALL STUDENTS MUST READ, SIGN AND DATE</div>';
	$content .= '<table class="tbl">
					<tr>
						<td style="width:5%">[' . $form->privacy_declaration . ']</td>
						<td style="width:95%">
							<p>
								<strong>PRIVACY DECLARATION - </strong><br/>
The information being sought in this form is collected for the purposes of processing your enrolment application.
The information will be held by NECA Education & Careers and may be accessed and used by people employed or
engaged by NECA Education & Careers in the delivery of services to you. The information may be used or disclosed
to organizations outside NECA Education & Careers where permitted by relevant Privacy Legislation. The provision
of the information is voluntary, however if this information is not provided NECA Education & Careers may be
unable to process your enrolment application. You have a right of access to, and correction of, your personal
information in accordance with the Privacy Legislation and NECA Education & Careers\' Privacy Policy. Please
direct any enquiries you may have in relation to this matter to NECA Education & Careers Privacy Officer.
							</p>
						</td>
					</tr>
				</table>
								
				<table class="tbl">
					<tr>
						<td style="width: 5%;">[&nbsp; &nbsp; &nbsp;]</td>
						<td style="width: 95%;">
							I have read and agree to the Fees, Charges and Policy Guide
						</td>
					</tr>
								
					<tr>
						<td style="width: 5%;">[&nbsp; &nbsp; &nbsp;]</td>
						<td style="width: 95%;">
							I have read and agree to the Refund Policy
						</td>
					</tr>
								
				</table>
				<br/>
				<table class="tbl">
					<tr>
						<td style="width: 25%;">Name: </td>
						<td style="width: 75%;">&nbsp;<br/></td>
					</tr>
					<tr>
						<td style="width: 25%;">Signature: </td>
						<td style="width: 75%;">&nbsp;<br/></td>
					</tr>
					<tr>
						<td style="width: 25%;">Date: </td>
						<td style="width: 75%;">&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
					</tr>
				</table>';
	
	return $content;
}



/* ************** *
 * GOOGLE RELATED *
 * ************** */
// Add Google Tracking to NON ACCREDITED form
add_action( 'gform_post_paging_' . SHORT_COURSE_APPLICATION_FORM_NON_ACCREDITED, 'nasc_google_tracking', 10, 3 );

function nasc_google_tracking( $form, $source_page_number, $current_page_number)
{
	$course_scope_code = $_REQUEST['course_scope_code'];
	
	// Setup the PAGES array
	$pages = array();
	$page_count = 2;
	
	for($i=1; $i <= $page_count; $i++)
	{
		$page = new stdClass();
		$page->number = $i;
		$page->virtual_page_url = '/nasc/'.$course_scope_code.'/step' . $i . '/';
		$page->virtual_page_title = 'Non Accredited Application - ' . $course_scope_code . ' - Step ' . $i;
		$pages[$i] = $page;
	}
	
	echo "
	<script>
		function track_gtm_pageview(virtual_page_url, virtual_page_title)
		{
			console.log('Google Analytics Page View');
			dataLayer.push({
				'event':'VirtualPageview',
				'virtualPageURL': virtual_page_url,
				'virtualPageTitle' : virtual_page_title
			});
		}
			
        track_gtm_pageview('" . $pages[$current_page_number]->virtual_page_url . "', '" . $pages[$current_page_number]->virtual_page_title. "');
	</script>";
	
	//	console.log('Track Page " . $pages[$current_page_number]->number . "');
	//	console.log('Virtual Page URL: " . $pages[$current_page_number]->virtual_page_url . "');
	
}
