<?php
/* ************** *
 * PRE-PROCESSING *
 * ************** */
// Pre-Apprentice Application Form is the same thing as Non-Apprentice Application Form (PRE-APP)
add_filter("gform_pre_render_" . PRE_APPRENTICE_APPLICATION_FORM, 'pre_apprentice_application_form_prepopulate');
add_filter("gform_pre_validation_" . PRE_APPRENTICE_APPLICATION_FORM, 'pre_apprentice_application_form_prepopulate');
add_filter("gform_pre_submission_filter_" . PRE_APPRENTICE_APPLICATION_FORM, 'pre_apprentice_application_form_prepopulate');
add_filter("gform_admin_pre_render" . PRE_APPRENTICE_APPLICATION_FORM, 'pre_apprentice_application_form_prepopulate');

function pre_apprentice_application_form_prepopulate($form)
{
	$prefill_fields= array();

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
		return $form;
	}
	
	if(isset($_SESSION['prefill']))
	{
		// 2024.01.03 - Add new field
		$prefill_fields['183'] = $_SESSION['prefill']->previously_enrolled_at_neca;
		
		$prefill_fields['2'] = $_SESSION['prefill']->title;
		$prefill_fields['9'] = $_SESSION['prefill']->first_name;
		//$prefill_fields['81'] = $_SESSION['prefill']->middle_name;
		$prefill_fields['8'] = $_SESSION['prefill']->surname;
		$prefill_fields['10'] = $_SESSION['prefill']->known_by;
		$prefill_fields['69'] = $_SESSION['prefill']->gender;
		$prefill_fields['11'] = $_SESSION['prefill']->birth_date;
		$prefill_fields['20'] = $_SESSION['prefill']->home_phone;
		$prefill_fields['19'] = $_SESSION['prefill']->mobile_phone;
		
		/*
		$prefill_fields['21'] = array(	'21' => $_SESSION['prefill']->email,
				'21.2' => $_SESSION['prefill']->email );
		*/
		
		// 2023.11.13 - Replaced street_address1 with with street number and street name
		// $prefill_fields['104'] = $_SESSION['prefill']->street_address1;
		$prefill_fields['177'] = $_SESSION['prefill']->street_number;
		$prefill_fields['178'] = $_SESSION['prefill']->street_name;
		$prefill_fields['105'] = $_SESSION['prefill']->suburb;
		$prefill_fields['106'] = $_SESSION['prefill']->state;
		$prefill_fields['107'] = $_SESSION['prefill']->postcode;
		//$prefill_fields['82'] = $_SESSION['prefill']->postal_address_same;
		// 2023.11.13 - Replaced postal_street_address1 with with street number and street name
		//$prefill_fields['108'] = $_SESSION['prefill']->postal_street_address1;
		$prefill_fields['179'] = $_SESSION['prefill']->postal_street_number;
		$prefill_fields['180'] = $_SESSION['prefill']->postal_street_name;
		$prefill_fields['109'] = $_SESSION['prefill']->postal_suburb;
		$prefill_fields['110'] = $_SESSION['prefill']->postal_state;
		$prefill_fields['111'] = $_SESSION['prefill']->postal_postcode;
		
		$prefill_fields['72'] = $_SESSION['prefill']->emergency_contact_firstname;
		$prefill_fields['71'] = $_SESSION['prefill']->emergency_contact_surname;
		$prefill_fields['74'] = $_SESSION['prefill']->emergency_contact_relationship;
		$prefill_fields['73'] = $_SESSION['prefill']->emergency_contact_number;
		
		/*
		$prefill_fields['129'] = array(	'129' => $_SESSION['prefill']->emergency_contact_email,
				'129.2' => $_SESSION['prefill']->emergency_contact_email);
		*/
		
		$prefill_fields['171'] = $_SESSION['prefill']->labour_force_status;
		
		$prefill_fields['118'] = $_SESSION['prefill']->referred;
		$prefill_fields['119'] = $_SESSION['prefill']->referred_details;
		
		$prefill_fields['112'] = $_SESSION['prefill']->country_of_birth;
		$prefill_fields['172'] = $_SESSION['prefill']->indigenous_status;
		$prefill_fields['38'] = $_SESSION['prefill']->main_language == 'English' ? 'No, English only' : 'Yes';
		$prefill_fields['101'] = $_SESSION['prefill']->main_language;
		
		$prefill_fields['45'] = $_SESSION['prefill']->at_school_flag;
		$prefill_fields['168'] = $_SESSION['prefill']->highest_school_level;
		
		$prefill_fields['42'] = $_SESSION['prefill']->disability_flag;
		$prefill_fields['122'] = $_SESSION['prefill']->disability_other;
		$prefill_fields['50'] = $_SESSION['prefill']->prior_education_flag;
		$prefill_fields['52'] = $_SESSION['prefill']->prior_education_qualification;
		
		// 2021.03.19 - Added as requested by Ranjita
		// 2023.06.16 - Removed by Lyn Wang
		// 2024.01.03 - Added by Lyn Wang
		$prefill_fields['185'] = isset($_SESSION['prefill']->credit_transfer) ? $_SESSION['prefill']->credit_transfer : '';
		$prefill_fields['189'] = isset($_SESSION['prefill']->rpl) ? $_SESSION['prefill']->rpl : '';
		
		$prefill_fields['68'] = $_SESSION['prefill']->study_reason;
		$prefill_fields['116'] = $_SESSION['prefill']->industry_employment;
		$prefill_fields['66'] = $_SESSION['prefill']->occupation;
		// 2024.01.17 - Removed by Lyn Wang
		//$prefill_fields['117'] = $_SESSION['prefill']->concession_flag;
		$prefill_fields['67'] = $_SESSION['prefill']->how_did_you_hear;
		
		$prefill_fields['54'] = $_SESSION['prefill']->usi_number;
		
		$prefill_fields['57'] = $_SESSION['prefill']->previous_victorian_education;
		$prefill_fields['56'] = $_SESSION['prefill']->vsn;
		$prefill_fields['58'] = $_SESSION['prefill']->previous_victorian_school;
		$prefill_fields['59'] = $_SESSION['prefill']->previous_victorian_training;

		/* 2023.01.16 - Remove request by Ashima Nakra (email)
		// 2021.03.15 - Required for Skills First Program PDF
		$prefill_fields['150'] = isset($_SESSION['prefill']->highest_qualification_completed) ? $_SESSION['prefill']->highest_qualification_completed : '';
		$prefill_fields['152'] = isset($_SESSION['prefill']->government_funded_enrolments_this_year) ? $_SESSION['prefill']->government_funded_enrolments_this_year : '';
		$prefill_fields['153'] = isset($_SESSION['prefill']->government_funded_undertakings_at_present) ? $_SESSION['prefill']->government_funded_undertakings_at_present : '';
		$prefill_fields['154'] = isset($_SESSION['prefill']->government_funded_in_lifetime) ? $_SESSION['prefill']->government_funded_in_lifetime : '';

		// 2021.03.19 - Additional fields added specifically for Pre-Apprenticeship Skills First Program only
		$prefill_fields['171'] = isset($_SESSION['prefill']->jobtrainer) ? $_SESSION['prefill']->jobtrainer : '';
		$prefill_fields['172'] = isset($_SESSION['prefill']->jobtrainer_previously_started) ? $_SESSION['prefill']->jobtrainer_previously_started : '';
		$prefill_fields['173'] = isset($_SESSION['prefill']->jobtrainer_recommence) ? $_SESSION['prefill']->jobtrainer_recommence : '';
		$prefill_fields['174'] = isset($_SESSION['prefill']->jobtrainer_17_to_24) ? $_SESSION['prefill']->jobtrainer_17_to_24 : '';
		$prefill_fields['175'] = isset($_SESSION['prefill']->jobtrainer_job_seeker) ? $_SESSION['prefill']->jobtrainer_job_seeker : '';
		$prefill_fields['178'] = isset($_SESSION['prefill']->jobtrainer_applicable) ? $_SESSION['prefill']->jobtrainer_applicable : '';
		$prefill_fields['179'] = isset($_SESSION['prefill']->jobtrainer_declaration) ? $_SESSION['prefill']->jobtrainer_declaration : '';

		// 2021.03.15 - continued...
		$prefill_fields['156'] = isset($_SESSION['prefill']->enrolled_in_a_school) ? $_SESSION['prefill']->enrolled_in_a_school : '';
		$prefill_fields['157'] = isset($_SESSION['prefill']->enrolled_in_skills_for_education) ? $_SESSION['prefill']->enrolled_in_skills_for_education : '';
		$prefill_fields['158'] = isset($_SESSION['prefill']->subsidized_acknowledgement) ? $_SESSION['prefill']->subsidized_acknowledgement : '';
		$prefill_fields['159'] = isset($_SESSION['prefill']->contacted_by_department_acknowledgement) ? $_SESSION['prefill']->contacted_by_department_acknowledgement : '';
		*/
		
		
		// 2021.07.22 - Required for the Pre-Training Review
		/* 2023.01.16 - Remove request by Ashima Nakra (email)
		$prefill_fields['189'] = isset($_SESSION['prefill']->expectation_outline_reviewed) ? $_SESSION['prefill']->expectation_outline_reviewed : '';
		$prefill_fields['190'] = isset($_SESSION['prefill']->expectation_working_environment) ? $_SESSION['prefill']->expectation_working_environment : '';
		$prefill_fields['191'] = isset($_SESSION['prefill']->expectation_reason_and_outcome) ? $_SESSION['prefill']->expectation_reason_and_outcome : '';
		$prefill_fields['192'] = isset($_SESSION['prefill']->expectation_knowledge_and_appeal) ? $_SESSION['prefill']->expectation_knowledge_and_appeal : '';
		$prefill_fields['193'] = isset($_SESSION['prefill']->expectation_why_neca) ? $_SESSION['prefill']->expectation_why_neca : '';
		$prefill_fields['195'] = isset($_SESSION['prefill']->ple_difficulties_flag) ? $_SESSION['prefill']->ple_difficulties_flag : '';
		$prefill_fields['196'] = isset($_SESSION['prefill']->ple_difficulties) ? $_SESSION['prefill']->ple_difficulties : '';
		$prefill_fields['197'] = isset($_SESSION['prefill']->ple_concerns) ? $_SESSION['prefill']->ple_concerns : '';
		$prefill_fields['187'] = isset($_SESSION['prefill']->learning_style) ? $_SESSION['prefill']->learning_style : '';
		$prefill_fields['188'] = isset($_SESSION['prefill']->learning_preference) ? $_SESSION['prefill']->learning_preference : '';
		*/
		
		
		// 2021.07.22 - Pre-Training Review Form - Changes
		/* 2023.01.16 - Remove request by Ashima Nakra (email)
		$prefill_fields['205'] = isset($_SESSION['prefill']->computer_access_internet) ? $_SESSION['prefill']->computer_access_internet: '';
		$prefill_fields['206'] = isset($_SESSION['prefill']->computer_usage) ? $_SESSION['prefill']->computer_usage: '';
		$prefill_fields['182'] = isset($_SESSION['prefill']->computer_turn_on) ? $_SESSION['prefill']->computer_turn_on: '';
		$prefill_fields['183'] = isset($_SESSION['prefill']->computer_email) ? $_SESSION['prefill']->computer_email: '';
		$prefill_fields['184'] = isset($_SESSION['prefill']->computer_website) ? $_SESSION['prefill']->computer_website: '';
		$prefill_fields['185'] = isset($_SESSION['prefill']->computer_search) ? $_SESSION['prefill']->computer_search: '';
		$prefill_fields['209'] = isset($_SESSION['prefill']->computer_attach_email) ? $_SESSION['prefill']->computer_attach_email: '';
		$prefill_fields['210'] = isset($_SESSION['prefill']->computer_online_system) ? $_SESSION['prefill']->computer_online_system: '';
		*/
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
				//$prefill_fields['81'] = $form_fields->middle_name;
				$prefill_fields['8'] = $form_fields->surname;
				$prefill_fields['10'] = $form_fields->known_by;
				$prefill_fields['69'] = $form_fields->gender;
				$prefill_fields['11'] = $form_fields->birth_date;
				$prefill_fields['20'] = $form_fields->home_phone;
				$prefill_fields['19'] = $form_fields->mobile_phone;
				
				/*
				$prefill_fields['21'] = array(	'21' => $form_fields->email,
						'21.2' => $form_fields->email );
				*/
				
				// 2023.11.13 - Replaced street_address1 with with street number and street name
				//$prefill_fields['104'] = $form_fields->street_address1;
				$prefill_fields['177'] = $form_fields->street_number;
				$prefill_fields['178'] = $form_fields->street_name;
				$prefill_fields['105'] = $form_fields->suburb;
				$prefill_fields['106'] = $form_fields->state;
				$prefill_fields['107'] = $form_fields->postcode;
				
				//$prefill_fields['82'] = $form_fields->postal_address_same;
				// 2023.11.13 - Replaced postal_street_address1 with with street number and street name
				//$prefill_fields['108'] = $form_fields->postal_street_address1;
				$prefill_fields['179'] = $form_fields->postal_street_number;
				$prefill_fields['180'] = $form_fields->postal_street_name;
				$prefill_fields['109'] = $form_fields->postal_suburb;
				$prefill_fields['110'] = $form_fields->postal_state;
				$prefill_fields['111'] = $form_fields->postal_postcode;
				
				$prefill_fields['171'] = $form_fields->labour_force_status;
				$prefill_fields['112'] = $form_fields->country_of_birth;
				$prefill_fields['172'] = $form_fields->indigenous_status;
				$prefill_fields['101'] = $form_fields->main_language;
				$prefill_fields['38'] = $form_fields->main_language == 'English' ? 'No, English only' : 'Yes';
				$prefill_fields['45'] = $form_fields->at_school_flag;
				$prefill_fields['168'] = $form_fields->highest_school_level;
				$prefill_fields['42'] = $form_fields->disability_flag;
				$prefill_fields['50'] = $form_fields->prior_education_flag;
				
				$prefill_fields['113'] = $form_fields->usi_flag;
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
						$prefill_fields['72'] = $jra_party_contact->first_name;
						$prefill_fields['71'] = $jra_party_contact->surname;
						$prefill_fields['74'] = $jra_party_contact->relationship;
						$prefill_fields['73'] = $jra_party_contact->phone;
						
						/*
						$prefill_fields['129'] = array(	'129' => $jra_party_contact->email,
								'129.2' => $jra_party_contact->email);
						*/
						
						$primary_party_contact_found= true;
						break;
					}
				}
				
				// If there was no primary contact, use the first party contact
				if(!$primary_party_contact_found)
				{
					$prefill_fields['72'] = $jra_party_contacts[0]->first_name;
					$prefill_fields['71'] = $jra_party_contacts[0]->surname;
					$prefill_fields['74'] = $jra_party_contacts[0]->relationship;
					$prefill_fields['73'] = $jra_party_contacts[0]->phone;
					
					/*
					$prefill_fields['129'] = array(	'129' => $jra_party_contacts[0]->email,
							'129.2' => $jra_party_contacts[0]->email);
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
		// 14.10.2021 - Cost removed / disabled as requested by Linda via email 14/10/2021
		// 23.06.2023 - Cost re-enabled as requestey by Lyn Wang / Cheryl via email 23/06/2023
		// 05.04.2024 - Cost removed / disabled as requested by Lyn Wang via email 04/04/2024
		/*
		if($field->id == 174)
		{
			$choices = jrar_invoice_options($course_number, $neca_member);
			$field->choices = $choices;
		}
		*/
		
		// Title
		if($field->id == 2)
		{
			$field->choices = jrar_title();
		}
		
		// Gender
		if($field->id == 69)
		{
			$field->choices = jrar_gender();
		}
		
		// Employment Category
		if($field->id == 171)
		{
			$field->choices = jrar_employment_category();
		}
		
		// States
		if($field->id == 106 || $field->id == 110)
		{
			$field->choices = jrar_state();
		}
		
		// Country
		if($field->id == 112 || $field->id == 100)
		{
			$field->choices = jrar_country();
		}
		
		// Language
		if($field->id == 101)
		{
			$field->choices = jrar_language();
		}
		
		// Indigenous Status
		if($field->id == 172)
		{
			$field->choices = jrar_indigenous_status();
		}
		
		// Highest School Level
		if($field->id == 168)
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
				
				if(isset($form_fields) && array_search($choice['value'], $form_fields->disability_types))
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
				
				if(isset($form_fields) && array_search($choice['value'], $form_fields->prior_educations))
				{
					$choice['isSelected'] = true;
				}
				array_push($prior_education_choices, $choice);
			}
			
			$field->choices = $prior_education_choices;
		}
		
		// Client Industry Employer
		if($field->id == 116)
		{
			$field->choices = jrar_client_industry_employer();
		}
		
		// Client Occupation Identifier
		if($field->id == 66)
		{
			$field->choices = jrar_client_occupation_identifer();
		}
		
		// Study Reason
		if($field->id == 68)
		{
			$field->choices = jrar_study_reason();
		}
		
		// Client Industry
		if($field->id == 116)
		{
			$field->choices = jrar_client_industry_employer();
		}
		
		// Client Occupation
		if($field->id == 66)
		{
			$field->choices = jrar_client_occupation_identifer();
		}
		
		
		// Course Cost (update label to be course name and date)
		if($field->id == 29)
		{
			$total_label = $jrd->course_name . " (" . $jrd->start_date_clean. " to " . $jrd->end_date_clean . ")";
			$field->label = $total_label;
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
// Pre-Apprentice Application Form is the same thing as Non-Apprentice Application Form (PRE-APP)
add_filter("gform_pre_submission_" . PRE_APPRENTICE_APPLICATION_FORM, 'non_apprentice_application_form_presubmission');

function non_apprentice_application_form_presubmission()
{
	if(PREAPPRENTICE_DEBUG_MODE)
	{
		echo "<div>POST Variables: <br/>";
		var_dump($_POST);
		echo "<br/><br/></div>";
	}
	
	if(isset($_SESSION['prefill']))
	{
		$prefill = $_SESSION['prefill'];
	}
	else
	{
		$prefill = new stdClass();
	}
	
	$prefill->previously_enrolled_at_neca = $_POST['input_183'];
	$prefill->title = $_POST['input_2'];
	$prefill->first_name = $_POST['input_9'];
	//$prefill->middle_name = $_POST['input_81'];
	$prefill->surname = $_POST['input_8'];
	$prefill->known_by = $_POST['input_10'];
	$prefill->gender = $_POST['input_69'];
	$prefill->birth_date = $_POST['input_11'];
	$prefill->home_phone = $_POST['input_20'];
	$prefill->mobile_phone = $_POST['input_19'];
	$prefill->email = $_POST['input_21'];
	
	// 2023.11.13 - Replaced street address1 with with street number and street name
	//$prefill->street_address1 = $_POST['input_104'];
	$prefill->street_number = $_POST['input_177'];
	$prefill->street_name = $_POST['input_178'];
	$prefill->suburb = $_POST['input_105'];
	$prefill->state = $_POST['input_106'];
	$prefill->postcode = $_POST['input_107'];
	
	$prefill->postal_address_same = isset($_POST['input_82_1']) ? "Yes" : "";
	// 2023.11.13 - Replaced postal_street_address1 with with street number and street name
	//$prefill->postal_street_address1 = $_POST['input_108'];
	$prefill->postal_street_number = $_POST['input_179'];
	$prefill->postal_street_name = $_POST['input_180'];
	$prefill->postal_suburb = $_POST['input_109'];
	$prefill->postal_state = $_POST['input_110'];
	$prefill->postal_postcode = $_POST['input_111'];
	
	$prefill->emergency_contact_firstname = $_POST['input_72'];
	$prefill->emergency_contact_surname = $_POST['input_71'];
	$prefill->emergency_contact_number = $_POST['input_73'];
	$prefill->emergency_contact_email = $_POST['input_129'];
	$prefill->emergency_contact_relationship = $_POST['input_74'];
	
	$prefill->labour_force_status = $_POST['input_171'];
	
	$prefill->referred = $_POST['input_118'];
	$prefill->referred_details = $_POST['input_119'];
	
	$prefill->country_of_birth = $_POST['input_112'];
	$prefill->indigenous_status = $_POST['input_172'];
	$prefill->main_language = ($_POST['input_38'] == "No, English only") ? 'English' : $_POST['input_101'];
	
	$prefill->at_school_flag = $_POST['input_45'];
	$prefill->highest_school_level = $_POST['input_168'];
	
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
		$prefill->disability_other = $_POST['input_122'];
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
		$prefill->prior_education_qualification = $_POST['input_52'];
	}
	
	// 2021.02.15 - Added as requested by Ranjita
	// 2023.06.16 - Removed by Lyn Wang
	// 2024.01.03 - Added by Lyn Wang
	$prefill->credit_transfer = $_POST['input_211'];
	$prefill->rpl = $_POST['input_169'];
	
	$prefill->usi_number = $_POST['input_54'];
	
	$prefill->study_reason = $_POST['input_68'];
	$prefill->industry_employment = $_POST['input_116'];
	$prefill->occupation = $_POST['input_66'];
	// 2024.01.17 - Removed by Lyn Wang
	//$prefill->concession_flag = $_POST['input_117'];
	$prefill->how_did_you_hear = $_POST['input_67'];
	
	$prefill->previous_victorian_education = $_POST['input_57'];
	$prefill->vsn = $_POST['input_56'];
	$prefill->previous_victorian_school = $_POST['input_58'];
	$prefill->previous_victorian_training = $_POST['input_59'];
	
	// 2021.03.15 - Required for Skills First Program PDF
	/* 2023.01.16 - Remove request by Ashima Nakra (email)
	if(isset($_POST['input_150']))
		$prefill->highest_qualification_completed = ucwords(strtolower($_POST['input_150']));
		
	if(isset($_POST['input_152']))
		$prefill->government_funded_enrolments_this_year = $_POST['input_152'];
		
	if(isset($_POST['input_153']))
		$prefill->government_funded_undertakings_at_present = $_POST['input_153'];
		
	if(isset($_POST['input_154']))
		$prefill->government_funded_in_lifetime = $_POST['input_154'];
		
	// 2021.03.19 - Additional fields added specifically for Pre-Apprenticeship Skills First Program
	if(isset($_POST['input_171']))
		$prefill->jobtrainer = $_POST['input_171'];
		
	if(isset($_POST['input_172']))
		$prefill->jobtrainer_previously_started = $_POST['input_172'];
		
	if(isset($_POST['input_173']))
		$prefill->jobtrainer_recommence = $_POST['input_173'];
		
	if(isset($_POST['input_174']))
		$prefill->jobtrainer_17_to_24 = $_POST['input_174'];
		
	if(isset($_POST['input_175']))
		$prefill->jobtrainer_job_seeker = $_POST['input_175'];
		
	if(isset($_POST['input_178']))
		$prefill->jobtrainer_applicable = $_POST['input_178'];
		
	if(isset($_POST['input_179']))
		$prefill->jobtrainer_declaration = $_POST['input_179'];
		
	// 2021.03.15 - continued...
	if(isset($_POST['input_156']))
		$prefill->enrolled_in_a_school = $_POST['input_156'];
		
	if(isset($_POST['input_157']))
		$prefill->enrolled_in_skills_for_education = $_POST['input_157'];
		
	if(isset($_POST['input_158']))
		$prefill->subsidized_acknowledgement = $_POST['input_158'];
		
	if(isset($_POST['input_159']))
		$prefill->contacted_by_department_acknowledgement = $_POST['input_159'];
		
	// 2018.10.17 - Required for the Pre-Training Review
	if(isset($_POST['input_189']))
		$prefill->expectation_outline_reviewed = $_POST['input_189'];
		
	if(isset($_POST['input_190']))
		$prefill->expectation_working_environment = $_POST['input_190'];
		
	if(isset($_POST['input_191']))
		$prefill->expectation_reason_and_outcome = ucwords(strtolower($_POST['input_191']));
		
	if(isset($_POST['input_192']))
		$prefill->expectation_knowledge_and_appeal = ucwords(strtolower($_POST['input_192']));
		
	if(isset($_POST['input_193']))
		$prefill->expectation_why_neca = ucwords(strtolower($_POST['input_193']));
		
	if(isset($_POST['input_195']))
		$prefill->ple_difficulties_flag = $_POST['input_195'];
		
	if(isset($_POST['input_196']))
		$prefill->ple_difficulties = ucwords(strtolower($_POST['input_196']));
		
	if(isset($_POST['input_197']))
		$prefill->ple_concerns = ucwords(strtolower($_POST['input_197']));
		
	if(isset($_POST['input_187']))
		$prefill->learning_style = $_POST['input_187'];
		
	if(isset($_POST['input_188']))
		$prefill->learning_preference = $_POST['input_188'];
	*/
	
	
		
	// 2021.07.22 - Pre-Training Review Form Updates
	/* 2023.01.16 - Remove request by Ashima Nakra (email)
	if(isset($_POST['input_205']))
		$prefill->computer_access_internet = $_POST['input_205'];
		
	if(isset($_POST['input_206']))
		$prefill->computer_usage= $_POST['input_206'];
		
	if(isset($_POST['input_182']))
		$prefill->computer_turn_on= $_POST['input_182'];
		
	if(isset($_POST['input_183']))
		$prefill->computer_email= $_POST['input_183'];
		
	if(isset($_POST['input_184']))
		$prefill->computer_website= $_POST['input_184'];
		
	if(isset($_POST['input_185']))
		$prefill->computer_search= $_POST['input_185'];
		
	if(isset($_POST['input_209']))
		$prefill->computer_attach_email= $_POST['input_209'];
		
	if(isset($_POST['input_210']))
		$prefill->computer_online_system= $_POST['input_210'];
	*/
		
	$_SESSION['prefill'] = $prefill;
	
	if(PREAPPRENTICE_DEBUG_MODE)
	{
		echo "<div>SESSION Prefile Variable: <br/>";
		var_dump($_SESSION['prefill']);
		echo "<br/><br/></div>";
	}
}


/* *************** *
 * POST-PROCESSING *
 * *************** */

// Calls the fuction "pre_apprentice_application_form_submission_process" after "Form #62: Pre-Apprenticeship Application Form" has been submitted

// 05.04.2024 - Payment for PRE APPRENTICESHIP APPLICATION FORM removed as requested by Lyn Wang
// This is the function used when payments go through the form
// function pre_apprentice_application_form_submission_process($entry_id, $form_data, $order, $item_cost)

// This action is required to handle application form submission when no payment is applicable
add_action('gform_after_submission_' . PRE_APPRENTICE_APPLICATION_FORM, 'pre_apprentice_application_form_submission_process', 10, 2);
function pre_apprentice_application_form_submission_process($entry, $form_data)
{
	if (PREAPPRENTICE_DEBUG_MODE) 
	{
		echo "<div><h3>Pre-Apprentice Application Form - Submission Process</h3>";
	}

	// 05.04.2024 - Payment has been removed from this function call is no longer required as Entry is passed into the function
	// Load the Entry
	// $entry = GFAPI::get_entry( $entry_id );

	if(PREAPPRENTICE_DEBUG_MODE)
	{
		echo "Entry Data Dump: <br/>";
		var_dump($entry);
		echo "<br/><br/></div>";
	}
	
	$form = new JobReadyForm ();
	
	// Course Details
	$form->course_scope_code = rgar($entry, '77');
	$form->course_number = rgar($entry, '78');
	
	// 23.06.2023 - Payments re-enabled by Lyn Wang
	// 05.04.2024 - Payment disabled by Lyn Wang
	//$form->invoice_option = rgar($entry, '174');
	//$form->cost = $item_cost;
	
	// 03.01.2024 - Previously enrolled at NECA
	$form->previously_enrolled_at_neca = rgar($entry, '183');
	
	// Personal Details
	$form->gender = rgar($entry, '69');
	$form->title = rgar($entry, '2');
	$form->first_name = ucwords ( strtolower ( rgar($entry, '9')) );
	//$form->middle_name = ucwords ( strtolower ( rgar($entry, '81')) );
	$form->surname = ucwords ( strtolower ( rgar($entry, '8')) );
	$form->known_by = ucwords ( strtolower ( rgar($entry, '10')) );
	$form->birth_date = rgar($entry, '11');
	
	// Contact Details
	$form->home_phone = rgar($entry, '20');
	$form->mobile_phone = rgar($entry, '19');
	$form->email = strtolower ( rgar($entry, '21'));
	
	// Address
	// 2023.11.13 - Replaced street address1 with with street number and street name
	//$form->street_address1 = ucwords ( strtolower ( rgar($entry, '104')) );
	$form->street_number = ucwords ( strtolower ( rgar($entry, '177')) );
	$form->street_name = ucwords ( strtolower ( rgar($entry, '178')) );
	$form->street_address1 = $form->street_number . ' ' . $form->street_name;
	$form->suburb = ucwords ( strtolower ( rgar($entry, '105')) );
	$form->state = ucwords ( strtolower ( rgar($entry, '106')) );
	$form->postcode = rgar($entry, '107');
	
	$form->postal_address_same = rgar($entry, '82.1');
	
	if ($form->postal_address_same != 'Yes') 
	{
		// Address
		// 2023.11.13 - Replaced postal_street_address1 with with street number and street name
		// $form->postal_street_address1 = ucwords ( strtolower ( rgar($entry, '108')) );
		$form->postal_street_number = ucwords ( strtolower ( rgar($entry, '179')) );
		$form->postal_street_name = ucwords ( strtolower ( rgar($entry, '180')) );
		$form->postal_street_address1 = $form->postal_street_number . ' ' . $form->postal_street_name;
		$form->postal_suburb = ucwords ( strtolower ( rgar($entry, '109')) );
		$form->postal_state = ucwords ( strtolower ( rgar($entry, '110')) );
		$form->postal_postcode = rgar($entry, '111');
	}
	
	// Emergency Contact
	$form->emergency_contact_firstname = ucwords ( strtolower ( rgar($entry, '72')) );
	$form->emergency_contact_surname = ucwords ( strtolower ( rgar($entry, '71')) );
	$form->emergency_contact_number = rgar($entry, '73');
	$form->emergency_contact_email = rgar($entry, '129');
	$form->emergency_contact_relationship = ucwords ( strtolower ( rgar($entry, '74')) );
	
	// Labour Force
	$form->labour_force_status = rgar($entry, '171');
	
	// Referred from a Job Seeker
	$form->referred = rgar($entry, '118');
	if ($form->referred == 'Yes') 
	{
		$form->referred_details = rgar($entry, '119');
	}
	
	// Birth + Nationality + Indigenous + Language
	$form->country_of_birth = rgar($entry, '112');
	
	$form->indigenous_status = rgar($entry, '172');
	$language_other_than_english = rgar($entry, '38');
	if ($language_other_than_english != 'Yes') 
	{
		$form->main_language = "English";
		// $form->spoken_english_proficiency= "Very Well";
	}
	else 
	{
		$form->main_language = rgar($entry, '101');
		// $form->spoken_english_proficiency= "Very Well";
	}
	 
	// School Details
	$form->at_school_flag = rgar($entry, '45');
	$form->highest_school_level = rgar($entry, '168');
	 
	// Disability
	$form->disability_types = array ();
	$form->disability_flag = rgar($entry, '42');
	if ($form->disability_flag == 'Yes') 
	{
		for($i = 1; $i < 20; $i ++) 
		{
			$ref = '43.' . $i;
			if ($entry[$ref] != '') 
			{
				$form->disability_types [] = $entry[$ref];
			}
		}
		$form->disability_other = ucwords ( strtolower ( rgar($entry, '44')) );
	}
	 
	// Prior Education
	$form->prior_education_flag = rgar($entry, '50');
	$form->prior_educations = array ();
	if ($form->prior_education_flag == 'Yes') 
	{
		for($i = 1; $i <= 20; $i ++) 
		{
			$ref = '51.' . $i;
			if ($entry[$ref] != '') 
			{
				$form->prior_educations [] = $entry[$ref];
			}
		}
		$form->prior_education_qualification = rgar($entry, '52');
	}
	
	// 2021.03.19 - Credit Transfer Added
	// 2023.06.16 - Removed by Lyn Wang
	// 2024.01.03 - Added again by Lyn Wang
	$form->credit_transfer = rgar($entry, '185');
	$form->rpl = rgar($entry, '189');
	
	// 2024.01.03 - Added File Field for USI Transcript
	$form->file_usi_transcript = rgar($entry, '187');
	
	// Unique Student Number
	$form->usi_number = rgar($entry, '54');
	 
	// Enrolment > Skills VIC AVETMISS
	$form->study_reason = rgar($entry, '68');
	$form->industry_employment = rgar($entry, '116');
	$form->occupation = rgar($entry, '66');
	
	// 2024.01.17 - Removed by Lyn Wang
	//$form->concession_flag = rgar($entry, '117');
	
	// 2024.01.03 - Would you describe yourself as belonging to any of the following cohorts?
	$form->cohorts = array();
	for($i = 1; $i < 20; $i ++)
	{
		$ref = '193.' . $i;
		$cohort_value = rgar($entry, $ref);
		if ($cohort_value != '' && $cohort_value != 'NNNNNN')
		{
			$form->cohorts [] = $cohort_value;
		}
	}	
	
	$form->how_did_you_hear = ucwords ( strtolower ( rgar($entry, '67')) );
	 
	// Victorian Student
	$form->previous_victorian_education = rgar($entry, '57');
	if (strpos ( $form->previous_victorian_education, 'Yes' ) !== false) 
	{
		$form->vsn = rgar($entry, '56');
	}
	
	if (rgar($entry, '58')!= '') 
	{
		$form->previous_victorian_school = ucwords ( strtolower ( rgar($entry, '58')) );
	}
	elseif (rgar($entry, '59')!= '') 
	{
		$form->previous_victorian_school = ucwords ( strtolower ( rgar($entry, '59')) );
	}
	
	// Student Declaraction
	$form->privacy_declaration = rgar($entry, '130.1');
	
	// 2021.03.15 - Required for Skills First Program PDF
	/* 2023.01.16 - Remove request by Ashima Nakra (email)
	$form->highest_qualification_completed = rgar($entry, '150');
	$form->government_funded_enrolments_this_year = rgar($entry, '152');
	$form->government_funded_undertakings_at_present = rgar($entry, '153');
	$form->government_funded_in_lifetime = rgar($entry, '154');
	
	// 2021.03.19 - Added additional form fields for Pre-Apprentice Skills First Program only
	$form->jobtrainer = rgar($entry, '171');
	
	if($form->jobtrainer == 'Yes')
	{
		$form->jobtrainer_previously_started = rgar($entry, '172');
		if($form->jobtrainer_previously_started == 'Yes')
		{
			$form->jobtrainer_recommence = rgar($entry, '173');
			if($form->jobtrainer_recommence == 'Yes')
			{
				$form->jobtrainer_17_to_24 = rgar($entry, '174');
				if($form->jobtrainer_17_to_24 == 'Yes')
				{
					$form->jobtrainer_job_seeker = rgar($entry, '175');
					if($form->jobtrainer_job_seeker == 'Yes')
					{
						$form->jobtrainer_applicable = array();
						for($i = 1; $i <= 5; $i ++)
						{
							$ref = '178.' . $i;
							if ($entry[$ref] != '')
							{
								array_push($form->jobtrainer_applicable, $entry[$ref]);
							}
						}
						
						if(isset($entry['179.1']))
						{
							$form->jobtrainer_declaration = rgar($entry, '179.1');
						}
					}
				}
			}
		}
	}
	
	// 2021.03.15 - continued....
	$form->enrolled_in_a_school = rgar($entry, '156');
	$form->enrolled_in_skills_for_education = rgar($entry, '157');
	$form->subsidized_acknowledgement = rgar($entry, '158');
	$form->contacted_by_department_acknowledgement = rgar($entry, '159');
	*/
	
	
	// 2021.07.22 - Required for the Pre-Training Review
	/* 2023.01.16 - Remove request by Ashima Nakra (email)
	$form->expectation_outline_reviewed = rgar($entry, '189');
	$form->expectation_working_environment = rgar($entry, '190');
	$form->expectation_reason_and_outcome = rgar($entry, '191');
	$form->expectation_knowledge_and_appeal = rgar($entry, '192');
	$form->expectation_why_neca = rgar($entry, '193');
	$form->ple_difficulties_flag = rgar($entry, '195');
	$form->ple_difficulties = rgar($entry, '196');
	$form->ple_concerns = rgar($entry, '197');
	
	$learning_style = rgar($entry, '187');
	$form->learning_style = explode (',' , $learning_style);
	$learning_preference = rgar($entry, '188');
	$form->learning_preference = explode( ',' , $learning_preference);
	
	// 2021.07.22 - Pre-Training Review Form changes
	$form->computer_access_internet = rgar($entry, '205');
	$form->computer_usage = rgar($entry, '206');
	$form->computer_turn_on = rgar($entry, '182');
	$form->computer_email = rgar($entry, '183');
	$form->computer_website = rgar($entry, '184');
	$form->computer_search = rgar($entry, '185');
	$form->computer_attach_email = rgar($entry, '209');
	$form->computer_online_system = rgar($entry, '210');
	*/
	
	$form->signature = rgar($entry, '166');
	
	if (PREAPPRENTICE_DEBUG_MODE) 
	{
		echo "<div>Form Variable: <br/>";
		var_dump ( $form );
		echo "<br/><br/></div>";
	}

	/*
	 * Setup Job Ready Resources and Create Accordingly
	 */
	 
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
	$party->usi_number = $form->usi_number;
	
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
	if ($form->postal_address_same != 'Yes') {
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
	
	if (trim ( $form->home_phone != '' )) {
		$contact_detail = new JRAPartyContactDetail ();
		$contact_detail->primary = '';
		$contact_detail->contact_type = 'Phone';
		$contact_detail->value = $form->home_phone;
		array_push ( $contact_details, $contact_detail );
	}
	
	if (trim ( $form->mobile_phone != '' )) {
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
	*/
	
	$party->adhoc_child = $adhoc_fields;
	  
	if (PREAPPRENTICE_DEBUG_MODE) 
	{
		$jrd = JobReadyDateOperations::loadJobReadyDateByCourseNumber ( $form->course_number );
		echo "<div>JRD Variable: <br/>";
		var_dump ( $jrd );
		echo "<br/><br/>";
		$course_name = $jrd->course_number;
		
		if (trim ( $jrd->course_name ) != '') 
		{
			$course_name .= ' - ' . $jrd->course_name;
		}
		
		$start_date = $jrd->start_date_clean;
		$end_date = $jrd->end_date_clean;
		echo "Course Name: " . $course_name . "<br/>";
		echo "Start Date: " . $start_date . "<br/>";
		echo "End Date: " . $end_date . "<br/><br/></div>";
	}
	
	// Create PDF
	$naafp = pre_apprentice_application_form_pdf ( $form );
	if (PREAPPRENTICE_DEBUG_MODE)
	{
		echo '<div><a href="' . JR_ROOT_URL . '/pdf/' . $naafp. '" target="_blank">Pre-Apprentice Applcation Form (PDF)</a><br/><br/></div>';
	}
	  
	// 2021.03.16 - Create Skills First PDF
	/* 2023.01.16 - Remove request by Ashima Nakra (email)
	$course_name = 'Pre-Apprenticeship Application Form';
	$sfp = skills_first_pdf ( $form, $course_name, true );
	
	if (PREAPPRENTICE_DEBUG_MODE)
	{
		echo '<a href="' . JR_ROOT_URL . '/pdf/' . $sfp. '" target="_blank">Skills First Program (PDF)</a><br/><br/>';
	}
	*/
	
	  
	// 2021.07.22 - Create Pre Training Review PDF
	/* 2023.01.16 - Remove request by Ashima Nakra (email)
	$course_name = 'UEE22011 Certificate II in Electrotechnology (Career Start)';
	$ptrp = pre_training_review_pdf ( $form, $course_name );
	
	if (PREAPPRENTICE_DEBUG_MODE)
	{
		echo '<a href="' . JR_ROOT_URL . '/pdf/' . $ptrp . '" target="_blank">Pre Training Review (PDF)</a><br/><br/>';
	}
	*/
	
	
	// Check if the Party Exists
	if (PREAPPRENTICE_DEBUG_MODE) 
	{
		echo "<div>Check if Party Exists<br/></div>";
	}
	  
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
		
		if (PREAPPRENTICE_DEBUG_MODE) 
		{
			echo "<div>Party Exists - Party ID: " . $party_id . "<br/><br/>";
// 			echo "Result: <br/>";
// 			var_dump ( $party_result );
// 			echo "<br/><br/></div>";
		}
	  	
		// Update Party
		$update_party_xml = JRAPartyOperations::updateJRAPartyXML ( $party );
	  	
		if (PREAPPRENTICE_DEBUG_MODE) 
		{
			echo "<div>Update Party XML: <br/>";
			var_dump ( $update_party_xml );
			echo "<br/><br/></div>";
		}
	  	
		$update_party_result = JRAPartyOperations::updateJRAParty ( $update_party_xml, $party_id );
	  	
		if (isset ( $update_party_result->{'party-identifier'} )) 
		{
			if (PREAPPRENTICE_DEBUG_MODE) 
			{
				echo "<div>Party Updated<br/><br/>";
				echo "Update Party Result: <br/>";
				var_dump ( $update_party_result );
				echo "<br/><br/></div>";
			}
		}
		else 
		{
			if (PREAPPRENTICE_DEBUG_MODE) 
			{
				echo "<div>An error occurred while updating a Party Resource<br/><br/></div>";
			}
			return false;
		}
	}
	else
	{
		if (PREAPPRENTICE_DEBUG_MODE)
		{
			echo "<div>Party does not Exist - Create New Party<br/></div>";
		}
		
		// Create Party
		$party_xml = JRAPartyOperations::createJRAPartyXML ( $party );
		
		if (PREAPPRENTICE_DEBUG_MODE) 
		{
			echo "<div>Party XML: <br/>";
			var_dump ( $party_xml );
			echo "<br/><br/></div>";
		}
		
		$party_result = JRAPartyOperations::createJRAParty ( $party_xml );
		
		if (isset ( $party_result->{'party-identifier'} )) 
		{
			$party_id = ( string ) $party_result->{'party-identifier'};
			if (PREAPPRENTICE_DEBUG_MODE) 
			{
				echo "<div>Party Created - Party ID: " . $party_id . "<br/><br/></div>";
				// echo "Party Result: <br/>";
				// var_dump($party_result);
				// echo "<br/><br/>";
			}
		} 
		else 
		{
			if (PREAPPRENTICE_DEBUG_MODE) 
			{
				echo "<div>An error occurred while creating a Party Resource<br/><br/></div>";
			}
			return false;
		}
	}
	  
	if (isset ( $party_id )) 
	{
		//Create Enrolment
		$enrolment = new JRAEnrolment ();
		$enrolment->party_identifier = $party_id;
		$enrolment->course_number = $form->course_number;
		$enrolment->study_reason = $form->study_reason;
	  	 
		// Disabled until I can retrieve the reference data from Job Ready directly
		$enrolment->client_occupation_identifier = $form->occupation;
		$enrolment->client_industry_employment = $form->industry_employment;
		// 05.04.2024 - Payments + Invoice Options disabled as requested by Lyn Wang
		//$enrolment->invoice_option = $form->invoice_option;
	  	 
		// NOTE: Victorian student number does not have a valid checksum (need to validate before sending to Job Ready)
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

		// 18.01.2024 - Added as requested by Lyn Wang
		// COHORT FIELD
		$cohort_fields = array ();
		foreach($form->cohorts as $cohort)
		{
			$cohort_field = new JRAEnrolmentCommencingProgramCohortIdentifier();
			$cohort_field->code = $cohort;
			array_push ( $cohort_fields, $cohort_field );
		}
		$enrolment->commencing_program_cohort_identifiers = $cohort_fields;
		
		if (PREAPPRENTICE_DEBUG_MODE) 
		{
	  		echo "<div>Create Enrolment<br/></div>";
		}
	  	 
		// Create Enrolment
		$enrolment_xml = JRAEnrolmentOperations::createJRAEnrolmentXML ( $enrolment );
		if (PREAPPRENTICE_DEBUG_MODE) 
		{
			echo "<div>Enrolment XML: <br/>";
			var_dump ( $enrolment_xml );
			echo "<br/><br/></div>";
		}
		$enrolment_result = JRAEnrolmentOperations::createJRAEnrolment ( $enrolment_xml );
	  	 
		if (isset ( $enrolment_result->{'enrolment-identifier'} )) 
		{
			$enrolment_id = ( string ) $enrolment_result->{'enrolment-identifier'};
			if (PREAPPRENTICE_DEBUG_MODE) 
			{
				echo "<div>Enrolment created - Enrolment ID: " . $enrolment_id . "<br/><br/></div>";
			}
		}
		else 
		{
			if (PREAPPRENTICE_DEBUG_MODE) 
			{
				echo "<div>Error occurred while creating Enrolment<br/><br/></div>";
			}
		}
	  	 
		if (isset ( $enrolment_id )) 
		{
			// Update the VSN if it was specified
			if ($enrolment->victorian_student_number) 
			{
				$update_enrolment_vsn_result = JRAEnrolmentOperations::updateJRAEnrolmentVSN ( $enrolment, $enrolment_id );
			}

			/* 14.10.2021 - DISABLED Deposit as requested by Linda via email on 14/10/2021 */
			/* 23.06.2023 - RENABLED Payment as requested by Lyn Wang via email on 23.06.2021 */
			/* 05.04.2024 - DISABLED Payment as requested by Lyn Wang via email on 04.04.2024 */
			/*
			// Load Invoice by Enrolment ID
			$invoices = JRAInvoiceOperations::loadInvoiceByEnrolmentID ( $enrolment_id );
	  	 	
			if (PREAPPRENTICE_DEBUG_MODE) 
			{
				echo "<div>Invoices retrieved from Job Ready: " . count ( $invoices );
				echo "<br/><br/></div>";
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
					if (PREAPPRENTICE_DEBUG_MODE) 
					{
						echo "<div>Payment Created<br/><br/></div>";
					}
				}
				else
				{
					if (PREAPPRENTICE_DEBUG_MODE) 
					{
						echo "<div>Error occured when creating Payment<br/><br/></div>";
					}
				}
			}
			// END OF PAYMENT SECTION
			 */
			
		}
	  	 
		// Create "Party Contact" Resource for Emergency Contact Person
		$party_contact = new JRAPartyContact ();
		$party_contact->contact_method = 'Phone';
		$party_contact->first_name = $form->emergency_contact_firstname;
		$party_contact->surname = $form->emergency_contact_surname;
		$party_contact->phone = preg_replace ( '/\s/', '', $form->emergency_contact_number );
		$party_contact->email = $form->emergency_contact_email;
		$party_contact->relationship = $form->emergency_contact_relationship;
	  	 
		// Check if "Party Contact" exists already
		if (PREAPPRENTICE_DEBUG_MODE) 
		{
			echo "<div>Check if Party Contact Exists<br/></div>";
		}
	  	 
		$jra_party_contacts = JRAPartyContactOperations::getJRAPartyContacts ( $party_id );
	  	 
		// Loops through all party contacts linked to the party_id
		foreach ( $jra_party_contacts as $jra_party_contact ) 
		{
			if ($jra_party_contact->first_name == $party_contact->first_name && $jra_party_contact->surname == $party_contact->surname && $jra_party_contact->phone == $party_contact->phone) 
			{
				$party_contact_id = ( string ) $jra_party_contact->id;
				break;
			}
		}
	  	 
		if (isset ( $party_contact_id )) 
		{
			if (PREAPPRENTICE_DEBUG_MODE) 
			{
				echo "<div>Party Contact Exists<br/><br/></div>";
			}
	  	 	
			// Update Party Contact (email + phone number)
			$party_contact_update_result = JRAPartyContactOperations::updateJRAPartyContact ( $party_id, $party_contact_id, $party_contact );
		}
		else
		{
			if (PREAPPRENTICE_DEBUG_MODE) 
			{
				echo "<div>Create Party Contact<br/><br/></div>";
			}
			
			$party_contact_xml = JRAPartyContactOperations::createJRAPartyContactXML ( $party_contact );
			$party_contact_result = JRAPartyContactOperations::createJRAPartyContact ( $party_id, $party_contact_xml );
			
			if (isset ( $party_contact_result->id )) 
			{
				if (PREAPPRENTICE_DEBUG_MODE) 
				{
					echo "<div>Party Contact created - Party Contact ID: " . $party_contact->id . "<br/><br/></div>";
				}
			}
			else 
			{
				if (PREAPPRENTICE_DEBUG_MODE) 
				{
					echo "<div>Error occured when creating Party Contact<br/><br/></div>";
				}
			}
		}
	  	 
		// Create "PartyDocument" and link the Application Form PDF to the Party
		$pdf_file = JR_ROOT_PATH . '/pdf/' . $naafp;
		$document = new JRAPartyDocument ();
		$document->party_id = $party_id;
		$document->name = 'Pre-Apprentice Application Form (' . $form->course_scope_code . ' - ' . $form->course_number . ')';
		$document->description = 'Pre-Apprentice Application Form (' . $form->course_scope_code . ' - ' . $form->course_number . ')';
		$document->filename = $pdf_file;
	  	 
		if (PREAPPRENTICE_DEBUG_MODE) 
		{
			echo "<div>Create Party Document<br/></div>";
		}
	  	 
		$party_document_result = JRAPartyDocumentOperations::createJRAPartyDocument ( $party_id, $document );
	  	 
		if (isset ( $party_document_result->id )) 
		{
			if (PREAPPRENTICE_DEBUG_MODE) 
			{
				echo "<div>Party Document Created - Party Document ID: " . $party_document_result->id . "<br/></div>";
			}
	  	 	
			// Remove PDF from server
			if (!PREAPPRENTICE_DEBUG_MODE)
			{
				unlink ( $pdf_file );
			}
			
			if (PREAPPRENTICE_DEBUG_MODE) 
			{
				echo "<div>PDF file removed from web server<br/><br/></div>";
			}
		}
		else
		{
			if (PREAPPRENTICE_DEBUG_MODE) 
			{
				echo "<div>Error occurred when created Party Document<br/><br/></div>";
			}
		}
		
		
		
		// 2024.01.03 - Upload the USI TRANSCRIPT FILE Party Document
		if(trim($form->file_usi_transcript) != '')
		{
			$url_components = parse_url($form->file_usi_transcript);
			
			$usi_file = ABSPATH . $url_components['path'];
			$document = new JRAPartyDocument();
			$document->party_id = $party_id;
			$document->name = 'USI Transcript - ' . $form->first_name . ' ' . $form->surname;
			$document->description = 'USI Transcript';
			$document->document_category = 'Application Form';
			$document->document_type = 'Resources';
			$document->filename = $usi_file;
			
			$usi_party_document_result = JRAPartyDocumentOperations::createJRAPartyDocument($party_id, $document);
			
			if(isset($usi_party_document_result->id ))
			{
				if(PREAPPRENTICE_DEBUG_MODE)
				{
					echo "Party Document Created (USI Transcript) - Party Document ID: " . $usi_party_document_result->id . "<br/>";
				}
				
				// Remove the file from server - if not debugging
				if(!PREAPPRENTICE_DEBUG_MODE)
				{
					unlink($usi_file);
				}
				
				if(PREAPPRENTICE_DEBUG_MODE)
				{
					echo "File removed from web server<br/><br/>";
				}
			}
			else
			{
				if(PREAPPRENTICE_DEBUG_MODE)
				{
					echo "Error occurred when creating Party Document (USI Transcript)<br/>";
					echo "<pre>";
					var_dump($usi_party_document_result);
					echo "</pre>";
				}
			}
		}


		// 2021.03.16 - Create the SKILL FIRST Party Document
		/* 2023.01.16 - Remove request by Ashima Nakra (email)
		$sf_pdf_file = JR_ROOT_PATH . '/pdf/' . $sfp;
		$document = new JRAPartyDocument ();
		$document->party_id = $party_id;
		$document->name = 'Skills First Program (' . $form->course_scope_code . ' - ' . $form->course_number . ')';
		$document->description = 'Skills First Program (' . $form->course_scope_code . ' - ' . $form->course_number . ')';
		$document->filename = $sf_pdf_file;
	  	 
		$sf_party_document_result = JRAPartyDocumentOperations::createJRAPartyDocument ( $party_id, $document );
	  	 
		if (isset ( $sf_party_document_result->id )) 
		{
			if (PREAPPRENTICE_DEBUG_MODE) 
			{
				echo "Party Document Created - Party Document ID: " . $sf_party_document_result->id . "<br/>";
			}

			// Remove PDF from server - if not debugging
			if (!PREAPPRENTICE_DEBUG_MODE)
			{
				unlink ( $sf_pdf_file );
			}

			if (PREAPPRENTICE_DEBUG_MODE) 
			{
				echo "PDF file removed from web server<br/><br/>";
			}
		}
		*/
	  	 
		// 2021.07.22 - Create the PRE TRAINING REVIEW Party Document
		/* 2023.01.16 - Remove request by Ashima Nakra (email)
		$ptr_pdf_file = JR_ROOT_PATH . '/pdf/' . $ptrp;
		$document = new JRAPartyDocument ();
		$document->party_id = $party_id;
		$document->name = 'Pre Training Review (' . $form->course_scope_code . ' - ' . $form->course_number . ')';
		$document->description = 'Pre Training Review (' . $form->course_scope_code . ' - ' . $form->course_number . ')';
		$document->filename = $ptr_pdf_file;
	  	 
		$ptr_party_document_result = JRAPartyDocumentOperations::createJRAPartyDocument ( $party_id, $document );
	  	 
		if (isset ( $ptr_party_document_result->id )) 
		{
			if (PREAPPRENTICE_DEBUG_MODE) 
			{
				echo "Party Document Created - Party Document ID: " . $ptr_party_document_result->id . "<br/>";
			}

			// Remove PDF from server - If not debugging
			if (!PREAPPRENTICE_DEBUG_MODE)
			{
				unlink ( $ptr_pdf_file );
			}
	  	 	
			if (PREAPPRENTICE_DEBUG_MODE) 
			{
				echo "PDF file removed from web server<br/><br/>";
			}
		}
		*/
		
		if(PREAPPRENTICE_DEBUG_MODE)
		{
			echo "Exit before Couse Sync and Cleanup";
			exit;
		}

		// Check course enrolment availability and sync from Job Ready if less than 3 remaining
		check_course_date_and_sync ( $form->course_number );


		// 05.04.2024 - Payment for PRE APPRENTICESHIP APPLICATION FORM removed as requested by Lyn Wang
		// Clean-up Gravity Form process required to be re-added
		
		// Define Gravity Form Linked Entry ID(from entry array)
		$gravity_form_linked_entry_id =(int) $form->gform_id;
		
		// Setup the WooCommerce keep array (array of keys to be kept on website database)
		$wc_keep_array = array (
				'_gravity_forms_history',
				'_Course Scope Code',
				'_Course Number',
				'Course Option',
				'First Name',
				'Family Name',
				'Party ID'
		);
		
		// Set up a Gravity Form keep array (array of form field id's to be kept on website database)
		$gf_keep_array = array (77, 78, 9, 8, 21, 
								119, 122, 113, 54, 68, 
								116, 66, 67, 57, 56, 
								58, 59);
		
		// Gravity Form Party ID field #
		$gf_party_id_field = 124;
		
		// Clean up Gravity Form variables
		if(PREAPPRENTICE_DEBUG_MODE)
		{
			echo "Confidential Clean Up currently disabled<br/><br/>";
			error_log("Confidential Clean Up currently disabled<br/><br/>");
		}
		else
		{
			// Clean up Gravity form fields - unless debugging
			confidential_clean_up_gf($gravity_form_linked_entry_id, $gf_keep_array, $party_id, $gf_party_id_field);
		}
		
		return $party_id;
	}
	else
	{
		if (PREAPPRENTICE_DEBUG_MODE) 
		{
			echo "<div>No Party ID available<br/><br/></div>";
		}
	}
}



/* *********** *
 * PDF-RELATED *
 * *********** */

function pre_apprentice_application_form_pdf($form)
{
	// Start
	$content = neca_pdf_content_start ();
	
	// Add Heading
	$content .= neca_pdf_content_heading ( 'Pre Apprentice Application Form', $form );
	
	// Previously Enrolled
	// 03.01.2024 - Added Previously Enrolled - Requested by Lyn Wang
	$content .= neca_pdf_previously_enrolled($form);
	
	// Personal Details Content
	$content .= neca_pdf_content_personal_details ( $form );
	
	// Emergency Contact Details Content
	$content .= neca_pdf_content_emergency_content_details ( $form );
	
	// Page End
	$content .= neca_pdf_page_end ();
	
	// Page Start
	$content .= neca_pdf_page_start ();
	
	// Avetmiss Heading
	$content .= neca_pdf_content_avetmiss_heading ( $form );
	
	// Labour Force Details Content
	$content .= neca_pdf_content_labour_details ( $form );
	
	// Referred from a Job Seeker
	$content .= neca_pdf_content_referred_details ( $form );
	
	// Nationality Details Contact
	$content .= neca_pdf_content_language_details ( $form, false );
	
	// School Details + Prior Education Details Content
	$content .= neca_pdf_content_education_details ( $form );
	
	// Credit Transfer Details
	// 2024.01.03 - Added by Lyn Wang
	$content .= neca_pdf_content_credit_transfer ( $form );
	
	// Recognition of Prior Learning
	// 2024.01.03 - Added by Lyn Wang
	$content .= neca_pdf_content_recognition_of_prior_learning( $form );
	
	// Disability Details Content
	$content .= neca_pdf_content_disability_details ( $form );
	
	// Concession Card Content
	// $content .= neca_pdf_content_concession_details ( $form );
	
	// Page End
	$content .= neca_pdf_page_end ();
	
	// Page Start
	$content .= neca_pdf_page_start ();
	
	// USI Details
	$content .= neca_pdf_content_usi ( $form );
	
	// VSN Details
	$content .= neca_pdf_content_vsn ( $form );
	
	// Enrolment Avetmiss Details Content
	$content .= neca_pdf_content_enrolment_avetmiss_details ( $form );

	// How did you hear Details Content
	$content .= neca_pdf_content_cohort($form);
		
	// How did you hear Details Content
	$content .= neca_pdf_content_how_did_you_hear ( $form );
	
	// Page End
	$content .= neca_pdf_page_end ();
	
	// Page Start
	$content .= neca_pdf_page_start ();
	
	// Privacy Policy FEB2023
	$content .= neca_pdf_content_privacy_policy_feb2023();
	
	// NECA Privacy Notice
	//$content .= neca_pdf_content_neca_privacy_notice_2019 ();
	
	// Student Enrolment Privacy Notice - Removed 12/09/2019
	// $content .= neca_pdf_content_student_enrolment_privacy_notice();
	
	// Page End
	$content .= neca_pdf_page_end ();
	
	// Page Start
	$content .= neca_pdf_page_start ();
	
	// Victorian Enrolment Privacy Notice - Updated on FEB 2023
	$content .= neca_pdf_content_victorian_enrolment_privacy_notice();
		
	// Victorian Enrolment Privacy Notice
	//$content .= neca_pdf_content_victorian_enrolment_privacy_notice ();
	
	// Page End
	$content .= neca_pdf_page_end ();
	
	// Page Start
	$content .= neca_pdf_page_start ();
	
	// All Students Must Read, Sign and Data
	$content .= neca_pdf_content_all_students_must_read_sign_date_2019 ( $form );
	
	// Tickboxes
	$content .= neca_pdf_content_tickboxes_2019 ();
	
	// Signatures
	$content .= neca_pdf_content_signatures_2019 ( $form );
	
	// Page End
	$content .= neca_pdf_page_end ();
	
	/* Remove last page as requested by Lyn Wang 16.06.2023
	// Page Start
	$content .= neca_pdf_page_start ();
	
	// Office Use Only
	$content .= neca_pdf_content_office_use_only_pre_apprentice ( $form );
	
	// Sign Off
	$content .= neca_pdf_content_sign_off ();
	
	// End
	$content .= neca_pdf_page_end ();
	*/
	
	// Setup Filename
	$filename = urlencode ( $form->course_number . '_' . $form->first_name . '_' . $form->surname . '_' . current_time ( 'Ymd_hms' ) ) . '.pdf';
	
	// Create PDF
	neca_create_pdf ( $filename, $content );
	
	return $filename;
}


function neca_pdf_content_office_use_only_pre_apprentice($form) 
{
	$jrd = JobReadyDateOperations::loadJobReadyDateByCourseNumber ( $form->course_number );
	$course_name = $jrd->course_number;

	$start_date = $jrd->start_date_clean;
	$end_date = $jrd->end_date_clean;
	 
	$content = '<div class="heading3">OFFICE USE ONLY</div>';
	 
	$content .= '<table class="tbl">
					<tr>
						<td colspan="2"><strong>PROGRAM DETAILS (PRE-APPRENTICESHIP)</strong></td>
					</tr>
					<tr>
						<tr>
							<td style="width: 5%;">[&nbsp;&nbsp;&nbsp;]</td>
							<td style="width: 95%;">UEE22011 - Certificate II in Electrotechnology (Career Start  )</td>
						</tr>
					</tr>
				</table>';
	 
	return $content;
}