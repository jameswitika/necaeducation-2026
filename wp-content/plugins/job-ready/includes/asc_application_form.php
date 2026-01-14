<?php
/* ************** *
 * PRE-PROCESSING *
 * ************** */

// Short Course (Accredited) Application Form (ASC)
add_filter("gform_pre_render_" . SHORT_COURSE_APPLICATION_FORM_ACCREDITED, 'short_course_accredited_application_form_prepopulate');
add_filter("gform_pre_validation_" . SHORT_COURSE_APPLICATION_FORM_ACCREDITED, 'short_course_accredited_application_form_prepopulate');
add_filter("gform_pre_submission_filter_" . SHORT_COURSE_APPLICATION_FORM_ACCREDITED, 'short_course_accredited_application_form_prepopulate');
add_filter("gform_admin_pre_render" . SHORT_COURSE_APPLICATION_FORM_ACCREDITED, 'short_course_accredited_application_form_prepopulate');

function short_course_accredited_application_form_prepopulate($form)
{
	$prefill_fields= array();
	
	if(ASC_DEBUG_MODE)
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
	
	$haystack = 'How are you?';
	$needle   = 'are';
	
	// Check if there no more enrolment spots remaining
	if(strpos($course_number, 'Holding Bay') === false && $jrd->enrolments_remaining <= 0 )
	{
		// Exit or Redirect
		$form['limitEntries'] = true;
		$form['limitEntriesCount'] = 0;
		$form['limitEntriesMessage'] = "There are no more enrolment spots left for this course date. Please select another date.";
		return $form;
	}
	
	if(isset($_SESSION['prefill']))
	{
		$prefill_fields['2'] = $_SESSION['prefill']->title;
		$prefill_fields['9'] = $_SESSION['prefill']->first_name;
		$prefill_fields['74'] = $_SESSION['prefill']->middle_name;
		$prefill_fields['8'] = $_SESSION['prefill']->surname;
		$prefill_fields['10'] = $_SESSION['prefill']->known_by;
		$prefill_fields['73'] = $_SESSION['prefill']->gender;
		$prefill_fields['11'] = $_SESSION['prefill']->birth_date;
		$prefill_fields['20'] = $_SESSION['prefill']->home_phone;
		$prefill_fields['19'] = $_SESSION['prefill']->mobile_phone;
		
		/*
		$prefill_fields['21'] = array(	'21' => $_SESSION['prefill']->email,
				'21.2' => $_SESSION['prefill']->email );
		*/
		
		$prefill_fields['91'] = $_SESSION['prefill']->emergency_contact_firstname;
		$prefill_fields['92'] = $_SESSION['prefill']->emergency_contact_surname;
		$prefill_fields['94'] = $_SESSION['prefill']->emergency_contact_relationship;
		$prefill_fields['93'] = $_SESSION['prefill']->emergency_contact_number;
		
		// 2023.11.13 - Replaced street address1 with with street number and street name
		// $prefill_fields['101'] = $_SESSION['prefill']->street_address1;
		$prefill_fields['138'] = $_SESSION['prefill']->street_number;
		$prefill_fields['139'] = $_SESSION['prefill']->street_name;
		$prefill_fields['102'] = $_SESSION['prefill']->suburb;
		$prefill_fields['103'] = $_SESSION['prefill']->state;
		$prefill_fields['104'] = $_SESSION['prefill']->postcode;
		// 		$prefill_fields['75'] = $_SESSION['prefill']->postal_address_same;

		// 2023.11.13 - Replaced street address1 with with street number and street name
		// $prefill_fields['105'] = $_SESSION['prefill']->postal_street_address1;
		$prefill_fields['140'] = $_SESSION['prefill']->postal_street_number;
		$prefill_fields['141'] = $_SESSION['prefill']->postal_street_name;
		$prefill_fields['106'] = $_SESSION['prefill']->postal_suburb;
		$prefill_fields['107'] = $_SESSION['prefill']->postal_state;
		$prefill_fields['108'] = $_SESSION['prefill']->postal_postcode;
		
		$prefill_fields['28'] = $_SESSION['prefill']->labour_force_status;
		$prefill_fields['87'] = $_SESSION['prefill']->country_of_birth;
		$prefill_fields['34'] = $_SESSION['prefill']->indigenous_status;
		$prefill_fields['38'] = $_SESSION['prefill']->main_language == 'English' ? 'No, English only' : 'Yes';
		$prefill_fields['89'] = $_SESSION['prefill']->main_language;
		
		$prefill_fields['45'] = $_SESSION['prefill']->at_school_flag;
		$prefill_fields['47'] = $_SESSION['prefill']->highest_school_level;
		$prefill_fields['42'] = $_SESSION['prefill']->disability_flag;
		$prefill_fields['111'] = $_SESSION['prefill']->disability_other;
		$prefill_fields['50'] = $_SESSION['prefill']->prior_education_flag;
		$prefill_fields['125'] = $_SESSION['prefill']->prior_education_qualification;
		
		$prefill_fields['132'] = $_SESSION['prefill']->credit_transfer;
		
		// 31.08.2020 - Industry and Occupation added
		$prefill_fields['128'] = isset($_SESSION['prefill']->industry_employment) ? $_SESSION['prefill']->industry_employment : '';
		$prefill_fields['129'] = isset($_SESSION['prefill']->occupation) ? $_SESSION['prefill']->occupation : '';
		
		$prefill_fields['68'] = $_SESSION['prefill']->study_reason;
		$prefill_fields['67'] = $_SESSION['prefill']->how_did_you_hear;
		$prefill_fields['126'] = $_SESSION['prefill']->language_literacy_numeracy;
		
		$prefill_fields['54'] = $_SESSION['prefill']->usi_number;
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
				$prefill_fields['74'] = $form_fields->middle_name;
				$prefill_fields['8'] = $form_fields->surname;
				$prefill_fields['10'] = $form_fields->known_by;
				$prefill_fields['11'] = $form_fields->birth_date;
				$prefill_fields['73'] = $form_fields->gender;
				$prefill_fields['20'] = $form_fields->home_phone;
				$prefill_fields['19'] = $form_fields->mobile_phone;
				
				/*
				$prefill_fields['21'] = array(	'21' => $form_fields->email,
						'21.2' => $form_fields->email );
				*/
				
				// 2023.11.13 - Replaced Street Address with Street Number and Street Name
				//$prefill_fields['101'] = $form_fields->street_address1;
				$prefill_fields['138'] = $form_fields->street_number;
				$prefill_fields['139'] = $form_fields->street_name;
				$prefill_fields['102'] = $form_fields->suburb;
				$prefill_fields['103'] = $form_fields->state;
				$prefill_fields['104'] = $form_fields->postcode;
				
				//$prefill_fields['75'] = $form_fields->postal_address_same;
				
				// 2023.11.13 - Replaced street address1 with with street number and street name
				//$prefill_fields['105'] = $form_fields->postal_street_address1;
				$prefill_fields['140'] = $form_fields->postal_street_number;
				$prefill_fields['141'] = $form_fields->postal_street_name;
				$prefill_fields['106'] = $form_fields->postal_suburb;
				$prefill_fields['107'] = $form_fields->postal_state;
				$prefill_fields['108'] = $form_fields->postal_postcode;
				
				$prefill_fields['28'] = $form_fields->labour_force_status;
				$prefill_fields['87'] = $form_fields->country_of_birth;
				$prefill_fields['34'] = $form_fields->indigenous_status;
				$prefill_fields['38'] = $form_fields->main_language == 'English' ? 'No, English only' : 'Yes';
				$prefill_fields['89'] = $form_fields->main_language;
				
				
				$prefill_fields['34'] = $form_fields->indigenous_status;
				$prefill_fields['89'] = $form_fields->main_language;
				
				$prefill_fields['45'] = $form_fields->at_school_flag;
				$prefill_fields['47'] = $form_fields->highest_school_level;
				$prefill_fields['42'] = $form_fields->disability_flag;
				$prefill_fields['50'] = $form_fields->prior_education_flag;
				
				$prefill_fields['54'] = $form_fields->usi_number;
				
				$neca_member = $form_fields->neca_member == 'true' ? true : false;
				
				$party_id = $employee_party_xml_object->{'party'}->{'party-identifier'};
				
				// Retrieve "Party Contact" and pre-populate Emergency Contact Details using "primary" record
				$jra_party_contacts = JRAPartyContactOperations::getJRAPartyContacts($party_id);
				
				$primary_party_contact_found = false;
				
				// Loops through all party contacts linked to the party_id
				foreach($jra_party_contacts as $jra_party_contact)
				{
					if(	$jra_party_contact->primary == 'true')
					{
						$prefill_fields['91'] = $jra_party_contact->first_name;
						$prefill_fields['92'] = $jra_party_contact->surname;
						$prefill_fields['94'] = $jra_party_contact->relationship;
						$prefill_fields['93'] = $jra_party_contact->phone;
						
						/*
						$prefill_fields['121'] = array(	'121' => $jra_party_contact->email,
								'121.2' => $jra_party_contact->email);
						*/
						
						$primary_party_contact_found= true;
						break;
					}
				}
				
				// If there was no primary contact, use the first party contact
				if(!$primary_party_contact_found)
				{
					$prefill_fields['91'] = $jra_party_contacts[0]->first_name;
					$prefill_fields['92'] = $jra_party_contacts[0]->surname;
					$prefill_fields['94'] = $jra_party_contacts[0]->relationship;
					$prefill_fields['93'] = $jra_party_contacts[0]->phone;
					
					/*
					$prefill_fields['121'] = array(	'121' => $jra_party_contacts[0]->email,
							'121.2' => $jra_party_contacts[0]->email);
					*/
				}
				
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
		if($field->id == 72)
		{
			// Setup Cost Choices
			$choices = jrar_invoice_options($course_number, $neca_member);
			$field->choices = $choices;
		}
		
		// Title
		if($field->id == 2)
		{
			$field->choices = jrar_title();
		}
		
		// Gender
		if($field->id == 73)
		{
			$field->choices = jrar_gender();
		}
		
		// Employment Category
		if($field->id == 28)
		{
			$field->choices = jrar_employment_category();
		}
		
		// Indigenous Status
		if($field->id == 34)
		{
			$field->choices = jrar_indigenous_status();
		}
		
		// Language
		if($field->id == 89)
		{
			$field->choices = jrar_language();
		}
		
		// Highest School Level
		if($field->id == 47)
		{
			$field->choices = jrar_highest_school_level();
		}
		
		// Disability Type
		if($field->id == 43)
		{
			$disability_choices = array();
			
			$choices = jrar_disability_type();
			
			foreach($choices as $choice)
			{
				if(isset($_SESSION['prefill']->disability_types) && array_search($choice['value'], $_SESSION['prefill']->disability_types) !== false)
				{
					$choice['isSelected'] = true;
				}
				if(isset($form_fields) && array_search($choice['value'], $form_fields->disability_types) !== false)
				{
					$choice['isSelected'] = true;
				}
				array_push($disability_choices, $choice);
			}
			
			$field->choices = $disability_choices;
		}
		
		// Prior Education Type
		if($field->id == 51)
		{
			$prior_education_choices = array();
			
			$choices = jrar_prior_education_type();
			
			foreach($choices as $choice)
			{
				if(isset($_SESSION['prefill']->prior_educations) && array_search($choice['value'], $_SESSION['prefill']->prior_educations) !== false)
				{
					$choice['isSelected'] = true;
				}
				if(isset($form_fields) && array_search($choice['value'], $form_fields->prior_educations) !== false )
				{
					$choice['isSelected'] = true;
				}
				array_push($prior_education_choices, $choice);
			}
			
			$field->choices = $prior_education_choices;
		}
		
		// Study Reason
		if($field->id == 68)
		{
			$field->choices = jrar_study_reason();
		}
		
		// Client Industry
		if($field->id == 128)
		{
			$field->choices = jrar_client_industry_employer();
		}
		
		// Client Occupation
		if($field->id == 129)
		{
			$field->choices = jrar_client_occupation_identifer();
		}
		
		// State
		if($field->id == 103 || $field->id == 107)
		{
			$field->choices = jrar_state();
		}
		
		// Country
		if($field->id == 87 || $field->id == 88)
		{
			$field->choices = jrar_country();
		}
		
		// Pre-requisites
		if($field->id == 61)
		{
			$prereq_heading = "<strong>Pre-requisites</strong><br/>";
			$prereq_desc = JobReadyCourseOperations::getJobReadyCoursePrerequisitesByCourseScopeCode( $_GET['course_scope_code'] );
			
			if(trim($prereq_desc) != '')
			{
				$field->content = apply_filters('the_content', $prereq_heading . $prereq_desc);
			}
			else
			{
				$field['visibility'] = 'hidden';
			}
		}
		
		// Pre-requisites
		if($field->id == 86)
		{
			$prereq_desc .= JobReadyCourseOperations::getJobReadyCoursePrerequisitesByCourseScopeCode( $_GET['course_scope_code'] );
			
			if(trim($prereq_desc) == '')
			{
				$field['isSelected'] = true;
				$field['visibility'] = 'hidden';
			}
		}
		
		
		// Course Cost (update label to be course name and date)
		if($field->id == 84)
		{
			//$total_label = $jrd->course_name . " (" . $jrd->start_date_clean. " to " . $jrd->end_date_clean . ")";
			//$field->label = $total_label;
			$field->label = 'Cost';
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
// Short Course (Accredited) Application Form (ASC)
add_filter("gform_pre_submission_" . SHORT_COURSE_APPLICATION_FORM_ACCREDITED, 'short_course_accredited_application_form_presubmission');

function short_course_accredited_application_form_presubmission()
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
	$prefill->middle_name = $_POST['input_74'];
	$prefill->surname = $_POST['input_8'];
	$prefill->known_by = $_POST['input_10'];
	$prefill->gender = $_POST['input_73'];
	$prefill->birth_date = $_POST['input_11'];
	$prefill->home_phone = $_POST['input_20'];
	$prefill->mobile_phone = $_POST['input_19'];
	$prefill->email = $_POST['input_21'];
	
	// Replace Street Address 1 with Street Number and Street Name
	//$prefill->street_address1 = $_POST['input_101'];
	$prefill->street_number = $_POST['input_138'];
	$prefill->street_name = $_POST['input_139'];
	$prefill->suburb = $_POST['input_102'];
	$prefill->state = $_POST['input_103'];
	$prefill->postcode = $_POST['input_104'];
	$prefill->postal_address_same = isset($_POST['input_75_1']) ? "Yes" : "";
	
	// 2023.11.13 - Replaced street address1 with with street number and street name
	// $prefill->postal_street_address1 = $_POST['input_105'];
	$prefill->postal_street_number = $_POST['input_140'];
	$prefill->postal_street_name = $_POST['input_141'];
	$prefill->postal_suburb = $_POST['input_106'];
	$prefill->postal_state = $_POST['input_107'];
	$prefill->postal_postcode = $_POST['input_108'];
	
	$prefill->emergency_contact_firstname = $_POST['input_91'];
	$prefill->emergency_contact_surname = $_POST['input_92'];
	$prefill->emergency_contact_number = $_POST['input_93'];
	$prefill->emergency_contact_relationship = $_POST['input_94'];
	
	$prefill->labour_force_status = $_POST['input_28'];
	$prefill->country_of_birth = $_POST['input_87'];
	$prefill->indigenous_status = $_POST['input_34'];
	$prefill->main_language = ($_POST['input_38'] == "No, English only") ? 'English' : $_POST['input_89'];
	$prefill->at_school_flag = $_POST['input_45'];
	$prefill->highest_school_level = $_POST['input_47'];
	$prefill->disability_flag = $_POST['input_42'];
	
	if($prefill->disability_flag == 'Yes')
	{
		$disability_types = array();
		for($i=1; $i<=20; $i++)
		{
			if(isset($_POST['input_43_' . $i]))
			{
				array_push($disability_types, $_POST['input_43_' . $i]);
			}
		}
		$prefill->disability_types = $disability_types;
		$prefill->disability_other = $_POST['input_111'];
	}
	
	$prefill->prior_education_flag = $_POST['input_50'];
	
	if($prefill->prior_education_flag== 'Yes')
	{
		$prior_educations = array();
		for($i=1; $i<=20; $i++)
		{
			if(isset($_POST['input_51_' . $i]))
			{
				array_push($prior_educations, $_POST['input_51_' . $i]);
			}
		}
		$prefill->prior_educations= $prior_educations;
		$prefill->prior_education_qualification = $_POST['input_125'];
	}
	
	
	// 02.02.2021 - Credit Transfer
	$prefill->credit_transfer = $_POST['input_132'];
	
	$prefill->usi_number = $_POST['input_54'];
	
	// 31.08.2020 - Industry and Occupation added
	$prefill->industry_employment = $_POST['input_128'];
	$prefill->occupation = $_POST['input_129'];
	
	$prefill->study_reason = $_POST['input_68'];
	$prefill->how_did_you_hear = $_POST['input_67'];
	$prefill->language_literacy_numeracy = $_POST['input_126'];
	
	$_SESSION['prefill'] = $prefill;
}



/* *************** *
 * POST-PROCESSING *
 * *************** */

function short_course_application_form_accredited_submission_process($form_data, $order, $item_cost) 
{
	if (ASC_DEBUG_MODE) 
	{
		echo "<h3>Short Course Application Form - Accredited Submission Process</h3>";
	}
	
	$form = new JobReadyForm ();
	
	// Course Details
	$form->course_scope_code = $form_data->{'69'};
	$form->course_number = $form_data->{'70'};
	$form->invoice_option = $form_data->{'72'};
	$form->cost = $item_cost;
	
	// Personal Details
	$form->gender = $form_data->{'73'};
	$form->title = $form_data->{'2'};
	$form->first_name = ucwords ( strtolower ( $form_data->{'9'} ) );
	$form->middle_name = ucwords ( strtolower ( $form_data->{'74'} ) );
	$form->surname = ucwords ( strtolower ( $form_data->{'8'} ) );
	$form->known_by = ucwords ( strtolower ( $form_data->{'10'} ) );
	$form->birth_date = $form_data->{'11'};
	
	// Contact Details
	$form->home_phone = $form_data->{'20'};
	$form->mobile_phone = $form_data->{'19'};
	$form->email = strtolower ( $form_data->{'21'} );
	
	// Address
	// 2023.11.13 - Replaced street_address1 with street_number and street_name
	//$form->street_address1 = ucwords ( strtolower ( $form_data->{'101'} ) );
	$form->street_number = ucwords ( strtolower ( $form_data->{'138'} ) );
	$form->street_name = ucwords ( strtolower ( $form_data->{'139'} ) );
	$form->suburb = ucwords ( strtolower ( $form_data->{'102'} ) );
	$form->state = ucwords ( strtolower ( $form_data->{'103'} ) );
	$form->postcode = $form_data->{'104'};
	
	$form->postal_address_same = $form_data->{'75.1'};
	
	if ($form->postal_address_same != 'Yes') 
	{
		// Address
		// 2023.11.13 - Replaced street_address1 with street_number and street_name
		//$form->postal_street_address1 = ucwords ( strtolower ( $form_data->{'105'} ) );
		$form->postal_street_number = ucwords ( strtolower ( $form_data->{'140'} ) );
		$form->postal_street_name = ucwords ( strtolower ( $form_data->{'141'} ) );
		$form->postal_suburb = ucwords ( strtolower ( $form_data->{'106'} ) );
		$form->postal_state = ucwords ( strtolower ( $form_data->{'107'} ) );
		$form->postal_postcode = $form_data->{'108'};
	}
	
	// Emergency Contact
	$form->emergency_contact_firstname = ucwords ( strtolower ( $form_data->{'91'} ) );
	$form->emergency_contact_surname = ucwords ( strtolower ( $form_data->{'92'} ) );
	$form->emergency_contact_number = $form_data->{'93'};
	$form->emergency_contact_relationship = ucwords ( strtolower ( $form_data->{'94'} ) );
	
	// Labour Force
	$form->labour_force_status = $form_data->{'28'};
	
	// Referred from a Job Seeker
	// $form->referred = $form_data->{'95'};
	// $form->referred_details = $form_data->{'96'};
	
	// Birth + Nationality + Indigenous + Language
	$form->country_of_birth = $form_data->{'87'};
	$form->indigenous_status = $form_data->{'34'};
	$language_other_than_english = $form_data->{'38'};
	
	if ($language_other_than_english != 'Yes') 
	{
		$form->main_language = "English";
		// $form->spoken_english_proficiency= "Very Well";
	}
	else
	{
		$form->main_language = $form_data->{'89'};
		// $form->spoken_english_proficiency= "Very Well";
	}
	
	// School Details
	$form->at_school_flag = $form_data->{'45'};
	$form->highest_school_level = $form_data->{'47'};
	
	// Disability
	$form->disability_types = array ();
	$form->disability_flag = $form_data->{'42'};
	if ($form->disability_flag == 'Yes') 
	{
		// Array fields are passed in as 43.1, 43.2, 43.3.... so we have iterate through them
		for($i = 1; $i < 10; $i ++) 
		{
			$ref = '43.' . $i;
			if ($form_data->{$ref} != '') 
			{
				$form->disability_types [] = $form_data->{$ref};
			}
		}
		if ($form->disabilities_other = $form_data->{'111'} != '') 
		{
			$form->disabilities_other = ucwords ( strtolower ( $form_data->{'111'} ) );
		}
	}
	
	// Prior Education
	$form->prior_education_flag = $form_data->{'50'};
	$form->prior_educations = array ();
	if ($form->prior_education_flag == 'Yes') 
	{
		// Array fields are passed in as 51.1, 51.2, 51.3.... so we have iterate through them
		for($i = 1; $i <= 20; $i ++) 
		{
			$ref = '51.' . $i;
			if ($form_data->{$ref} != '') 
			{
				$form->prior_educations [] = $form_data->{$ref};
			}
		}
		$form->prior_education_qualification = $form_data->{'125'};
	}
	
	// 2021.02.02 - Credit Transfer Added
	$form->credit_transfer = $form_data->{'132'};
	
	// Unique Student Number
	$form->usi_number = $form_data->{'54'};
	
	// 31.08.2020 - Added Industry Employment + Occupation
	$form->industry_employment = $form_data->{'128'};
	$form->occupation = $form_data->{'129'};
	
	
	// Enrolment > Skills VIC AVETMISS
	$form->study_reason = $form_data->{'68'};
	$form->how_did_you_hear = ucwords ( strtolower ( $form_data->{'67'} ) );
	
	// Language, Literacy or Numeracy
	$form->language_literacy_numeracy = $form_data->{'126'};
	
	// Student Declaraction
	$form->prerequisite_declaration = isset ( $form_data->{'86.1'} ) ? $form_data->{'86.1'} : ''; // Check boxes use 86.1, 86.2 etc in naming convention for each checkbox
	$form->privacy_declaration = isset ( $form_data->{'60.1'} ) ? $form_data->{'60.1'} : '';
	
	if (ASC_DEBUG_MODE) 
	{
		echo "Form Variable: <br/>";
		var_dump ( $form );
		echo "<br/><br/>";
	}
	
	/*
	 * Setup Job Ready Resources and Create Accordingly
	 */
	
	// Create PDF
	$aafp = short_course_accredited_application_form_pdf ( $form );
	if(ASC_DEBUG_MODE)
	{
		echo '<a href="' . JR_ROOT_URL . '/pdf/' . $aafp. '" target="_blank">Short Course (Accredited) Applcation Form (PDF)</a><br/><br/>';
	}
	
	// Setup "Party" Resource
	$party = new JRAParty ();
	$party->party_type = 'Person';
	$party->contact_method = 'Email';
	$party->first_name = $form->first_name;
	$party->middle_name = $form->middle_name;
	$party->surname = $form->surname;
	$party->known_by = $form->known_by;
	$party->birth_date = date_create_from_format ( "Y-m-d", $form->birth_date, timezone_open ( "Australia/Melbourne" ) );
	$party->gender = $form->gender;
	$party->title = $form->title;
	$party->usi_number = $form->usi_number;
	
	// Setup "Party > Address" Child Resources
	$party_addresses = array ();
	$party_address = new JRAPartyAddress ();
	$party_address->primary = 'true';
	// 2023.11.13 - Replace street_address1 with street_number and street_name
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
		// 2023.11.13 - Replace street_address1 with street_number and street_name
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
	
	// Setup "Party > AVETMISS" Child Resource
	$avetmiss = new JRAPartyAvetmiss ();
	$avetmiss->labour_force_status = $form->labour_force_status;
	$avetmiss->country_of_birth = $form->country_of_birth;
	
	if ($form->australian_citizen == 'Yes') 
	{
		$avetmiss->nationality = 'Australia';
		$avetmiss->citizenship_status = $form->citizenship_status;
	}
	else
	{
		$avetmiss->nationality = $form->citizenship_other;
	}
	
	$avetmiss->indigenous_status = $form->indigenous_status;
	$avetmiss->main_language = $form->main_language;
	$avetmiss->spoken_english_proficiency = $form->spoken_english_proficiency;
	$avetmiss->at_school_flag = $form->at_school_flag;
	$avetmiss->school = $form->school;
	$avetmiss->highest_school_level = $form->highest_school_level;
	$avetmiss->year_highest_school_level = $form->year_highest_school_level;
	$avetmiss->disability_flag = $form->disability_flag;
	
	if ($avetmiss->disability_flag == 'Yes') 
	{
		$avetmiss->disability_types = $form->disability_types;
	}
	
	$avetmiss->prior_education_flag = $form->prior_education_flag;
	
	if ($avetmiss->prior_education_flag == 'Yes') 
	{
		$avetmiss->prior_educations = $form->prior_educations;
		$avetmiss->prior_education_qualification = $form->prior_education_qualification;
	}
	
	if ($form->city_of_birth != '') 
	{
		$avetmiss->town_of_birth = $form->city_of_birth;
	}
	
	$party->avetmiss_child = $avetmiss;
	
	// CRICOS
	$cricos = new JRAPartyCricos ();
	$cricos->country_of_birth = $form->country_of_birth;
	$cricos->citizenship_status = $form->citizenship_status;
	$cricos->nationality = $form->australian_citizen == 'Yes' ? 'Australia' : $form->citizenship_other;
	$party->cricos_child = $cricos;
	
	// ADHOC FIELD
	/*
	$adhoc_fields = array ();
	$adhoc_field = new JRAPartyAdhoc ();
	$adhoc_field->name = 'How did you hear about us?';
	$adhoc_field->value = $form->how_did_you_hear;
	array_push ( $adhoc_fields, $adhoc_field );
	$party->adhoc_child = $adhoc_fields;
	*/
	
	if (ASC_DEBUG_MODE) 
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
		$party_id = ( string ) $party_result->party->{'party-identifier'};
		
		// Check if the existing party already has a middle name specified
		// If so, make the middle name "blank" so it does not update (workaround)
		if ($party_result->party->{'middle-name'} != '') 
		{
			$party->middle_name = '';
		}
		
		if (ASC_DEBUG_MODE) 
		{
			echo "Party Exists - Party ID: " . $party_id . " <br/><br/>";
			echo "Result: <br/>";
			var_dump ( $party_result );
			echo "<br/><br/>";
		}
		
		// Update Party
		$update_party_xml = JRAPartyOperations::updateJRAPartyXML ( $party );
		
		if (ASC_DEBUG_MODE) 
		{
			// echo "Update Party XML: <br/>";
			// var_dump($update_party_xml);
			// echo "<br/><br/>";
		}
		
		$update_party_result = JRAPartyOperations::updateJRAParty ( $update_party_xml, $party_id );
		
		if (isset ( $update_party_result->{'party-identifier'} )) 
		{
			if (ASC_DEBUG_MODE) 
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
		if (ASC_DEBUG_MODE) 
		{
			echo "Party does not exist - Create Party<br/>";
		}
		
		// Create Party
		$party_xml = JRAPartyOperations::createJRAPartyXML ( $party );
		
		if (ASC_DEBUG_MODE) 
		{
			echo "Party XML: <br/>";
			var_dump ( $party_xml );
			echo "<br/><br/>";
		}
		
		$party_result = JRAPartyOperations::createJRAParty ( $party_xml );
		
		if (isset ( $party_result->{'party-identifier'} )) 
		{
			$party_id = ( string ) $party_result->{'party-identifier'};
			
			if (ASC_DEBUG_MODE) 
			{
				echo "New Party Created - Party ID: " . $party_id . "<br/><br/>";
			}
		} 
		else
		{
			if (ASC_DEBUG_MODE) 
			{
				echo "Error occurred when creating Party: <br/><br/>";
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
		$enrolment->study_reason = $form->study_reason;
		
		// 31.08.2020 - Added Industry Employment + Occupation
		$enrolment->client_occupation_identifier = $form->occupation;
		$enrolment->client_industry_employment = $form->industry_employment;
		
		$enrolment->invoice_option = $form->invoice_option;
		
		// NOTE: Victorian student number must be an 9 digit value
		$enrolment->victorian_student_number = ($form->vsn != '' && strlen ( $form->vsn ) == 9) ? $form->vsn : '';
		$enrolment->unknown_victorian_student_number = $form->vsn != '' ? 'false' : 'true';
		$enrolment->previous_victorian_education_enrolment = $form->previous_victorian_education != '' ? $form->previous_victorian_education : '';
		
		// ADHOC FIELD
		$adhoc_fields = array ();
		$adhoc_field = new JRAEnrolmentAdhoc ();
		$adhoc_field->name = 'How did you hear about us?';
		$adhoc_field->value = $form->how_did_you_hear;
		array_push ( $adhoc_fields, $adhoc_field );
		$enrolment->adhoc_child = $adhoc_fields;
		
		if (ASC_DEBUG_MODE) 
		{
			echo "Create Enrolment<br/>";
		}
		
		// Create Enrolment
		$enrolment_xml = JRAEnrolmentOperations::createJRAEnrolmentXML ( $enrolment );
		
		if (ASC_DEBUG_MODE) 
		{
			echo "Enrolment XML: <br/>";
			var_dump ( $enrolment_xml );
			echo "<br/><br/>";
		}
		
		$enrolment_result = JRAEnrolmentOperations::createJRAEnrolment ( $enrolment_xml );
		
		if (isset ( $enrolment_result->{'enrolment-identifier'} )) 
		{
			$enrolment_id = ( string ) $enrolment_result->{'enrolment-identifier'};
			if (ASC_DEBUG_MODE) 
			{
				echo "Enrolment created - Enrolment ID: " . $enrolment_id . "<br/><br/>";
			}
		} 
		else 
		{
			if (ASC_DEBUG_MODE) 
			{
				echo "Error occurred while creating Enrolment<br/><br/>";
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
			
			if (ASC_DEBUG_MODE) 
			{
				echo "Invoices retrieved from Job Ready: " . count ( $invoices );
				echo "<br/><br/>";
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
					if (ASC_DEBUG_MODE) 
					{
						echo "Payment Created<br/><br/>";
					}
				} 
				else 
				{
					if (ASC_DEBUG_MODE) 
					{
						echo "Error occured when creating Payment<br/><br/>";
					}
				}
			}
		}
		
		// Create "Party Contact" Resource for Emergency Contact Person
		// Setup "Party Contact" Resource
		$party_contact = new JRAPartyContact ();
		$party_contact->contact_method = 'Phone';
		$party_contact->first_name = $form->emergency_contact_firstname;
		$party_contact->surname = $form->emergency_contact_surname;
		$party_contact->phone = preg_replace ( '/\s/', '', $form->emergency_contact_number );
		$party_contact->email = $form->emergency_contact_email;
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
			if (ASC_DEBUG_MODE) 
			{
				echo "Party Contact Exists<br/><br/>";
			}
			
			// Update Party Contact (email + phone number)
			$party_contact_update_result = JRAPartyContactOperations::updateJRAPartyContact ( $party_id, $party_contact_id, $party_contact );
		} 
		else 
		{
			if (ASC_DEBUG_MODE) 
			{
				echo "Create Party Contact<br/><br/>";
			}
			
			$party_contact_xml = JRAPartyContactOperations::createJRAPartyContactXML ( $party_contact );
			$party_contact_result = JRAPartyContactOperations::createJRAPartyContact ( $party_id, $party_contact_xml );
			
			if (isset ( $party_contact_result->id )) 
			{
				if (ASC_DEBUG_MODE) 
				{
					echo "Party Contact created - Party Contact ID: " . $party_contact->id . "<br/><br/>";
				}
			} 
			else 
			{
				if (ASC_DEBUG_MODE) 
				{
					echo "Error occured when creating Party Contact<br/><br/>";
				}
			}
		}
		
		// Create "PartyDocument" and link the Application Form PDF to the Party
		$pdf_file = JR_ROOT_PATH . '/pdf/' . $aafp;
		$document = new JRAPartyDocument ();
		$document->party_id = $party_id;
		$document->name = 'Course Application Form (' . $form->course_scope_code . ' - ' . $form->course_number . ')';
		$document->description = 'Short Course Accredited Application Form (' . $form->course_scope_code . ' - ' . $form->course_number . ')';
		$document->filename = $pdf_file;
		
		$party_document_result = JRAPartyDocumentOperations::createJRAPartyDocument ( $party_id, $document );
		
		if (isset ( $party_document_result->id )) 
		{
			if (ASC_DEBUG_MODE) 
			{
				echo "Party Document Created - Party Document ID: " . $party_document_result->id . "<br/>";
			}
			
			// Remove PDF from server - if not debugging
			if(!ASC_DEBUG_MODE)
			{
				unlink ( $pdf_file );
			}
			
			if (ASC_DEBUG_MODE) 
			{
				echo "PDF file removed from web server<br/><br/>";
			}
		} 
		else 
		{
			if (ASC_DEBUG_MODE) 
			{
				echo "Error occurred when created Party Document<br/><br/>";
			}
		}
		
		// If language_literacy_numeracy is Yes
		if ($form->language_literacy_numeracy == 'Yes') 
		{
			// Sends an email with a link to the language, literacy and numeracy quiz
			email_language_literacy_numeracy_quiz ( $form );
		}
		
		// Check course enrolment availability and sync from Job Ready if less than 3 remaining
		check_course_date_and_sync ( $form->course_number );
		
		return $party_id;
	} 
	else 
	{
		if (ASC_DEBUG_MODE) 
		{
			echo "Party was not created and submission process was stopped<br/><br/>";
		}
		return false;
	}
}



/* *********** *
 * PDF-RELATED *
 * *********** */

// Setup Short Course (Accredited) Application Form PDF
function short_course_accredited_application_form_pdf($form) 
{
	// Start
	$content = neca_pdf_content_start ();
	
	// Add Heading
	$content .= neca_pdf_content_heading ( 'Short Course Accredited Application Form', $form );
	
	// Personal Details Content
	$content .= neca_pdf_content_personal_details ( $form );
	
	// Emergency Contact Details Content
	$content .= neca_pdf_content_emergency_content_details ( $form );
	
	// Avetmiss Heading
	$content .= neca_pdf_content_avetmiss_heading ( $form );
	
	// Labour Force Details
	$content .= neca_pdf_content_labour_details ( $form );
	
	// Language Details
	$content .= neca_pdf_content_language_details ( $form );
	
	// Page End
	$content .= neca_pdf_page_end ();
	
	// Page Start
	$content .= neca_pdf_page_start ();
	
	// Education Details
	$content .= neca_pdf_content_education_details ( $form );
	
	// Disability Details Content
	$content .= neca_pdf_content_disability_details ( $form );
	
	// 2021.02.02 - Added
	// Credit Transfer Details
	$content .= neca_pdf_content_credit_transfer_rpl( $form );
	
	// USI Details
	$content .= neca_pdf_content_usi ( $form );
	
	// Enrolment Avetmiss Details
	$content .= neca_pdf_content_enrolment_avetmiss_details ( $form );
	
	// How Did You Hear
	$content .= neca_pdf_content_how_did_you_hear ( $form );
	
	// Language, Literacy or Numeracy
	$content .= neca_pdf_content_language_literacy_numeracy ( $form );
	
	// Page End
	$content .= neca_pdf_page_end ();
	
	// Page Start
	$content .= neca_pdf_page_start ();
	
	// Privacy Policy FEB2021
	$content .= neca_pdf_content_privacy_policy_feb2021();
	
	// Page End
	$content .= neca_pdf_page_end ();
	
	// Page Start
	$content .= neca_pdf_page_start ();
	
	// Prerequisites Content
	$content .= neca_pdf_content_prerequisite_declaration ( $form );
	
	// Student Declaration Content
	$content .= neca_pdf_content_all_students_must_read_sign_date_2019 ( $form );
	
	// Read and Understood the Privacy Notice and Student Declaration
	$content .= neca_pdf_content_privacy_notice_and_student_declaration ();
	
	// Enrolment Declaration Content
	$content .= neca_pdf_content_enrolment_declaration ();
	
	// Tickboxes
	$content .= neca_pdf_content_tickboxes ();
	
	// Student Enrolment Privacy Notice
	// FEB 2021 - Disabled due to adding new privacy policy (see above)
	//$content .= neca_pdf_content_student_enrolment_privacy_notice ();
	
	// Signatures
	$content .= neca_pdf_content_signatures_asc ( $form );
	
	// Page End
	$content .= neca_pdf_page_end ();
	
	// Page Start
	$content .= neca_pdf_page_start ();
	
	// Office Use Only
	$content .= neca_pdf_content_office_use_only_asc ( $form );
	
	// Sign Off
	$content .= neca_pdf_content_sign_off ( true );
	
	// End
	$content .= neca_pdf_page_end ();
	
	// Setup Filename
	$filename = urlencode ( $form->course_number . '_' . $form->first_name . '_' . $form->surname . '_' . current_time ( 'Ymd_hms' ) ) . '.pdf';
	
	// Create PDF
	neca_create_pdf ( $filename, $content );
	
	return $filename;
}


// Signatures (ASC)
function neca_pdf_content_signatures_asc( $form, $tick_all = false ) 
{
	$content = '<div class="heading3">STUDENT DECLARATION AND CONSENT</div>';
	$content .= '<table class="tbl">
					<tr>
						<td style="width: 5%;">';
	
	$content .= $tick_all ? '[YES]' : '[&nbsp;&nbsp;&nbsp;]';
	
	$content .= '		</td>
						<td style="width: 95%;">
							<p>I declare that the information I have provided to the best of my knowledge is true and correct.</p>
						</td>
					</tr>
					<tr>
						<td style="width: 5%;">';
	
	$content .= $tick_all ? '[YES]' : '[&nbsp;&nbsp;&nbsp;]';
	
	$content .= '		</td>
						<td style="width: 95%;">
							<p>I consent to the collection, use and disclosure of my personal information in accordance with the Privacy Notice above.</p>
						</td>
					</tr>
			
				</table>';
	
	$content .= '<table class="tbl">
					<tr>
						<td style="width: 25%;">Student Name: </td>
						<td style="width: 75%;">' . $form->first_name . ' ' . $form->surname . '<br/></td>
					</tr>
					<tr>
						<td style="width: 25%;">Signature: </td>
						<td style="width: 75%;"><strong><em>' . $form->signature . '</em></strong><br/></td>
					</tr>
					<tr>
						<td style="width: 25%;">Date: </td>
						<td style="width: 75%;">' . current_time ( "d/m/Y" ) . '</td>
					</tr>
					<tr>
						<td style="width: 25%;">Parent / Guardian Name: </td>
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
					<tr>
						<td colspan="2"><p class="small">Parental/guardian consent is required for all students under the age of 18</p></td>
					</tr>
				</table>';
	
	return $content;
}


// Office Only Use (ASC)
function neca_pdf_content_office_use_only_asc( $form, $show_dates = true ) 
{
	$jrd = JobReadyDateOperations::loadJobReadyDateByCourseNumber ( $form->course_number );
	$course_name = $jrd->course_number;
	$start_date = $jrd->start_date_clean;
	$end_date = $jrd->end_date_clean;
	 
	$content = '<div class="heading3">OFFICE USE ONLY</div>';
	 
	$content .= '<table class="tbl">
					<tr>
						<td colspan="2"><strong>PROGRAM DETAILS (Accredited Short Course)</strong></td>
					</tr>
					<tr>
						<tr>
							<td style="width: 5%;">[&nbsp;&nbsp;&nbsp;]</td>
							<td style="width: 95%;">Battery Storage</td>
						</tr>
						<tr>
							<td style="width: 5%;">[&nbsp;&nbsp;&nbsp;]</td>
							<td style="width: 95%;">Optical Fibre</td>
						</tr>
						<tr>
							<td style="width: 5%;">[&nbsp;&nbsp;&nbsp;]</td>
							<td style="width: 95%;">Construction Induction Card</td>
						</tr>
						<tr>
							<td style="width: 5%;">[&nbsp;&nbsp;&nbsp;]</td>
							<td style="width: 95%;">CPR + LVR</td>
						</tr>
						<tr>
							<td style="width: 5%;">[&nbsp;&nbsp;&nbsp;]</td>
							<td style="width: 95%;">First Aid</td>
						</tr>
						<tr>
							<td style="width: 5%;">[&nbsp;&nbsp;&nbsp;]</td>
							<td style="width: 95%;">Grid Connect</td>
						</tr>
						<tr>
							<td style="width: 5%;">[&nbsp;&nbsp;&nbsp;]</td>
							<td style="width: 95%;">Open Registration</td>
						</tr>
						<tr>
							<td style="width: 5%;">[&nbsp;&nbsp;&nbsp;]</td>
							<td style="width: 95%;">Structured and Coax Cabling</td>
						</tr>
						<tr>
							<td style="width: 5%;">[&nbsp;&nbsp;&nbsp;]</td>
							<td style="width: 95%;">Cert IV of Project Management Practice</td>
						</tr>
						<tr>
							<td style="width: 5%;">[&nbsp;&nbsp;&nbsp;]</td>
							<td style="width: 95%;">Diploma of Project Management Practice</td>
						</tr>
					</tr>
				</table>
				<br/>
				<table class="tbl">
					<tr>
						<td style="width: 25%;">Group Name: </td>
						<td style="width: 75%;">' . $course_name . '</td>
					</tr>';
	 
	if ($show_dates) 
	{
		$content .= '	<tr>
							<td style="width: 25%;">Start Date: </td>
							<td style="width: 75%;">' . $start_date . '</td>
						</tr>
						<tr>
							<td style="width: 25%;">End Date: </td>
							<td style="width: 75%;">' . $end_date . '</td>
						</tr>';
	}
	 
	$content .= '</table>
				<br/>
				<table class="tbl">
					<tr>
						<td style="width: 5%;">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width: 45%;">Government subsidised: </td>
						<td style="width: 25%;">Rates per SCH: </td>
						<td style="width: 25%;">&nbsp;</td>
					</tr>
					<tr>
						<td style="width: 5%;">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width: 45%;">Government subsidised - concession: </td>
						<td style="width: 25%;">Rates per SCH: </td>
						<td style="width: 25%;">&nbsp;</td>
					</tr>
					<tr>
						<td style="width: 5%;">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width: 45%;">Full fee service: </td>
						<td style="width: 25%;">Rates per SCH: </td>
						<td style="width: 25%;">&nbsp;</td>
					</tr>
					<tr>
						<td style="width: 5%;">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width: 45%;">Resources & Amenities Fee: </td>
						<td style="width: 25%;">Rates per SCH: </td>
						<td style="width: 25%;">&nbsp;</td>
					</tr>
				</table>';
	 
	return $content;
}