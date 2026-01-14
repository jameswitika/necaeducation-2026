<?php
/* ************** *
 * PRE-PROCESSING *
 * ************** */

// Project Management Form (PM-APP)
add_filter("gform_pre_render_" . PROJECT_MANAGEMENT_DIPLOMA_APPLICATION_FORM, 'project_management_diploma_form_prepopulate');
add_filter("gform_pre_validation_" . PROJECT_MANAGEMENT_DIPLOMA_APPLICATION_FORM, 'project_management_diploma_form_prepopulate');
add_filter("gform_pre_submission_filter_" . PROJECT_MANAGEMENT_DIPLOMA_APPLICATION_FORM, 'project_management_diploma_form_prepopulate');
add_filter("gform_admin_pre_render" . PROJECT_MANAGEMENT_DIPLOMA_APPLICATION_FORM, 'project_management_diploma_form_prepopulate');

function project_management_diploma_form_prepopulate($form)
{
	$prefill_fields= array();
	
	if(PROJECT_MGMNT_DIPLOMA_DEBUG_MODE)
	{
		if(isset($_SESSION['prefill']))
		{
			echo "Prefill: <br/>";
			var_dump($_SESSION['prefill']);
			echo "<br/><br/>";
		}
	}
	
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
		
	// Pre-populates the Nonce Hidden Field
	$nonce = wp_create_nonce( 'neca_employer_lookup' );
	$prefill_fields['135'] = $nonce;
	
	if(isset($_SESSION['prefill']))
	{
		$prefill_fields['2'] = $_SESSION['prefill']->title;
		$prefill_fields['9'] = $_SESSION['prefill']->first_name;
		$prefill_fields['83'] = $_SESSION['prefill']->middle_name;
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
		
		$prefill_fields['97'] = $_SESSION['prefill']->street_address1;
		$prefill_fields['98'] = $_SESSION['prefill']->suburb;
		$prefill_fields['99'] = $_SESSION['prefill']->state;
		$prefill_fields['100'] = $_SESSION['prefill']->postcode;
		// 		$prefill_fields['87'] = $_SESSION['prefill']->postal_address_same;
		$prefill_fields['101'] = isset($_SESSION['prefill']->postal_street_address1) ? $_SESSION['prefill']->postal_street_address1 : '';
		$prefill_fields['102'] = isset($_SESSION['prefill']->postal_suburb) ? $_SESSION['prefill']->postal_suburb : '';
		$prefill_fields['103'] = isset($_SESSION['prefill']->postal_state) ? $_SESSION['prefill']->postal_state : '';
		$prefill_fields['104'] = isset($_SESSION['prefill']->postal_postcode) ? $_SESSION['prefill']->postal_postcode : '';
		
		$prefill_fields['71'] = $_SESSION['prefill']->emergency_contact_firstname;
		$prefill_fields['92'] = $_SESSION['prefill']->emergency_contact_surname;
		$prefill_fields['74'] = $_SESSION['prefill']->emergency_contact_relationship;
		$prefill_fields['73'] = $_SESSION['prefill']->emergency_contact_number;
		
		/*
		$prefill_fields['133'] = array(	'133' => isset($_SESSION['prefill']->emergency_contact_email) ? $_SESSION['prefill']->emergency_contact_email : '',
				'133.2' => isset($_SESSION['prefill']->emergency_contact_email) ? $_SESSION['prefill']->emergency_contact_email : '');
		*/
		
		$prefill_fields['28'] = $_SESSION['prefill']->labour_force_status;
		
		// 2021.03.16 - Added - Requested by Ranjita
		$prefill_fields['172'] = $_SESSION['prefill']->referred;
		$prefill_fields['173'] = $_SESSION['prefill']->referred_details;
		
		$prefill_fields['93'] = isset($_SESSION['prefill']->country_of_birth) ? $_SESSION['prefill']->country_of_birth : '';

		// 2022.01.27 - Add Citizenship
		$prefill_fields['187'] = isset($_SESSION['prefill']->australian_citizen) ? $_SESSION['prefill']->australian_citizen : '';
		$prefill_fields['188'] = isset($_SESSION['prefill']->citizenship_other) ? $_SESSION['prefill']->citizenship_other : '';
		
		$prefill_fields['34'] = $_SESSION['prefill']->indigenous_status;
		$prefill_fields['38'] = ($_SESSION['prefill']->main_language == 'English') ? "English" : "Yes";
		$prefill_fields['95'] = $_SESSION['prefill']->main_language;
		
		$prefill_fields['45'] = $_SESSION['prefill']->at_school_flag;
		// $prefill_fields['46'] = $_SESSION['prefill']->school;
		$prefill_fields['47'] = $_SESSION['prefill']->highest_school_level;
		// $prefill_fields['105'] = $_SESSION['prefill']->year_highest_school_level;
		$prefill_fields['42'] = $_SESSION['prefill']->disability_flag;
		$prefill_fields['124'] = isset($_SESSION['prefill']->disability_other) ? $_SESSION['prefill']->disability_other : '';
		$prefill_fields['50'] = $_SESSION['prefill']->prior_education_flag;
		$prefill_fields['52'] = isset($_SESSION['prefill']->prior_education_qualification) ? $_SESSION['prefill']->prior_education_qualification : '';
		
		// 2021.03.25 - Added as requested by Ranjita / Kosala
		$prefill_fields['184'] = isset($_SESSION['prefill']->credit_transfer) ? $_SESSION['prefill']->credit_transfer : '';
		$prefill_fields['186'] = isset($_SESSION['prefill']->rpl) ? $_SESSION['prefill']->rpl : '';
		
		if(isset($_SESSION['prefill']->usi_number) && $_SESSION['prefill']->usi_number != '')
		{
			$prefill_fields['121'] = 'Yes';
			$prefill_fields['54'] = isset($_SESSION['prefill']->usi_number) ? $_SESSION['prefill']->usi_number : '';
		}
		$prefill_fields['122'] = isset($_SESSION['prefill']->city_of_birth) ? $_SESSION['prefill']->city_of_birth : '';
		
		$prefill_fields['107'] = isset($_SESSION['prefill']->employer_company) ? $_SESSION['prefill']->employer_company : '';
		$prefill_fields['108'] = isset($_SESSION['prefill']->employer_address) ? $_SESSION['prefill']->employer_address : '';
		$prefill_fields['109'] = isset($_SESSION['prefill']->employer_suburb) ? $_SESSION['prefill']->employer_suburb : '';
		$prefill_fields['110'] = isset($_SESSION['prefill']->employer_state) ? $_SESSION['prefill']->employer_state : '';
		$prefill_fields['111'] = isset($_SESSION['prefill']->employer_postcode) ? $_SESSION['prefill']->employer_postcode : '';
		$prefill_fields['112'] = isset($_SESSION['prefill']->employer_office_phone) ? $_SESSION['prefill']->employer_office_phone : '';
		$prefill_fields['118'] = isset($_SESSION['prefill']->employer_paying_invoice) ? $_SESSION['prefill']->employer_paying_invoice : '';
		
		$prefill_fields['68'] = isset($_SESSION['prefill']->study_reason) ? $_SESSION['prefill']->study_reason : '';
		$prefill_fields['96'] = isset($_SESSION['prefill']->industry_employment) ? $_SESSION['prefill']->industry_employment : '';
		$prefill_fields['66'] = isset($_SESSION['prefill']->occupation) ? $_SESSION['prefill']->occupation : '';

		// 14.01.2022 - Removed as request by Ashima
		//$prefill_fields['119'] = isset($_SESSION['prefill']->concession_flag) ? $_SESSION['prefill']->concession_flag : '';
		
		$prefill_fields['67'] = isset($_SESSION['prefill']->how_did_you_hear) ? $_SESSION['prefill']->how_did_you_hear : '';
		
		$prefill_fields['54'] = isset($_SESSION['prefill']->usi_number) ? $_SESSION['prefill']->usi_number : '';
		
		$prefill_fields['57'] = isset($_SESSION['prefill']->previous_victorian_education) ? $_SESSION['prefill']->previous_victorian_education : '';
		$prefill_fields['56'] = isset($_SESSION['prefill']->vsn) ? $_SESSION['prefill']->vsn : '';
		$prefill_fields['58'] = isset($_SESSION['prefill']->previous_victorian_school) ? $_SESSION['prefill']->previous_victorian_school : '';
		$prefill_fields['59'] = isset($_SESSION['prefill']->previous_victorian_training) ? $_SESSION['prefill']->previous_victorian_training : '';
		
		// 2018.10.17 - Required for Skills First Program PDF
		/* 2023.01.16 - Remove request by Ashima Nakra (email)
		$prefill_fields['146'] = isset($_SESSION['prefill']->highest_qualification_completed) ? $_SESSION['prefill']->highest_qualification_completed : '';
		$prefill_fields['148'] = isset($_SESSION['prefill']->government_funded_enrolments_this_year) ? $_SESSION['prefill']->government_funded_enrolments_this_year : '';
		$prefill_fields['149'] = isset($_SESSION['prefill']->government_funded_undertakings_at_present) ? $_SESSION['prefill']->government_funded_undertakings_at_present : '';
		$prefill_fields['150'] = isset($_SESSION['prefill']->government_funded_in_lifetime) ? $_SESSION['prefill']->government_funded_in_lifetime : '';
		$prefill_fields['152'] = isset($_SESSION['prefill']->enrolled_in_a_school) ? $_SESSION['prefill']->enrolled_in_a_school : '';
		$prefill_fields['153'] = isset($_SESSION['prefill']->enrolled_in_skills_for_education) ? $_SESSION['prefill']->enrolled_in_skills_for_education : '';
		$prefill_fields['154'] = isset($_SESSION['prefill']->subsidized_acknowledgement) ? $_SESSION['prefill']->subsidized_acknowledgement : '';
		$prefill_fields['155'] = isset($_SESSION['prefill']->contacted_by_department_acknowledgement) ? $_SESSION['prefill']->contacted_by_department_acknowledgement : '';
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
				$prefill_fields['83'] = $form_fields->middle_name;
				$prefill_fields['8'] = $form_fields->surname;
				$prefill_fields['10'] = $form_fields->known_by;
				$prefill_fields['11'] = $form_fields->birth_date;
				$prefill_fields['69'] = $form_fields->gender;
				$prefill_fields['20'] = $form_fields->home_phone;
				$prefill_fields['19'] = $form_fields->mobile_phone;
				
				/*
				$prefill_fields['21'] = array(	'21' => $form_fields->email,
						'21.2' => $form_fields->email );
				*/
				
				$prefill_fields['97'] = $form_fields->street_address1;
				$prefill_fields['98'] = $form_fields->suburb;
				$prefill_fields['99'] = $form_fields->state;
				$prefill_fields['100'] = $form_fields->postcode;
				
				//$prefill_fields['87'] = $form_fields->postal_address_same;
				$prefill_fields['101'] = $form_fields->postal_street_address1;
				$prefill_fields['102'] = $form_fields->postal_suburb;
				$prefill_fields['103'] = $form_fields->postal_state;
				$prefill_fields['104'] = $form_fields->postal_postcode;
				
				$prefill_fields['28'] = $form_fields->labour_force_status;
				$prefill_fields['93'] = $form_fields->country_of_birth;
				
				// 2022.01.27 - Add Citizenship again
				$prefill_fields['187'] = $form_fields->australian_citizen;
				$prefill_fields['188'] = $form_fields->citizenship_other;
				
				// Language
				$prefill_fields['38'] = ($form_fields->main_language == 'English') ? "English" : "Yes";
				$prefill_fields['95'] = $form_fields->main_language;
				
				$prefill_fields['45'] = $form_fields->at_school_flag;
				$prefill_fields['47'] = $form_fields->highest_school_level;
				$prefill_fields['105'] = $form_fields->year_highest_school_level;
				$prefill_fields['42'] = $form_fields->disability_flag;
				$prefill_fields['50'] = $form_fields->prior_education_flag;
				
				$prefill_fields['54'] = $form_fields->usi_number;
				
				$party_id = $employee_party_xml_object->{'party'}->{'party-identifier'};
				
				// Retrieve "Party Contact" and pre-populate Emergency Contact Details using "primary" record
				$jra_party_contacts = JRAPartyContactOperations::getJRAPartyContacts($party_id);
				
				$primary_party_contact_found = false;
				
				// Loops through all party contacts linked to the party_id
				foreach($jra_party_contacts as $jra_party_contact)
				{
					if(	$jra_party_contact->primary == 'true')
					{
						$prefill_fields['71'] = $jra_party_contact->first_name;
						$prefill_fields['92'] = $jra_party_contact->surname;
						$prefill_fields['74'] = $jra_party_contact->relationship;
						$prefill_fields['73'] = $jra_party_contact->phone;
						
						/*
						$prefill_fields['133'] = array(	'133' => $jra_party_contact->email,
								'133.2' => $jra_party_contact->email);
						*/
						
						$primary_party_contact_found= true;
						break;
					}
				}
				
				// If there was no primary contact, use the first party contact
				if(!$primary_party_contact_found)
				{
					$prefill_fields['71'] = $jra_party_contacts[0]->first_name;
					$prefill_fields['92'] = $jra_party_contacts[0]->surname;
					$prefill_fields['74'] = $jra_party_contacts[0]->relationship;
					$prefill_fields['73'] = $jra_party_contacts[0]->phone;
					
					/*
					$prefill_fields['133'] = array(	'133' => $jra_party_contacts[0]->email,
							'133.2' => $jra_party_contacts[0]->email);
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
				
				$prefill_fields['107'] = $form_fields->employer_company;
				$prefill_fields['108'] = $form_fields->employer_address;
				$prefill_fields['109'] = $form_fields->employer_suburb;
				$prefill_fields['110'] = $form_fields->employer_state;
				$prefill_fields['111'] = $form_fields->employer_postcode;
				$prefill_fields['112'] = $form_fields->employer_office_phone;
				$prefill_fields['118'] = $form_fields->employer_paying_invoice;
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
		
		// State (Address) + State (Postal Address) + States
		if($field->id == 99 || $field->id == 103 || $field->id == 110)
		{
			$field->choices = jrar_state();
		}
		
		// Employment Category
		if($field->id == 28)
		{
			$field->choices = jrar_employment_category();
		}
		
		// Country
		if($field->id == 93)
		{
			$field->choices = jrar_country();
		}
		
		// Indigenous Status
		if($field->id == 34)
		{
			$field->choices = jrar_indigenous_status();
		}
		
		// Language
		if($field->id == 95)
		{
			$field->choices = jrar_language();
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
		
		// Highest School Level
		if($field->id == 47)
		{
			$field->choices = jrar_highest_school_level();
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
		
		// Study Reason
		if($field->id == 68)
		{
			$field->choices = jrar_study_reason();
		}
		
		// Client Industry
		if($field->id == 96)
		{
			$field->choices = jrar_client_industry_employer();
		}
		
		// Client Occupation
		if($field->id == 66)
		{
			$field->choices = jrar_client_occupation_identifer();
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
// Project Management Form (PM-APP)
add_action( "gform_pre_submission_" . PROJECT_MANAGEMENT_DIPLOMA_APPLICATION_FORM, 'project_management_diploma_form_presubmission');

function project_management_diploma_form_presubmission()
{
	if(PROJECT_MGMNT_DIPLOMA_DEBUG_MODE)
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
	
	$prefill->title = $_POST['input_2'];
	$prefill->first_name = $_POST['input_9'];
	$prefill->middle_name = $_POST['input_83'];
	$prefill->surname = $_POST['input_8'];
	$prefill->known_by = $_POST['input_10'];
	$prefill->birth_date = $_POST['input_11'];
	$prefill->gender = $_POST['input_69'];
	$prefill->home_phone = $_POST['input_20'];
	$prefill->mobile_phone = $_POST['input_19'];
	$prefill->email = $_POST['input_21'];
	$prefill->street_address1 = $_POST['input_97'];
	$prefill->suburb = $_POST['input_98'];
	$prefill->state = $_POST['input_99'];
	$prefill->postcode = $_POST['input_100'];
	$prefill->postal_address_same = isset($_POST['input_87_1']) ? "Yes" : "";
	if($prefill->postal_address_same != "Yes")
	{
		$prefill->postal_street_address1 = $_POST['input_101'];
		$prefill->postal_suburb = $_POST['input_102'];
		$prefill->postal_state = $_POST['input_103'];
		$prefill->postal_postcode = $_POST['input_104'];
	}
	$prefill->emergency_contact_firstname = $_POST['input_71'];
	$prefill->emergency_contact_surname = $_POST['input_92'];
	$prefill->emergency_contact_number = $_POST['input_73'];
	$prefill->emergency_contact_email = $_POST['input_133'];
	$prefill->emergency_contact_relationship = $_POST['input_74'];
	
	$prefill->labour_force_status = $_POST['input_28'];
	
	// 2021.03.16 - Added Referred from Job Seeker
	$prefill->referred = $_POST['input_172'];
	$prefill->referred_details = $_POST['input_173'];
	
	$prefill->country_of_birth = $_POST['input_93'];

	// 2022.01.27 - Citizenship re-added
	$prefill->australian_citizen = $_POST['input_187'];
	$prefill->citizenship_other = $_POST['input_188'];

	
	$language_other_than_english = $_POST['input_38'];
	if($language_other_than_english != 'English')
	{
		$prefill->main_language = $_POST['input_95'];
	}
	else
	{
		$prefill->main_language = 'English';
	}
	$prefill->at_school_flag = $_POST['input_45'];
	
	// 2021.03.16 - Removed
	// $prefill->school = isset($_POST['input_46']) ? $_POST['input_46'] : '';
	
	$prefill->highest_school_level = isset( $_POST['input_47']) ? $_POST['input_47'] : '';
	
	// 2021.03.16 - Removed
	// $prefill->year_highest_school_level = isset($_POST['input_105']) ? $_POST['input_105'] : '';
	
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
		$prefill->disability_other = $_POST['input_124'];
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
	
	$prefill->employer_company = isset($_POST['input_107']) ? $_POST['input_107'] : '';
	$prefill->employer_address = isset($_POST['input_108']) ? $_POST['input_108'] : '';
	$prefill->employer_suburb = isset($_POST['input_109']) ? $_POST['input_109'] : '';
	$prefill->employer_state = isset($_POST['input_110']) ? $_POST['input_110'] : '';
	$prefill->employer_postcode = isset($_POST['input_111']) ? $_POST['input_111'] : '';
	$prefill->employer_office_phone = isset($_POST['input_112']) ? $_POST['input_112'] : '';
	$prefill->employer_paying_invoice = $_POST['input_118'];
	
	// 2021.03.25 - Added as requested by Ranjita / Kosala
	$prefill->credit_transfer = $_POST['input_184'];
	$prefill->rpl = $_POST['input_186'];
	
	$prefill->usi_number = isset($_POST['input_54']) ? $_POST['input_54'] : '';
	
	$prefill->study_reason = $_POST['input_68'];
	$prefill->industry_employment = $_POST['input_96'];
	$prefill->occupation = $_POST['input_66'];
	
	//$prefill->concession_flag = $_POST['input_119'];
	
	$prefill->how_did_you_hear = $_POST['input_67'];
	
	$prefill->previous_victorian_education = $_POST['input_57'];
	$prefill->vsn = isset($_POST['input_56']) ? $_POST['input_56'] : '';
	$prefill->previous_victorian_school = isset($_POST['input_58']) ? $_POST['input_58'] : '';
	$prefill->previous_victorian_training = isset($_POST['input_59']) ? $_POST['input_59'] : '';
	
	// 2020.03.04 - Required for Skills First Program PDF
	/* 2023.01.16 - Remove request by Ashima Nakra (email)
	if(isset($_POST['input_146']))
	{
		$prefill->highest_qualification_completed = ucwords(strtolower($_POST['input_146']));
	}
	
	if(isset($_POST['input_148']))
	{
		$prefill->government_funded_enrolments_this_year = $_POST['input_148'];
	}
	
	if(isset($_POST['input_149']))
	{
		$prefill->government_funded_undertakings_at_present = $_POST['input_149'];
	}
	
	if(isset($_POST['input_150']))
	{
		$prefill->government_funded_in_lifetime = $_POST['input_150'];
	}
	
	if(isset($_POST['input_152']))
	{
		$prefill->enrolled_in_a_school = $_POST['input_152'];
	}
	
	if(isset($_POST['input_153']))
	{
		$prefill->enrolled_in_skills_for_education = $_POST['input_153'];
	}
	
	if(isset($_POST['input_154']))
	{
		$prefill->subsidized_acknowledgement = $_POST['input_154'];
	}
	
	if(isset($_POST['input_155']))
	{
		$prefill->contacted_by_department_acknowledgement = $_POST['input_155'];
	}
	*/
	
	
	$_SESSION['prefill'] = $prefill;
	
	if(PROJECT_MGMNT_DIPLOMA_DEBUG_MODE)
	{
		echo "Prefill set: <br/>";
		var_dump($_SESSION['prefill']);
		echo "<br/><br/>";
	}
}



/* *************** *
 * POST-PROCESSING *
 * *************** */

/**
 * PROJECT MANAGEMENT FORM SUBMISSION PROCESS
 *
 * @param array $entry
 * @param object $form_data
 * @return boolean
 */
function project_management_diploma_form_submission_process($entry, $form_data) 
{
	if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
	{
		echo "<h3>Project Management Submission Process</h3>";
		error_log ( "--- Project Management Submission Process ---" );
	}
	
	$form = new JobReadyForm ();
	
	// Gravity Form
	$form->gform_id = $entry ['id'];
	$form->gform_form_id = $entry ['form_id'];
	
	// Course Details
	$form->course_scope_code = rgar ( $entry, '81' );
	$form->course_number = rgar ( $entry, '82' );
	
	// Personal Details
	$form->gender = rgar ( $entry, '69' );
	$form->title = rgar ( $entry, '2' );
	$form->first_name = ucwords ( strtolower ( rgar ( $entry, '9' ) ) );
	$form->middle_name = ucwords ( strtolower ( rgar ( $entry, '83' ) ) );
	$form->surname = ucwords ( strtolower ( rgar ( $entry, '8' ) ) );
	$form->known_by = ucwords ( strtolower ( rgar ( $entry, '10' ) ) );
	$form->birth_date = rgar ( $entry, '11' );
	
	// Contact Details
	$form->home_phone = rgar ( $entry, '20' );
	$form->mobile_phone = rgar ( $entry, '19' );
	$form->email = strtolower ( rgar ( $entry, '21' ) );
	
	// Address
	$form->street_address1 = ucwords ( strtolower ( rgar ( $entry, '97' ) ) );
	$form->suburb = ucwords ( strtolower ( rgar ( $entry, '98' ) ) );
	$form->state = ucwords ( strtolower ( rgar ( $entry, '99' ) ) );
	$form->postcode = rgar ( $entry, '100' );
	
	$form->postal_address_same = rgar ( $entry, '87.1' ); // 87.1 because its a checkbox
	
	if ($form->postal_address_same != 'Yes') 
	{
		$form->postal_street_address1 = ucwords ( strtolower ( rgar ( $entry, '101' ) ) );
		$form->postal_suburb = ucwords ( strtolower ( rgar ( $entry, '102' ) ) );
		$form->postal_state = ucwords ( strtolower ( rgar ( $entry, '103' ) ) );
		$form->postal_postcode = rgar ( $entry, '104' );
	}
	
	// Emergency Contact
	$form->emergency_contact_firstname = ucwords ( strtolower ( rgar ( $entry, '71' ) ) );
	$form->emergency_contact_surname = ucwords ( strtolower ( rgar ( $entry, '92' ) ) );
	$form->emergency_contact_number = rgar ( $entry, '73' );
	$form->emergency_contact_email = rgar ( $entry, '133' );
	$form->emergency_contact_relationship = ucwords ( strtolower ( rgar ( $entry, '74' ) ) );
	
	// Labour Force
	$form->labour_force_status = rgar ( $entry, '28' );
	
	// 2021.03.16 - Added as requested by Ranjita
	// Referred from a Job Seeker
	$form->referred = rgar( $entry, '172' );
	if ($form->referred == 'Yes')
	{
		$form->referred_details = rgar( $entry, '173' );
	}
	
	// Country of Birth
	$form->country_of_birth = $entry ['93'];
	
	// Birth + Nationality + Indigenous + Language
	$form->australian_citizen = $entry ['187'];
	if ($form->australian_citizen == 'Yes') 
	{
		$form->citizenship_status = 'Australian Citizen or Permanent Resident';
	}
	else 
	{
		$form->citizenship_other = $entry ['188'];
	}
	
	$form->indigenous_status = rgar ( $entry, '34' );
	$language_other_than_english = rgar ( $entry, '38' );
	if ($language_other_than_english != 'Yes') 
	{
		$form->main_language = "English";
		// $form->spoken_english_proficiency= "Very Well";
	}
	else 
	{
		$form->main_language = rgar ( $entry, '95' );
		// $form->spoken_english_proficiency= "Very Well";
	}
	
	// Are you still attending school?
	$form->at_school_flag = rgar ( $entry, '45' );
	
	/* 2021.03.16 - Disabled
	 if ($form->at_school_flag == 'No') {
	 $form->school = ucwords ( strtolower ( rgar ( $entry, '46' ) ) );
	 }
	 */
	
	// School Details
	$form->highest_school_level = rgar ( $entry, '47' );
	
	// 2021.03.16 - Disabled
	//$form->year_highest_school_level = rgar ( $entry, '105' );
	
	// Disability
	$form->disability_types = array ();
	$form->disability_flag = rgar ( $entry, '42' );
	if ($form->disability_flag == 'Yes') 
	{
		// Array fields are passed in as 43.1, 43.2, 43.3.... so we have iterate through them
		for($i = 1; $i < 11; $i ++) 
		{
			$ref = '43.' . $i;
			if (isset ( $entry [$ref] ) && $entry [$ref] != '') 
			{
				$form->disability_types [] = $entry [$ref];
			}
		}
	}
	
	// Prior Education
	$form->prior_educations = array ();
	$form->prior_education_flag = rgar ( $entry, 50 );
	if ($form->prior_education_flag == 'Yes') 
	{
		// Array fields are passed in as 51.1, 51.2, 51.3.... so we have iterate through them
		for($i = 1; $i <= 20; $i ++) 
		{
			$ref = '51.' . $i;
			if ($entry [$ref] != '') 
			{
				$form->prior_educations [] = $entry [$ref];
			}
		}
		$form->prior_education_qualification = $entry ['52'];
	}
	
	// Employer Details
	$form->employer_party_id = rgar ( $entry, 126 );
	$form->employer_search_or_create = rgar ( $entry, 136 );
	$form->employer_party_new = ($form->employer_search_or_create == 'Create a new employer in our system') ? true : false;
	
	if ($form->employer_party_new) 
	{
		$form->employer_company = ucwords ( strtolower ( rgar ( $entry, 107 ) ) );
		$form->employer_address = ucwords ( strtolower ( rgar ( $entry, 108 ) ) );
		$form->employer_suburb = ucwords ( strtolower ( rgar ( $entry, 109 ) ) );
		$form->employer_state = ucwords ( strtolower ( rgar ( $entry, 110 ) ) );
		$form->employer_postcode = rgar ( $entry, 111 );
		$form->employer_office_phone = rgar ( $entry, 112 );
	}
	$form->employer_paying_invoice = rgar ( $entry, 118 );
	
	// Unique Student Number
	$form->usi_flag = "Yes";
	$form->usi_number = rgar ( $entry, 54 );
	
	// Enrolment > Skills VIC AVETMISS
	$form->study_reason = rgar ( $entry, '68' );
	$form->industry_employment = rgar ( $entry, '96' );
	$form->occupation = rgar ( $entry, '66' );
	
	// Concession Card
	//$form->concession_flag = rgar ( $entry, '119' );
	
	// How did you hear
	$form->how_did_you_hear = ucwords ( strtolower ( rgar ( $entry, '67' ) ) );
	
	// Victorian Student
	$form->previous_victorian_education = rgar ( $entry, '57' );
	if (strpos ( $form->previous_victorian_education, 'Yes' ) !== false) 
	{
		$form->vsn = rgar ( $entry, '56' );
	}
	
	if (strpos ( $form->previous_victorian_education, 'Yes' ) !== false && isset ( $entry ['58'] ) && $entry ['58'] != '') 
	{
		$form->previous_victorian_school = ucwords ( strtolower ( rgar ( $entry, '58' ) ) );
	}
	if (strpos ( $form->previous_victorian_education, 'Yes' ) !== false && isset ( $entry ['59'] ) && $entry ['59'] != '') 
	{
		$form->previous_victorian_school = ucwords ( strtolower ( rgar ( $entry, '59' ) ) );
	}
	
	// 2020.03.04 - Required for Skills First Program PDF
	/* 2023.01.16 - Remove request by Ashima Nakra (email)
	$form->highest_qualification_completed = ucwords ( strtolower ( rgar ( $entry, '146' ) ) );
	$form->government_funded_enrolments_this_year = rgar ( $entry, '148' );
	$form->government_funded_undertakings_at_present = rgar ( $entry, '149' );
	$form->government_funded_in_lifetime = rgar ( $entry, '150' );
	$form->enrolled_in_a_school = rgar ( $entry, '152' );
	$form->enrolled_in_skills_for_education = rgar ( $entry, '153' );
	$form->subsidized_acknowledgement = rgar ( $entry, '154' );
	$form->contacted_by_department_acknowledgement = rgar ( $entry, '155' );
	*/
	
	// 25.03.2021 - Credit Transfer + RPL
	$form->credit_transfer = rgar ( $entry, '184' );
	$form->rpl = rgar ( $entry, '186' );
	
	// 2018.09.05 - Signature
	$form->signature = ucwords ( strtolower ( rgar ( $entry, '170' ) ) );
	
	// Student Declaraction
	$form->privacy_declaration = $entry ['60.1'];
	
	if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
	{
		echo "Form Variable: <br/>";
		var_dump ( $form );
		echo "<br/><br/>";
	}
	
	/*
	 * Setup Job Ready Resources and Create Accordingly
	 */
	
	$new_employer = false;
	
	if ($form->employer_party_new) 
	{
		// Setup "Employer Party" Resource
		$employer_party = new JRAParty ();
		$employer_party->party_type = 'Employer';
		$employer_party->contact_method = 'Email';
		$employer_party->legal_name = $form->employer_company;
		$employer_party->trading_name = $form->employer_company;
		
		// Setup "Party > Address" Child Resources
		$employer_party_addresses = array ();
		
		$employer_party_address = new JRAPartyAddress ();
		$employer_party_address->primary = 'true';
		$employer_party_address->street_address1 = $form->employer_address;
		$employer_party_address->suburb = $form->employer_suburb;
		$employer_party_address->state = $form->employer_state;
		$employer_party_address->country = 'Australia';
		$employer_party_address->post_code = $form->employer_postcode;
		$employer_party_address->location = "Work";
		
		// Add to employer_party_addresses array
		array_push ( $employer_party_addresses, $employer_party_address );
		
		$employer_party->address_child = $employer_party_addresses;
		
		// Setup "Party > Contact Detail" Child Resources
		$employer_contact_details = array ();
		
		if (trim ( $form->employer_office_phone != '' )) 
		{
			$employer_contact_detail = new JRAPartyContactDetail ();
			$employer_contact_detail->primary = 'true';
			$employer_contact_detail->contact_type = 'Phone';
			$employer_contact_detail->value = $form->employer_office_phone;
			array_push ( $employer_contact_details, $employer_contact_detail );
		}
		
		$employer_party->contact_detail_child = $employer_contact_details;
		
		if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
		{
			echo "Check if Employer Party exists<br/>";
		}
		
		// Check if the Employer Party Exists
		$employer_party_result = JRAPartyOperations::getJRAParty ( $employer_party );
		$employer_party_attributes = $employer_party_result->attributes ();
		$count = ( int ) $employer_party_attributes ['total'];
		
		if ($count > 0) 
		{
			// Set the Employer Party ID
			$employer_party_id = ( string ) $employer_party_result->party->{'party-identifier'};
			$new_employer = false;
			
			if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
			{
				echo "Employer Party exists - Employer ID: " . $employer_party_id . "<br/><br/>";
				error_log ( "Employer Party exists - Employer ID: " . $employer_party_id . "<br/><br/>" );
			}
		} 
		else 
		{
			if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
			{
				echo "Employer does not exist - Create Employer Party<br/>";
				error_log ( "Employer does not exist - Create Employer Party<br/>" );
			}
			
			// Create Employer Party
			$employer_party_xml = JRAPartyOperations::createJRAPartyXML ( $employer_party );
			$employer_party_result = JRAPartyOperations::createJRAParty ( $employer_party_xml );
			
			if (isset ( $employer_party_result->{'party-identifier'} )) 
			{
				$employer_party_id = ( string ) $employer_party_result->{'party-identifier'};
				$new_employer = true;
				
				if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
				{
					echo "Employer created - Employer ID: " . $employer_party_id . "<br/><br/>";
					error_log ( "Employer created - Employer ID: " . $employer_party_id . "<br/><br/>" );
				}
			} 
			else 
			{
				if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
				{
					echo "An error occured while creating an Employer Party<br/><br/>";
					error_log ( "An error occured while creating an Employer Party<br/><br/>" );
				}
				return false;
			}
		}
		// Sets the Employer Party ID
		$form->employer_party_id = $employer_party_id;
		$form->employer_party_new = $new_employer;
	} 
	else 
	{
		$employer_party_id = $form->employer_party_id;
	}
	
	// Check if we are using an existing Employer and if so, load their Employer information into the
	// FORM variables so they are included in the PDF
	
	if (! $form->employer_party_new) 
	{
		// Load the party by login
		$employer_party_xml_object = JRAEmployerOperations::getJRAEmployer ( $form->employer_party_id );
		
		// Load the Employer Party from Job Ready
		$employer_party_result = JRAEmployerOperations::getJRAEmployer ( $employer_party_id );
		
		// Confirms a valid response from Job Ready
		if ($employer_party_result !== false) 
		{
			// Convert the XML to an Object
			$employer_object = xmlToObject ( $employer_party_result );
			
			// var_dump($employer);
			// echo "<br/><br/>";
			
			// Set the Employer Address
			$employer_address = $employer_object->addresses->address;
			$employer_phone = '';
			
			// Loops through all contact detail
			if( isset($employer_object->{'contact-details'}->{'contact-detail'}) && is_array($employer_object->{'contact-details'}->{'contact-detail'}) )
			{
				foreach ( $employer_object->{'contact-details'}->{'contact-detail'} as $contact_detail ) 
				{
					if ($contact_detail->{'contact-type'} == 'Phone') 
					{
						$employer_phone = ( string ) $contact_detail->value;
						break;
					}
				}
			}
			
			$form->employer_company = isset ( $employer_object->{'trading-name'} ) ? ( string ) $employer_object->{'trading-name'} : '';
			$form->employer_address = isset ( $employer_address->{'street-address1'} ) ? ( string ) $employer_address->{'street-address1'} : '';
			$form->employer_suburb = isset ( $employer_address->suburb ) ? ( string ) $employer_address->suburb : '';
			$form->employer_state = isset ( $employer_address->state ) ? ( string ) $employer_address->state : '';
			$form->employer_postcode = isset ( $employer_address->{'post-code'} ) ? ( string ) $employer_address->{'post-code'} : '';
			$form->employer_office_phone = ( string ) $employer_phone;
			
			unset ( $employer_party_result );
			unset ( $employer_object );
			unset ( $employer_address );
			unset ( $employer_phone );
		}
	}
	
	if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
	{
		echo "Form Variable (before PDF): <br/>";
		var_dump ( $form );
		echo "<br/><br/>";
	}
	
	// Create PDF (after the Employer has been determine as NEW or EXISTING
	$pmfp = project_management_diploma_application_form_pdf ( $form );
	
	if(PROJECT_MGMNT_DIPLOMA_DEBUG_MODE)
	{
		echo '<a href="' . JR_ROOT_URL . '/pdf/' . $pmfp . '" target="_blank">Project Management Applcation Form (PDF)</a><br/><br/>';
	}
	
	// Create Skills First PDF
	/* 2023.01.16 - Remove request by Ashima Nakra (email)
	$course_name = 'Project Management Application Form';
	$sfp = skills_first_pdf ( $form, $course_name );
	
	if(PROJECT_MGMNT_DIPLOMA_DEBUG_MODE)
	{
		echo '<a href="' . JR_ROOT_URL . '/pdf/' . $sfp . '" target="_blank">Skills First (PDF)</a><br/><br/>';
	}
	*/
	
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
	$party_address->street_address1 = $form->street_address1;
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
		$postal_address->street_address1 = $form->postal_street_address1;
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
		//$avetmiss->citizenship_status = $form->citizenship_status;
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
	
	if ($avetmiss->disability_flag == 'Yes') {
		$avetmiss->disability_types = $form->disability_types;
	}
	
	$avetmiss->prior_education_flag = $form->prior_education_flag;
	
	if ($avetmiss->prior_education_flag == 'Yes') 
	{
		$avetmiss->prior_educations = $form->prior_educations;
		$avetmiss->prior_education_qualification = $form->prior_education_qualification;
	}
	if ($form->city_of_birth != '') {
		$avetmiss->town_of_birth = $form->city_of_birth;
	}
	
	$party->avetmiss_child = $avetmiss;
	
	// CRICOS
	$cricos = new JRAPartyCricos ();
	$cricos->country_of_birth = $form->country_of_birth;
	//$cricos->citizenship_status = $form->citizenship_status;
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
	
	if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
	{
		echo "Party Variable: <br/>";
		var_dump ( $party );
		echo "<br/><br/>";
	}
	
	// Check if the Party Exists
	if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
	{
		echo "Check if Party exists<br/>";
		error_log ( "Check if Party exists<br/>" );
	}
	
	$party_result = JRAPartyOperations::getJRAParty ( $party );
	$party_attributes = $party_result->attributes ();
	$count = ( int ) $party_attributes ['total'];
	
	if ($count > 0) 
	{
		// Set Party ID
		$party_id = ( string ) $party_result->party->{'party-identifier'};
		
		// Check if the existing party already has a middle name specified
		// If so, make the middle name "blank" so it does not update (workaround)
		if ($party_result->party->{'middle-name'} != '') 
		{
			$party->middle_name = '';
		}
		
		$new_party = false;
		
		if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
		{
			echo "Party exists - Party ID: " . $party_id . "<br/><br/>";
			error_log ( "Party exists - Party ID: " . $party_id . "<br/><br/>" );
			echo "Result: <br/>";
			var_dump ( $party_result );
			echo "<br/><br/>";
		}
		
		// Update Party
		$update_party_xml = JRAPartyOperations::updateJRAPartyXML ( $party );
		
		if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
		{
			// echo "Update Party XML: <br/>";
			// var_dump($update_party_xml);
			// echo "<br/><br/>";
		}
		
		$update_party_result = JRAPartyOperations::updateJRAParty ( $update_party_xml, $party_id );
		
		if (isset ( $update_party_result->{'party-identifier'} )) 
		{
			if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
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
		if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
		{
			echo "Party does not exist - Create New Party<br/>";
			error_log ( "Party does not exist - Create New Party<br/>" );
		}
		
		// Create Party
		$party_xml = JRAPartyOperations::createJRAPartyXML ( $party );
		$party_result = JRAPartyOperations::createJRAParty ( $party_xml );
		$new_party = true;
		
		if (isset ( $party_result->{'party-identifier'} )) 
		{
			$party_id = ( string ) $party_result->{'party-identifier'};
			if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
			{
				echo "Party created - Party ID: " . $party_id . "<br/><br/>";
				error_log ( "Party created - Party ID: " . $party_id . "<br/><br/>" );
			}
		} 
		else 
		{
			if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
			{
				echo "An error occurred while creating a Party<br/><br/>";
				error_log ( "An error occurred while creating a Party<br/><br/>" );
			}
			return false;
		}
	}
	
	if (isset ( $party_id ) && isset ( $employer_party_id )) 
	{
		// Create Employee Resource
		if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
		{
			echo "Create an Employee Resource<br/>";
			error_log ( "Create an Employee Resource<br/>" );
		}
		
		// Setup the "Employee" Resource
		$employee = new JRAEmployee ();
		$employee->employer_party_identifier = $employer_party_id;
		$employee->start_date = current_time ( 'Y-m-d' );
		
		// Check to see if the "Employee" resource already exists
		if ($new_employer || $new_party) 
		{
			// Sets an empty array for employers (no need to retrieve employee resources)
			$employers = array ();
		}
		else 
		{
			// Retrieve all employers for this party
			$employers = JRAEmployeeOperations::getJRAEmployee ( $employer_party_id );
			
			if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
			{
				echo "Retrieve all Employers for this Party ID: " . count ( $employers ) . "<br/>";
				var_dump ( $employers );
				echo "<br/><br/>";
				error_log ( "Retrieve all Employers for this Party ID: " . count ( $employers ) . "<br/>" );
			}
		}
		
		// If employer_party_id is not in employers array, create the new Employee
		if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
		{
			echo "Checking to see if the employer_party_id ($employer_party_id) is in the employers array (above)<br/>";
		}
		
		if (! in_array ( $employer_party_id, $employers )) 
		{
			// Create "Employee" Resource
			$employee_xml = JRAEmployeeOperations::createJRAEmployeeXML ( $employee );
			
			if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
			{
				echo "Creating Employee Record<br/>";
				echo "Employee XML: <br/>";
				echo "<pre>" . $employee_xml . "</pre><br/><br/>";
			}
			
			$employee_result = JRAEmployeeOperations::createJRAEmployee ( $party_id, $employee_xml );
			
			if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
			{
				echo "Employee Result: <br/>";
				var_dump ( $employee_result );
				echo "<br/><br/>";
			}
			
			if (isset ( $employee_result->id )) 
			{
				if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
				{
					echo "Employee Resource Created<br/><br/>";
					error_log ( "Employee Resource Created<br/><br/>" );
				}
			} 
			else 
			{
				if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
				{
					echo "An error occurred while creating an Employee Resource<br/><br/>";
					error_log ( "An error occurred while creating an Employee Resource<br/><br/>" );
				}
			}
		}
		
		// Create Enrolment (not a prospect for Project Management)
		$enrolment = new JRAEnrolment ();
		$enrolment->party_identifier = $party_id;
		$enrolment->course_number = $form->course_number;
		$enrolment->study_reason = $form->study_reason;
		
		// Disabled until I can retrieve the reference data from Job Ready directly
		$enrolment->client_occupation_identifier = $form->occupation;
		$enrolment->client_industry_employment = $form->industry_employment;
		
		// There is no invoice options on this form so we don't capture it
		// $enrolment->invoice_option = $form->invoice_option;
		
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
		
		if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
		{
			echo "Create Enrolment<br/>";
		}
		
		// Create Enrolment
		$enrolment_xml = JRAEnrolmentOperations::createJRAEnrolmentXML ( $enrolment );
		
		if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
		{
			echo "Enrolment XML: <br/>";
			var_dump ( $enrolment_xml );
			echo "<br/><br/>";
		}
		
		$enrolment_result = JRAEnrolmentOperations::createJRAEnrolment ( $enrolment_xml );
		
		if (isset ( $enrolment_result->{'enrolment-identifier'} )) 
		{
			$enrolment_id = ( string ) $enrolment_result->{'enrolment-identifier'};
			if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
			{
				echo "Enrolment created - Enrolment ID: " . $enrolment_id . "<br/><br/>";
			}
		} 
		else 
		{
			if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
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
			if ($jra_party_contact->first_name == $party_contact->first_name && $jra_party_contact->surname == $party_contact->surname && $jra_party_contact->phone == $party_contact->phone) 
			{
				$party_contact_id = ( string ) $jra_party_contact->id;
				break;
			}
		}
		
		if (isset ( $party_contact_id )) 
		{
			if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
			{
				echo "Party Contact Exists<br/><br/>";
			}
			
			// Update Party Contact (email + phone number)
			$party_contact_update_result = JRAPartyContactOperations::updateJRAPartyContact ( $party_id, $party_contact_id, $party_contact );
		} 
		else 
		{
			if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
			{
				echo "Create Party Contact<br/>";
			}
			
			$party_contact_xml = JRAPartyContactOperations::createJRAPartyContactXML ( $party_contact );
			
			if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
			{
				echo "Party Contact XML: " . $party_contact_xml . "<br/><br/>";
			}
			
			$party_contact_result = JRAPartyContactOperations::createJRAPartyContact ( $party_id, $party_contact_xml );
			
			if (isset ( $party_contact_result->id )) 
			{
				if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
				{
					echo "Party Contact created - Party Contact ID: " . $party_contact->id . "<br/><br/>";
				}
			} 
			else 
			{
				if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
				{
					echo "Error occured when creating Party Contact<br/><br/>";
				}
			}
		}
		
		// Create the SKILL FIRST Party Document
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
			if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
			{
				echo "Party Document Created - Party Document ID: " . $sf_party_document_result->id . "<br/>";
			}
			
			// Remove PDF from server - unless debugging
			if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE)
			{
				unlink ( $sf_pdf_file );
			}
			
			if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
			{
				echo "PDF file removed from web server<br/><br/>";
			}
		}
		*/
		
		
		// Create "PartyDocument" and link the Application Form PDF to the Party
		$pdf_file = JR_ROOT_PATH . '/pdf/' . $pmfp;
		$document = new JRAPartyDocument ();
		$document->party_id = $party_id;
		$document->name = 'Project Management Application Form (' . $form->course_scope_code . ' - ' . $form->course_number . ')';
		$document->description = 'Project Management Application Form (' . $form->course_scope_code . ' - ' . $form->course_number . ')';
		$document->filename = $pdf_file;
		
		$party_document_result = JRAPartyDocumentOperations::createJRAPartyDocument ( $party_id, $document );
		
		if (isset ( $party_document_result->id )) 
		{
			if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
			{
				echo "Party Document Created - Party Document ID: " . $party_document_result->id . "<br/>";
				error_log ( "Party Document Created - Party Document ID: " . $party_document_result->id . "<br/>" );
			}
			
			// Remove PDF from server - unless debugging
			if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE)
			{
				unlink ( $pdf_file );
			}
			
			if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
			{
				echo "PDF file removed from web server<br/><br/>";
				error_log ( "PDF file removed from web server<br/><br/>" );
			}
			
			// Check course enrolment availability and sync from Job Ready if less than 3 remaining
			check_course_date_and_sync ( $form->course_number );
			
			// Define Gravity Form Linked Entry ID (from entry array)
			$gravity_form_linked_entry_id = ( int ) $form->gform_id;
			
			// Set up a Gravity Form keep array (array of form field id's to be kept on website database)
			$gf_keep_array = array (81, 82, 125, 126, 9,
									8, 21, 45, 46, 47,
									105, 118, 119, 58, 59 );
			
			// Gravity Form Party ID field #
			$gf_party_id_field = 125;
			
			// Gravity Form Employer Party ID field #
			$gf_employer_party_id_field = 126;
			
			// Clean up Gravity Form variables
			if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
			{
				echo "Confidential Clean Up currently disabled<br/><br/>";
				error_log ( "Confidential Clean Up currently disabled<br/><br/>" );
			}
			else 
			{
				confidential_clean_up_gf ( $gravity_form_linked_entry_id, $gf_keep_array, $party_id, $gf_party_id_field, $employer_party_id, $gf_employer_party_id_field );
			}
		} 
		else 
		{
			if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
			{
				echo "Error occurred when created Party Document<br/><br/>";
				error_log ( "Error occurred when created Party Document<br/><br/>" );
			}
		}
	}
	else 
	{
		if (PROJECT_MGMNT_DIPLOMA_DEBUG_MODE) 
		{
			echo "Party ID: " . $party_id . " OR Employer ID " . $employer_id . " does not exist";
			error_log ( "Error occurred when created Party Document<br/><br/>" );
		}
	}
}

// Calls the fuction "project_management_form_submission_process" after "Form #30: Project Management Form" has been submitted
add_action ( 'gform_after_submission_' . PROJECT_MANAGEMENT_DIPLOMA_APPLICATION_FORM, 'project_management_diploma_form_submission_process', 10, 2 );



/* *********** *
 * PDF-RELATED *
 * *********** */

// Project Management Application Form PDF
function project_management_diploma_application_form_pdf($form) {
	// Start
	$content = neca_pdf_content_start ();
	
	// Add Heading
	$content .= neca_pdf_content_heading ( '2022 Application Form<br/>BSB50820 Diploma in Project Management Process', $form );
	
	// Personal Details Content
	$content .= neca_pdf_content_personal_details ( $form );
	
	// Emergency Contanct Details Content
	$content .= neca_pdf_content_emergency_content_details ( $form );
	
	// Employer Content
	$content .= neca_pdf_content_employer_details ( $form );
	
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
	
	// Nationality Details Content
	$content .= neca_pdf_content_language_details ( $form, true );
	
	// Prior Education Details Content
	$content .= neca_pdf_content_education_details ( $form );
	
	// Disability Details Content
	$content .= neca_pdf_content_disability_details ( $form );
	
	// USI Details
	$content .= neca_pdf_content_usi ( $form );
	
	// Page End
	$content .= neca_pdf_page_end ();
	
	// Page Start
	$content .= neca_pdf_page_start ();
	
	// Enrolment Avetmiss Details Content
	$content .= neca_pdf_content_enrolment_avetmiss_details ( $form );
	
	// 14.01.2022 - Removed as requested by Ashima
	// Concession Details Content
	//$content .= neca_pdf_content_concession_details ( $form );
	
	// How did you hear Details Content
	$content .= neca_pdf_content_how_did_you_hear ( $form );
	
	// VSN Details
	$content .= neca_pdf_content_vsn ( $form );
	
	// Credit Transfer Details
	$content .= neca_pdf_content_credit_transfer ( $form );
	
	// Recognition of Prior Learning
	$content .= neca_pdf_content_recognition_of_prior_learning( $form );
	
	// Page End
	$content .= neca_pdf_page_end ();
	
	// Page Start
	$content .= neca_pdf_page_start ();
	
	// NECA Privacy Notice
	$content .= neca_pdf_content_privacy_policy_feb2023();
	
	// Student Enrolment Privacy Notice - Removed 13/09/2019
	// $content .= neca_pdf_content_student_enrolment_privacy_notice();
	
	// Page End
	$content .= neca_pdf_page_end ();
	
	// Page Start
	$content .= neca_pdf_page_start ();
	
	// Victorian Enrolment Privacy Notice
	$content .= neca_pdf_content_victorian_enrolment_privacy_notice ();
	
	// Page End
	$content .= neca_pdf_page_end ();
	
	// Page Start
	$content .= neca_pdf_page_start ();
	
	// All Students Must Read
	$content .= neca_pdf_content_all_students_must_read_sign_date_2019 ( $form );
	
	// Tickboxes
	$content .= neca_pdf_content_tickboxes_2019 ( true, true );
	
	// Signatures
	$content .= neca_pdf_content_signatures_2019 ( $form );
	
	// Page End
	$content .= neca_pdf_page_end ();
	
	// Page Start
	$content .= neca_pdf_page_start ();
	
	// Office Use Only
	$content .= neca_pdf_content_office_use_only_project_management_2020 ( $form );
	
	// Sign Off
	$content .= neca_pdf_content_sign_off_project_management_2020 ();
	
	// End
	$content .= neca_pdf_page_end ();
	
	// Setup Filename
	$filename = urlencode ( $form->course_number . '_' . $form->first_name . '_' . $form->surname . '_' . current_time ( 'Ymd_hms' ) ) . '.pdf';
	
	// Create PDF
	neca_create_pdf ( $filename, $content );
	
	return $filename;
}


function neca_pdf_content_office_use_only_project_management_2020($form) {
	$content = '<div class="heading3">OFFICE USE ONLY</div>';
	
	$content .= '<table class="tbl">
					<tr>
						<td colspan="2">
							<strong>PROGRAM DETAILS (Project Management)</strong><br/>
							Course Scope Code: ' . $form->course_scope_code . '
						</td>
					</tr>
				</table>';
	
	return $content;
}


function neca_pdf_content_sign_off_project_management_2020() {
	$content = '<div class="heading3">SIGN OFF</div>';
	
	$content .= '<table class="tbl">
					<tr>
						<td colspan="2">SIGN AND DATE TO CONFIRM THE FOLLOWING</td>
					</tr>';
	
	$content .= '	<tr>
						<td style="width: 5%;">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width: 95%;">Explained access to government subsidy</td>
					</tr>';
	
	$content .= '	<tr>
						<td style="width: 5%;">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width: 95%;">Provided the student with a copy of the Student manual and policy guide</td>
					</tr>
					<tr>
						<td style="width: 5%;">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width: 95%;">Explained the privacy policy and reporting</td>
					</tr>';
	
	$content .= '	<tr>
					<td style="width: 5%;">[&nbsp;&nbsp;&nbsp;]</td>
					<td style="width: 95%;">Completed and signed the current evidence of student eligibility and student declaration form in line with the current guidelines about determining student eligibility and supporting evidence</td>
				</tr>
				<tr>
					<td style="width: 5%;">[&nbsp;&nbsp;&nbsp;]</td>
					<td style="width: 95%;">The Guide to Fees & Payments was explained and provided to prior to the confirmation of my enrolment</td>
				</tr>
				<tr>
					<td style="width: 5%;">[&nbsp;&nbsp;&nbsp;]</td>
					<td style="width: 95%;">If applicable, has received a letter from the school releasing any student under the age of 17 and hasn\'t completed year 10</td>
				</tr>';
	
	$content .= '	<tr>
					<td style="width: 5%;">[&nbsp;&nbsp;&nbsp;]</td>
					<td style="width: 95%;">
						<strong>VET DATA USE STATEMENT AND RTO DECLARATION AND UNDERSTANDING</strong>
						<p class="small">VET Data Use STatement</p>
						<p class="small">
							Under the Data Provision Requirements 2012 and VET Data Policy (which includes the National VET Provider Collection Data Requirements Policy), Registered Training Organisations are required to collect and submit data compliant with AVETMISS for the National VET Provider Collection for all Nationally Recognised Training. This data is held by the National Centre for Vocational Education Research Ltd (NCVER), and may be used for the following purposes, to:<br/>
							<ul>
								<li>issue a VET Statement of Attainment of VET Qualification, and populate Authenticated VET Transcripts;</li>
								<li>facilitate statistics and research relating to education, including surveys;</li>
								<li>understand how the VET market operates, for policy, workforce planning and consumer information, and</li>
								<li>administer VET, including program administration, regulation, monitoring and evaluation.</li>
							</ul>
						</p>
						<p class="small">
							RTO Declaration and Understanding<br/>
							I declare that the information provided in this data submission is accurate and complete.<br/>
							I understand that information provided in this data submission about client training and outcomes may appear on Unique Student Identifer transcripts.<br/>
							I understand that:<br/>
							<ul>
								<li>information provided in this data submission will only be used, accessed, published and disseminated according to the National VET Data Policy;</li>
								<li>if that information also includes personal information, the Privacy Act 1988 and Australia Privacy Principles regulate the collection, use and disclosure of personal information.</li>
							</ul>
							I understand that:<br/>
							<ul>
								<li>information provided in this data submission may be used for the purposes outlined above, and</li>
								<li>identified RTO level information that supports consumer information, transparency and understanding of the national VET market may be published in reports, tables and a range of other data products, including data cubes and websites.</li>
							</ul>
						</p>
					</td>
				</tr>';
	
	$content .= '</table>
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
					<tr>
						<td style="width: 25%;">Position in RTO: </td>
						<td style="width: 75%;">&nbsp;<br/></td>
					</tr>
				</table>';
	
	return $content;
}


