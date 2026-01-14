<?php
/* ************** *
 * PRE-PROCESSING *
 * ************** */

// UEE30820 Electrical Apprenticeship Form (ELEC-APP)
add_filter("gform_pre_render_" . UEE30820_APPLICATION_FORM, 'uee30820_form_prepopulate');
add_filter("gform_pre_validation_" . UEE30820_APPLICATION_FORM, 'uee30820_form_prepopulate');
add_filter("gform_pre_submission_filter_" . UEE30820_APPLICATION_FORM, 'uee30820_form_prepopulate');
add_filter("gform_admin_pre_render" . UEE30820_APPLICATION_FORM, 'uee30820_form_prepopulate');

function uee30820_form_prepopulate($form)
{
	$prefill_fields= array();
	
	// 2024.11.06 - Removed Employer Details as requested by Lyn Wang
	/*
	// Pre-populates the Nonce Hidden Field
	$lookup_nonce = wp_create_nonce( 'neca_employer_lookup' );
	$prefill_fields['150'] = $lookup_nonce;
	*/
	
	if(isset($_SESSION['prefill']))
	{
		// 2024.01.03 - Add new field
		// 2024.11.06 - Removed by Lyn Wang
		//$prefill_fields['183'] = $_SESSION['prefill']->previously_enrolled_at_neca;
				
		$prefill_fields['2'] = isset($_SESSION['prefill']->title) ? $_SESSION['prefill']->title : '';
		$prefill_fields['9'] = isset($_SESSION['prefill']->first_name) ? $_SESSION['prefill']->first_name : '';
		//$prefill_fields['83'] = isset($_SESSION['prefill']->middle_name) ? $_SESSION['prefill']->middle_name : '';
		$prefill_fields['8'] = isset($_SESSION['prefill']->surname) ? $_SESSION['prefill']->surname : '';
		$prefill_fields['10'] = isset($_SESSION['prefill']->known_by) ? $_SESSION['prefill']->known_by : '';
		$prefill_fields['69'] = isset($_SESSION['prefill']->gender) ? $_SESSION['prefill']->gender : '';
		
		$prefill_fields['11'] = isset($_SESSION['prefill']->birth_date) ? $_SESSION['prefill']->birth_date : '';
		$prefill_fields['20'] = isset($_SESSION['prefill']->home_phone) ? $_SESSION['prefill']->home_phone : '';
		$prefill_fields['19'] = isset($_SESSION['prefill']->mobile_phone) ? $_SESSION['prefill']->mobile_phone : '';
		
		// Re-added the Email Field as it wasn't pre-filling
		//$prefill_fields['21'] = isset($_SESSION['prefill']->email) ? $_SESSION['prefill']->email : '';
		$prefill_fields['243'] = array(	isset($_SESSION['prefill']->email) ? $_SESSION['prefill']->email : '',
										isset($_SESSION['prefill']->email) ? $_SESSION['prefill']->email : '');
		
		// 2023.11.13 - Replaced street address1 with with street number and street name
		// $prefill_fields['97'] = $_SESSION['prefill']->street_address1;
		$prefill_fields['218'] = $_SESSION['prefill']->street_number;
		$prefill_fields['219'] = $_SESSION['prefill']->street_name;
		
		$prefill_fields['98'] = isset($_SESSION['prefill']->suburb) ? $_SESSION['prefill']->suburb : '';
		$prefill_fields['99'] = isset($_SESSION['prefill']->state) ? $_SESSION['prefill']->state : '';
		$prefill_fields['100'] = isset($_SESSION['prefill']->postcode) ? $_SESSION['prefill']->postcode : '';
		
		//$prefill_fields['101'] = isset($_SESSION['prefill']->postal_street_address1) ? $_SESSION['prefill']->postal_street_address1 : '';
		// 2023.11.13 - Replaced street address1 with with street number and street name
		// $prefill_fields['105'] = $_SESSION['prefill']->postal_street_address1;
		$prefill_fields['220'] = $_SESSION['prefill']->postal_street_number;
		$prefill_fields['221'] = $_SESSION['prefill']->postal_street_name;
		
		$prefill_fields['102'] = isset($_SESSION['prefill']->postal_suburb) ? $_SESSION['prefill']->postal_suburb : '';
		$prefill_fields['103'] = isset($_SESSION['prefill']->postal_state) ? $_SESSION['prefill']->postal_state : '';
		$prefill_fields['104'] = isset($_SESSION['prefill']->postal_postcode) ? $_SESSION['prefill']->postal_postcode : '';
		
		$prefill_fields['71'] = isset($_SESSION['prefill']->emergency_contact_firstname) ? $_SESSION['prefill']->emergency_contact_firstname : '';
		$prefill_fields['92'] = isset($_SESSION['prefill']->emergency_contact_surname) ? $_SESSION['prefill']->emergency_contact_surname : '';
		$prefill_fields['74'] = isset($_SESSION['prefill']->emergency_contact_relationship) ? $_SESSION['prefill']->emergency_contact_relationship : '';
		$prefill_fields['73'] = isset($_SESSION['prefill']->emergency_contact_number) ? $_SESSION['prefill']->emergency_contact_number : '';
		
		$prefill_fields['133'] = array(	isset($_SESSION['prefill']->emergency_contact_email) ? $_SESSION['prefill']->emergency_contact_email : '',
										isset($_SESSION['prefill']->emergency_contact_email) ? $_SESSION['prefill']->emergency_contact_email : '');
		
		$prefill_fields['205'] = isset($_SESSION['prefill']->labour_force_status) ? $_SESSION['prefill']->labour_force_status : '';
		
		// 11.02.2021 - Added as requested by Ranjita
		$prefill_fields['200'] = $_SESSION['prefill']->referred;
		$prefill_fields['201'] = $_SESSION['prefill']->referred_details;
		
		$prefill_fields['93'] = isset($_SESSION['prefill']->country_of_birth) ? $_SESSION['prefill']->country_of_birth : '';
		$prefill_fields['206'] = isset($_SESSION['prefill']->indigenous_status) ? $_SESSION['prefill']->indigenous_status : '';
		
		$prefill_fields['38'] = (isset($_SESSION['prefill']->main_language) && $_SESSION['prefill']->main_language == 'English') ? "English" : "";
		$prefill_fields['95'] = isset($_SESSION['prefill']->main_language) ? $_SESSION['prefill']->main_language : '';
		
		$prefill_fields['45'] = isset($_SESSION['prefill']->at_school_flag) ? $_SESSION['prefill']->at_school_flag : '';
		$prefill_fields['209'] = isset($_SESSION['prefill']->highest_school_level) ? $_SESSION['prefill']->highest_school_level : '';
		
		$prefill_fields['42'] = isset($_SESSION['prefill']->disability_flag) ? $_SESSION['prefill']->disability_flag : '';
		$prefill_fields['124'] = isset($_SESSION['prefill']->disability_other) ? $_SESSION['prefill']->disability_other : '';
		$prefill_fields['50'] = isset($_SESSION['prefill']->prior_education_flag) ? $_SESSION['prefill']->prior_education_flag : '';
		$prefill_fields['52'] = isset($_SESSION['prefill']->prior_education_qualification) ? $_SESSION['prefill']->prior_education_qualification : '';
		
		if(isset($_SESSION['prefill']->usi_number) && $_SESSION['prefill']->usi_number != '')
		{
			$prefill_fields['54'] = isset($_SESSION['prefill']->usi_number) ? $_SESSION['prefill']->usi_number : '';
		}
		
		// 2024.11.06 - Removed by Lyn Wang
		/*
		$prefill_fields['107'] = isset($_SESSION['prefill']->employer_company) ? $_SESSION['prefill']->employer_company : '';
		$prefill_fields['108'] = isset($_SESSION['prefill']->employer_address) ? $_SESSION['prefill']->employer_address : '';
		$prefill_fields['109'] = isset($_SESSION['prefill']->employer_suburb) ? $_SESSION['prefill']->employer_suburb : '';
		$prefill_fields['110'] = isset($_SESSION['prefill']->employer_state) ? $_SESSION['prefill']->employer_state : '';
		$prefill_fields['111'] = isset($_SESSION['prefill']->employer_postcode) ? $_SESSION['prefill']->employer_postcode : '';
		$prefill_fields['112'] = isset($_SESSION['prefill']->employer_office_phone) ? $_SESSION['prefill']->employer_office_phone : '';
		$prefill_fields['113'] = isset($_SESSION['prefill']->employer_supervisor_firstname) ? $_SESSION['prefill']->employer_supervisor_firstname : '';
		$prefill_fields['116'] = isset($_SESSION['prefill']->employer_supervisor_surname) ? $_SESSION['prefill']->employer_supervisor_surname : '';
		$prefill_fields['114'] = isset($_SESSION['prefill']->employer_supervisor_phone) ? $_SESSION['prefill']->employer_supervisor_phone : '';
		$prefill_fields['127'] = array(	'127' => isset($_SESSION['prefill']->employer_supervisor_email) ? $_SESSION['prefill']->employer_supervisor_email : '',
				'127.2' => isset($_SESSION['prefill']->employer_supervisor_email) ? $_SESSION['prefill']->employer_supervisor_email : '');
		$prefill_fields['118'] = isset($_SESSION['prefill']->employer_paying_invoice) ? $_SESSION['prefill']->employer_paying_invoice : '';
		*/
		
		$prefill_fields['68'] = isset($_SESSION['prefill']->study_reason) ? $_SESSION['prefill']->study_reason : '';
		$prefill_fields['96'] = isset($_SESSION['prefill']->industry_employment) ? $_SESSION['prefill']->industry_employment : '';
		$prefill_fields['66'] = isset($_SESSION['prefill']->occupation) ? $_SESSION['prefill']->occupation : '';
		$prefill_fields['119'] = isset($_SESSION['prefill']->concession_flag) ? $_SESSION['prefill']->concession_flag : '';
		$prefill_fields['67'] = isset($_SESSION['prefill']->how_did_you_hear) ? $_SESSION['prefill']->how_did_you_hear : '';
		
		$prefill_fields['54'] = isset($_SESSION['prefill']->usi_number) ? $_SESSION['prefill']->usi_number : '';
		
		$prefill_fields['57'] = isset($_SESSION['prefill']->previous_victorian_education) ? $_SESSION['prefill']->previous_victorian_education : '';
		$prefill_fields['56'] = isset($_SESSION['prefill']->vsn) ? $_SESSION['prefill']->vsn : '';
		$prefill_fields['58'] = isset($_SESSION['prefill']->previous_victorian_school) ? $_SESSION['prefill']->previous_victorian_school : '';
		$prefill_fields['59'] = isset($_SESSION['prefill']->previous_victorian_training) ? $_SESSION['prefill']->previous_victorian_training : '';
		
		$prefill_fields['202'] = isset($_SESSION['prefill']->credit_transfer) ? $_SESSION['prefill']->credit_transfer : '';
		$prefill_fields['203'] = isset($_SESSION['prefill']->rpl) ? $_SESSION['prefill']->rpl : '';
		
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
				//$prefill_fields['83'] = $form_fields->middle_name;
				$prefill_fields['8'] = $form_fields->surname;
				$prefill_fields['10'] = $form_fields->known_by;
				$prefill_fields['11'] = $form_fields->birth_date;
				$prefill_fields['69'] = $form_fields->gender;
				$prefill_fields['20'] = $form_fields->home_phone;
				$prefill_fields['19'] = $form_fields->mobile_phone;
				
				$prefill_fields['243'] = array(	'243' => $form_fields->email,
												'243.2' => $form_fields->email );
				
				// 2023.11.13 - Replaced Street Address with Street Number and Street Name
				//$prefill_fields['97'] = $form_fields->street_address1;
				$prefill_fields['218'] = $form_fields->street_number;
				$prefill_fields['219'] = $form_fields->street_name;
				
				
				$prefill_fields['98'] = $form_fields->suburb;
				$prefill_fields['99'] = $form_fields->state;
				$prefill_fields['100'] = $form_fields->postcode;
				
				// 2023.11.13 - Replaced street address1 with with street number and street name
				//$prefill_fields['101'] = $form_fields->postal_street_address1;
				$prefill_fields['220'] = $form_fields->postal_street_number;
				$prefill_fields['221'] = $form_fields->postal_street_name;
				$prefill_fields['102'] = $form_fields->postal_suburb;
				$prefill_fields['103'] = $form_fields->postal_state;
				$prefill_fields['104'] = $form_fields->postal_postcode;
				
				$prefill_fields['28'] = $form_fields->labour_force_status;
				$prefill_fields['34'] = $form_fields->indigenous_status;
				$prefill_fields['38'] = ($form_fields->main_language == 'English') ? "English" : "Yes";
				$prefill_fields['95'] = $form_fields->main_language;
				
				$prefill_fields['45'] = $form_fields->at_school_flag;
				$prefill_fields['47'] = $form_fields->highest_school_level;
				$prefill_fields['42'] = $form_fields->disability_flag;
				$prefill_fields['50'] = $form_fields->prior_education_flag;
				
				if($form_fields->citizenship_status == 'Australian Citizenship'
						|| $form_fields->citizenship_status == 'Permanent Humanitarian Visa Holder'
						|| $form_fields->citizenship_status == 'New Zealand Citizen')
				{
					$prefill_fields['153'] = "Yes";
					$prefill_fields['154'] = $form_fields->citizenship_status;
				}
				else
				{
					$prefill_fields['153'] = "No";
					$prefill_fields['155'] = $form_fields->citizenship_other;
				}
				
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
	
	// 2024.11.06 - Removed by Lyn Wang
	/*
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
				$prefill_fields['113'] = $form_fields->employer_supervisor_firstname;
				$prefill_fields['116'] = $form_fields->employer_supervisor_surname;
				$prefill_fields['114'] = $form_fields->employer_supervisor_phone;
				$prefill_fields['115'] = $form_fields->employer_supervisor_email;
				$prefill_fields['118'] = $form_fields->employer_paying_invoice;
			}
			else
			{
				unset($party_xml_object);
			}
		}
	}
	*/
	
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
		if($field->id == 205)
		{
			$field->choices = jrar_employment_category();
		}
		
		// Country
		if($field->id == 93 || $field->id == 155)
		{
			$field->choices = jrar_country();
		}
		
		// Citizenship Status
		if($field->id == 154)
		{
			$field->choices = jrar_citizenship_status();
		}
		
		
		// Indigenous Status
		if($field->id == 206)
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
		if($field->id == 209)
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
					foreach($field->inputs as $k => $input)
					{
						$input['defaultValue'] = $prefill_value[$k];
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




/* ************** *
 * PRE-SUBMISSION *
 * ************** */

// Electrical Apprenticeship Form (ELEC-APP)
add_action( "gform_pre_submission_" . UEE30820_APPLICATION_FORM, 'uee30820_form_presubmission');

function uee30820_form_presubmission()
{
	if(isset($_SESSION['prefill']))
	{
		$prefill = $_SESSION['prefill'];
	}
	else
	{
		$prefill = new stdClass();
	}
	
	// 2024.11.06 - Removed by Lyn Wang
	//$prefill->previously_enrolled_at_neca = $_POST['input_183'];
	
	$prefill->title = $_POST['input_2'];
	$prefill->first_name = $_POST['input_9'];
	//$prefill->middle_name = $_POST['input_83'];
	$prefill->surname = $_POST['input_8'];
	$prefill->known_by = $_POST['input_10'];
	$prefill->birth_date = $_POST['input_11'];
	$prefill->gender = $_POST['input_69'];
	$prefill->home_phone = $_POST['input_20'];
	$prefill->mobile_phone = $_POST['input_19'];
	$prefill->email = $_POST['input_243'];
	// Replace Street Address 1 with Street Number and Street Name
	//$prefill->street_address1 = $_POST['input_97'];
	$prefill->street_number = $_POST['input_218'];
	$prefill->street_name = $_POST['input_219'];
	$prefill->suburb = $_POST['input_98'];
	$prefill->state = $_POST['input_99'];
	$prefill->postcode = $_POST['input_100'];
	$prefill->postal_address_same = isset($_POST['input_87_1']) ? "Yes" : "";
	
	// 2023.11.13 - Replaced street address1 with with street number and street name
	// $prefill->postal_street_address1 = $_POST['input_101'];
	$prefill->postal_street_number = isset($_POST['input_220']) ? $_POST['input_220'] : '';
	$prefill->postal_street_name = isset($_POST['input_221']) ? $_POST['input_221'] : '';
	$prefill->postal_suburb = isset($_POST['input_102']) ? $_POST['input_102'] : '';
	$prefill->postal_state = isset($_POST['input_103']) ? $_POST['input_103'] : '';
	$prefill->postal_postcode = isset($_POST['input_104']) ? $_POST['input_104'] : '';

	$prefill->emergency_contact_firstname = $_POST['input_71'];
	$prefill->emergency_contact_surname = $_POST['input_92'];
	$prefill->emergency_contact_number = $_POST['input_73'];
					
	if(isset($_POST['input_133']))
	{
		$prefill->emergency_contact_email = $_POST['input_133'];
	}
						
	$prefill->emergency_contact_relationship = $_POST['input_74'];
	$prefill->labour_force_status = $_POST['input_205'];
	
	// 11.02.2021 - Added as requested by Ranjita
	$prefill->referred = $_POST['input_200'];
	$prefill->referred_details = $_POST['input_201'];
	
	$prefill->country_of_birth = $_POST['input_93'];
	
	$prefill->indigenous_status = $_POST['input_206'];
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
	$prefill->highest_school_level = $_POST['input_209'];
	
	
	// 2021.02.15 - Added as requested by Ranjita
	$prefill->credit_transfer = $_POST['input_202'];
	$prefill->rpl = $_POST['input_203'];
	
	
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
	
	// 2024.11.06 - Removed by Lyn Wang
	/*
	if(isset($_POST['input_107']))
		$prefill->employer_company = $_POST['input_107'];
		
	if(isset($_POST['input_108']))
		$prefill->employer_address = $_POST['input_108'];
		
	if(isset($_POST['input_109']))
		$prefill->employer_suburb = $_POST['input_109'];
		
	if(isset($_POST['input_110']))
		$prefill->employer_state = $_POST['input_110'];
		
	if(isset($_POST['input_111']))
		$prefill->employer_postcode = $_POST['input_111'];
		
	if(isset($_POST['input_112']))
		$prefill->employer_office_phone = $_POST['input_112'];
		
	if(isset($_POST['input_113']))
		$prefill->employer_supervisor_firstname = $_POST['input_113'];
		
	if(isset($_POST['input_116']))
		$prefill->employer_supervisor_surname = $_POST['input_116'];
		
	if(isset($_POST['input_114']))
		$prefill->employer_supervisor_phone = $_POST['input_114'];
		
	if(isset($_POST['input_127']))
		$prefill->employer_supervisor_email = $_POST['input_127'];
		
	$prefill->employer_paying_invoice = $_POST['input_118'];
	*/
	
	if(isset($_POST['input_54']))
		$prefill->usi_number = $_POST['input_54'];
		
	$prefill->study_reason = $_POST['input_68'];
	$prefill->industry_employment = $_POST['input_96'];
	$prefill->occupation = $_POST['input_66'];
	$prefill->concession_flag = $_POST['input_119'];
	$prefill->how_did_you_hear = $_POST['input_67'];
	
	$prefill->previous_victorian_education = $_POST['input_57'];
	if(isset($_POST['input_56']))
	{
		$prefill->vsn = $_POST['input_56'];
	}
	
	if(isset($_POST['input_58']))
		$prefill->previous_victorian_school = $_POST['input_58'];
		
	if(isset($_POST['input_59']))
		$prefill->previous_victorian_training = $_POST['input_59'];
 								
	$_SESSION['prefill'] = $prefill;
}





/* *************** *
 * POST PROCESSING *
 * *************** */

// Calls the fuction "uee30820_form_submission_process" after "Form #17: Electrical Apprenticeship Form" has been submitted
add_action('gform_after_submission_' . UEE30820_APPLICATION_FORM, 'uee30820_form_submission_process', 10, 2);

function uee30820_form_submission_process($entry, $form_data) 
{
	if(UEE30820_DEBUG_MODE)
	{
		echo "<h3>Electrical Apprenticeship Submission Process</h3>";
		error_log("--- Electrical Apprenticeship Submission Process ---");
	}
	
	$form = new JobReadyForm();
	
	// Gravity Form
	$form->gform_id = $entry['id'];
	$form->gform_form_id = $entry['form_id'];
	
	// Course Details
	$form->course_scope_code = rgar($entry, '81');
	//$form->course_number = rgar($entry, '82');
	// Select preferred campus to identify the course_number
	// Carlton - Cartlon Holding Bay
	// Dandenong - Dandenong Holding Bay
	$form->course_number = rgar($entry, '216');
	
	// 03.01.2024 - Previously enrolled at NECA
	// $form->previously_enrolled_at_neca = rgar($entry, '223'); // Was 183
	
	// Personal Details
	$form->gender = rgar($entry, '69');
	$form->title = rgar($entry, '2');
	$form->first_name = ucwords(strtolower(rgar($entry, '9')));
	//$form->middle_name = ucwords(strtolower(rgar($entry, '83')));
	$form->surname = ucwords(strtolower(rgar($entry, '8')));
	$form->known_by = ucwords(strtolower(rgar($entry, '10')));
	$form->birth_date = rgar($entry, '11');
	
	// Contact Details
	$form->home_phone = rgar($entry,'20');
	$form->mobile_phone = rgar($entry,'19');
	$form->email = strtolower(rgar($entry,'243'));
	
	// Address
	// 2023.11.13 - Replaced street_address1 with street_number and street_name
	//$form->street_address1 = ucwords(strtolower(rgar($entry, '97')));
	$form->street_number = ucwords(strtolower(rgar($entry, '218')));
	$form->street_name = ucwords(strtolower(rgar($entry, '219')));
	$form->street_address1 = $form->street_number . ' ' . $form->street_name;	
	$form->suburb = ucwords(strtolower(rgar($entry, '98')));
	$form->state = ucwords(strtolower(rgar($entry, '99')));
	$form->postcode = rgar($entry, '100');
	
	$form->postal_address_same = rgar($entry, '87.1'); // 87.1 because its a checkbox
	
	if($form->postal_address_same != 'Yes')
	{
		// Address
		// 2023.11.13 - Replaced street_address1 with street_number and street_name
		//$form->postal_street_address1 = ucwords(strtolower(rgar($entry, '101')));
		$form->postal_street_number = ucwords(strtolower(rgar($entry, '220')));
		$form->postal_street_name = ucwords(strtolower(rgar($entry, '221')));
		$form->postal_street_address1 = $form->postal_street_number . ' ' . $form->postal_street_name;
		$form->postal_suburb = ucwords(strtolower(rgar($entry, '102')));
		$form->postal_state = ucwords(strtolower(rgar($entry, '103')));
		$form->postal_postcode = rgar($entry, '104');
	}
	
	// Emergency Contact
	$form->emergency_contact_firstname = ucwords(strtolower(rgar($entry, '71')));
	$form->emergency_contact_surname = ucwords(strtolower(rgar($entry, '92')));
	$form->emergency_contact_number = rgar($entry, '73');
	$form->emergency_contact_email = rgar($entry, '133');
	$form->emergency_contact_relationship = ucwords(strtolower(rgar($entry, '74')));
	
	// Labour Force
	$form->labour_force_status = rgar($entry, '205');
	
	// 11.02.2021 - Added as requested by Ranjita
	// Referred from a Job Seeker
	$form->referred = rgar( $entry, '200');
	if($form->referred == 'Yes')
	{
		$form->referred_details = rgar( $entry, '201');
	}
	
	// Birth + Nationality + Indigenous + Language
	$form->country_of_birth = $entry['93'];
	 
	$form->indigenous_status = rgar($entry, '206');
	 
	$language_other_than_english = rgar($entry, '38');
	if($language_other_than_english != 'Yes')
	{
		$form->main_language = "English";
		// $form->spoken_english_proficiency= "Very Well";
	}
	else
	{
		$form->main_language = rgar($entry, '95');
		// $form->spoken_english_proficiency= "Very Well";
	}
	 
	$language_other_than_english = rgar($entry, '38');
	if($language_other_than_english != 'Yes') 
	{
		$form->main_language = "English";
		// $form->spoken_english_proficiency= "Very Well";
	}
	else
	{
		$form->main_language = rgar($entry, '95');
		// $form->spoken_english_proficiency= "Very Well";
	}
	 
	// School Details
	$form->at_school_flag = rgar($entry, '45');
	$form->highest_school_level = rgar($entry, '209');
	 
	// Disability
	$form->disability_types = array();
	$form->disability_flag = rgar($entry, '42');
	if($form->disability_flag == 'Yes')
	{
		// Array fields are passed in as 43.1, 43.2, 43.3.... so we have iterate through them
		for($i = 1; $i < 11; $i ++) 
		{
			$ref = '43.' . $i;
			if(isset($entry[$ref]) && $entry[$ref] != '') 
			{
				$form->disability_types[] = $entry[$ref];
			}
		}
	}
	 
	// Prior Education
	$form->prior_educations = array();
	$form->prior_education_flag = rgar($entry, 50);
	if($form->prior_education_flag == 'Yes') 
	{
		// Array fields are passed in as 51.1, 51.2, 51.3.... so we have iterate through them
		for($i = 1; $i <= 20; $i ++) 
		{
			$ref = '51.' . $i;
			if($entry[$ref] != '') 
			{
				$form->prior_educations[] = $entry[$ref];
			}
		}
		$form->prior_education_qualification = $entry['52'];
	}
	 
	// Employer Details
	/* Removed 06.11.2024 - Lyn Wang
	$form->employer_party_id = rgar($entry, 148);
	$form->employer_search_or_create = rgar($entry, 145);
	$form->employer_party_new =($form->employer_search_or_create == 'Create a new employer in our system') ? true : false;
	 
	if($form->employer_party_new)
	{
		$form->employer_company = ucwords(strtolower(rgar($entry, 107)));
		$form->employer_address = ucwords(strtolower(rgar($entry, 108)));
		$form->employer_suburb = ucwords(strtolower(rgar($entry, 109)));
		$form->employer_state = ucwords(strtolower(rgar($entry, 110)));
		$form->employer_postcode = rgar($entry, 111);
		$form->employer_office_phone = rgar($entry, 112);
		$form->employer_supervisor_firstname = ucwords(strtolower(rgar($entry, 113)));
		$form->employer_supervisor_surname = ucwords(strtolower(rgar($entry, 116)));
		$form->employer_supervisor_phone = rgar($entry, 114);
		$form->employer_supervisor_email = strtolower(rgar($entry, 127));
	}
	$form->employer_paying_invoice = rgar($entry, 118);
	*/
	 
	// Unique Student Number
	$form->usi_flag = "Yes";
	$form->usi_number = rgar($entry, 54);
	 
	// Enrolment > Skills VIC AVETMISS
	$form->study_reason = rgar($entry, '68');
	$form->industry_employment = rgar($entry, '96');
	$form->occupation = rgar($entry, '66');
	 
	// Concession Card
	$form->concession_flag = rgar($entry, '119');
	 
	// 2024.01.17 - Would you describe yourself as belonging to any of the following cohorts?
	$form->cohorts = array();
	for($i = 1; $i < 20; $i ++)
	{
		$ref = '233.' . $i;
		$cohort_value = rgar($entry, $ref);
		if ($cohort_value != '' && $cohort_value != 'NNNNNN')
		{
			$form->cohorts [] = $cohort_value;
		}
	}

	// How did you hear
	$form->how_did_you_hear = ucwords(strtolower(rgar($entry, '67')));
	
	// Victorian Student
	$form->previous_victorian_education = rgar($entry, '57');
	if(strpos($form->previous_victorian_education, 'Yes') !== false)
	{
		$form->vsn = rgar($entry, '57');
	}
	 
	if(strpos($form->previous_victorian_education, 'Yes') !== false && isset($entry['58'] ) && $entry['58'] != '') 
	{
		$form->previous_victorian_school = ucwords(strtolower(rgar($entry, '58')));
	}
	if(strpos($form->previous_victorian_education, 'Yes') !== false && isset($entry['59'] ) && $entry['59'] != '') 
	{
		$form->previous_victorian_school = ucwords(strtolower(rgar($entry, '59')));
	}
	
	// Student Declaraction
	$form->privacy_declaration = $entry['60.1'];
	
	// 2018.09.05 - Credit Transfer Added
	$form->credit_transfer = rgar($entry, '202');
	$form->rpl = rgar($entry, '203');
	
	// 2018.09.05 - Signature
	$form->signature = ucwords(strtolower(rgar($entry, '152') ));

	// 2023.01.23 - Added File Attachments
	// 2023.06.08 - Remove Concession Card Upload - Requested by Ashima
	//$form->concession_card_file = $entry['208'];
	$form->file_usi_transcript = $entry['210'];
	
	if(UEE30820_DEBUG_MODE)
	{
		echo "Form Variable: <br/>";
		var_dump($form);
		echo "<br/><br/>";
	}
	 
	/*
	 * Setup Job Ready Employer Party / Employer Party Address / Employer Contact Details / Supervisor Party / Supervisor Party Address / Supervisor Contact Details
	 * and Create accordingly
	 */
	
	// 2024.11.06 - Removed by Lyn Wang
	/*
	$new_employer = $form->employer_party_new;
	 
	if($form->employer_party_new)
	{
		// Setup "Employer Party" Resource
		$employer_party = new JRAParty();
		$employer_party->party_type = 'Employer';
		$employer_party->contact_method = 'Email';
		$employer_party->first_name = $form->employer_supervisor_firstname;
		$employer_party->surname = $form->employer_supervisor_surname;
		$employer_party->legal_name = $form->employer_company;
		$employer_party->trading_name = $form->employer_company;
		
		// Setup "Party > Address" Child Resources
		$employer_party_addresses = array();
		
		$employer_party_address = new JRAPartyAddress();
		$employer_party_address->primary = 'true';
		$employer_party_address->street_address1 = $form->employer_address;
		$employer_party_address->suburb = $form->employer_suburb;
		$employer_party_address->state = $form->employer_state;
		$employer_party_address->country = 'Australia';
		$employer_party_address->post_code = $form->employer_postcode;
		$employer_party_address->location = "Work";
		
		// Add to employer_party_addresses array
		array_push($employer_party_addresses, $employer_party_address);
		
		$employer_party->address_child = $employer_party_addresses;
		
		// Setup "Party > Contact Detail" Child Resources
		$employer_contact_details = array();
		
		$employer_contact_detail = new JRAPartyContactDetail();
		$employer_contact_detail->primary = 'true';
		$employer_contact_detail->contact_type = 'Email';
		$employer_contact_detail->value = $form->employer_supervisor_email;
		array_push($employer_contact_details, $employer_contact_detail);
		
		if(trim($form->employer_office_phone != ''))
		{
			$employer_contact_detail = new JRAPartyContactDetail();
			$employer_contact_detail->primary = '';
			$employer_contact_detail->contact_type = 'Phone';
			$employer_contact_detail->value = $form->employer_office_phone;
			array_push($employer_contact_details, $employer_contact_detail);
		}
		
		if(trim($form->employer_supervisor_phone != ''))
		{
			$employer_contact_detail = new JRAPartyContactDetail();
			$employer_contact_detail->primary = '';
			$employer_contact_detail->contact_type = 'Mobile';
			$employer_contact_detail->value = $form->employer_supervisor_phone;
			array_push($employer_contact_details, $employer_contact_detail);
		}
		
		$employer_party->contact_detail_child = $employer_contact_details;
		
		if(UEE30820_DEBUG_MODE)
		{
			echo "Check if Employer Party exists<br/>";
			var_dump($employer_party);
			echo "<br/><br/>";
		}
		
		// Check if the Employer Party Exists
		$employer_party_result = JRAPartyOperations::getJRAParty($employer_party);
		$employer_party_attributes = $employer_party_result->attributes();
		$count =(int ) $employer_party_attributes['total'];
		
		if($count > 0)
		{
			// Set the Employer Party ID
			$employer_id =(int ) $employer_party_result->party->{'id'};
			$employer_party_id =(string ) $employer_party_result->party->{'party-identifier'};
			$employer_trading_name =(string ) $employer_party_result->party->{'trading_name'};
			$new_employer = false;
			
			if(UEE30820_DEBUG_MODE)
			{
				echo "Employer Party exists - Employer ID: " . $employer_id . "<br/><br/>";
				echo "Employer Party exists - Employer Party ID: " . $employer_party_id . "<br/><br/>";
				error_log("Employer Party exists - Employer Party ID: " . $employer_party_id . "<br/><br/>");
			}
			
		}
		else
		{
			if(UEE30820_DEBUG_MODE)
			{
				echo "Employer does not exist - Create Employer Party<br/>";
				error_log("Employer does not exist - Create Employer Party<br/>");
			}
			
			// Create Employer Party
			$employer_party_xml = JRAPartyOperations::createJRAPartyXML($employer_party);
			$employer_party_result = JRAPartyOperations::createJRAParty($employer_party_xml);
			
			if(isset($employer_party_result->{'party-identifier'} ))
			{
				$employer_id =(int) $employer_party_result->party->{'id'};
				$employer_party_id =(string) $employer_party_result->{'party-identifier'};
				$employer_trading_name =(string) $form->employer_company;
				$new_employer = true;
				
				if(UEE30820_DEBUG_MODE)
				{
					echo "Employer created - Employer ID: " . $employer_id . "<br/><br/>";
					echo "Employer created - Employer Party ID: " . $employer_party_id . "<br/><br/>";
					error_log("Employer created - Employer Party ID: " . $employer_party_id . "<br/><br/>");
				}
			}
			else
			{
				if(UEE30820_DEBUG_MODE)
				{
					echo "An error occured while creating an Employer Party<br/><br/>";
					error_log("An error occured while creating an Employer Party<br/><br/>");
				}
				return false;
			}
		}
		
		// Sets the Employer Party ID
		$form->employer_id = $employer_id;
		$form->employer_party_id = $employer_party_id;
		$form->employer_party_new = $new_employer;
				
		// Create Party Resource for Supervisor Contact
		$supervisor_party = new JRAParty();
		$supervisor_party->party_type = 'Person';
		$supervisor_party->contact_method = 'Email';
		$supervisor_party->first_name = $form->employer_supervisor_firstname;
		$supervisor_party->surname = $form->employer_supervisor_surname;
		
		// Setup "Party > Address" Child Resources
		$supervisor_party_addresses = array();
		
		$supervisor_party_address = new JRAPartyAddress();
		$supervisor_party_address->primary = 'true';
		$supervisor_party_address->street_address1 = $form->employer_address;
		$supervisor_party_address->suburb = $form->employer_suburb;
		$supervisor_party_address->state = $form->employer_state;
		$supervisor_party_address->country = 'Australia';
		$supervisor_party_address->post_code = $form->employer_postcode;
		$supervisor_party_address->location = "Work";
		
		// Add to employer_party_addresses array
		array_push($supervisor_party_addresses, $supervisor_party_address);
		
		$supervisor_party->address_child = $supervisor_party_addresses;
		
		// Setup "Party > Contact Detail" Child Resources
		$supervisor_contact_details = array();
		
		$supervisor_contact_detail = new JRAPartyContactDetail();
		$supervisor_contact_detail->primary = 'true';
		$supervisor_contact_detail->contact_type = 'Email';
		$supervisor_contact_detail->value = $form->employer_supervisor_email;
		array_push($supervisor_contact_details, $supervisor_contact_detail);
		
		if(trim($form->employer_supervisor_phone != ''))
		{
			$supervisor_contact_detail = new JRAPartyContactDetail();
			$supervisor_contact_detail->primary = '';
			$supervisor_contact_detail->contact_type = 'Mobile';
			$supervisor_contact_detail->value = $form->employer_supervisor_phone;
			array_push($supervisor_contact_details, $supervisor_contact_detail);
		}
		
		$supervisor_party->contact_detail_child = $employer_contact_details;
		
		// Check if the Supervisor Party Exists
		if(UEE30820_DEBUG_MODE)
		{
			echo "Check if Supervisor Party exists<br/>";
			error_log("Check if Supervisor Party exists<br/>");
		}
	 	
		// NOTE: Supervisor Party matches on Firstname, Surname and Email(as DOB is not available)
		$supervisor_party_result = JRAPartyOperations::getJRAParty($supervisor_party, $form->employer_supervisor_email);
		$supervisor_party_attributes = $supervisor_party_result->attributes();
		$count =(int ) $supervisor_party_attributes['total'];
		
		if($count > 0)
		{
			// Set Supervisor Party ID
			$supervisor_party_id =(string ) $supervisor_party_result->party->{'party-identifier'};
			
			if(UEE30820_DEBUG_MODE)
			{
				echo "Supervisor Party exists - Supervisor ID: " . $supervisor_party_id . "<br/><br/>";
				error_log("Supervisor Party exists - Supervisor ID: " . $supervisor_party_id . "<br/><br/>");
			}
		}
		else
		{
			if(UEE30820_DEBUG_MODE)
			{
				echo "Supervisor Party does not exists - Create Supervisor Party<br/>";
				error_log("Supervisor Party does not exists - Create Supervisor Party<br/>");
			}
			
			// Create Supervisor Party
			$supervisor_party_xml = JRAPartyOperations::createJRAPartyXML($supervisor_party);
			$supervisor_party_result = JRAPartyOperations::createJRAParty($supervisor_party_xml);
			
			if(isset($supervisor_party_result->{'party-identifier'} ))
			{
				$supervisor_party_id =(string ) $supervisor_party_result->{'party-identifier'};
				if(UEE30820_DEBUG_MODE)
				{
					echo "Supervisor Party created - Supervisor ID: " . $supervisor_party_id . "<br/><br/>";
					error_log("Supervisor Party created - Supervisor ID: " . $supervisor_party_id . "<br/><br/>");
				}
			}
			else
			{
				if(UEE30820_DEBUG_MODE)
				{
					echo "An error occurred while creating a Supervisor Party<br/><br/>";
					error_log("An error occurred while creating a Supervisor Party<br/><br/>");
				}
				return false;
			}
		}
	}
	else
	{
		$employer_party_id = $form->employer_party_id;
	 	
	 	// Lookup the Employer by Party ID and retrieve the Job Ready ID
	 	$employer = JobReadyEmployerOperations::loadJobReadyEmployerByPartyID($employer_party_id);
	 	$employer_id = $employer->job_ready_id;
	}

	
	// Check if we are using an existing Employer and if so, load their Employer information into the
	// FORM variables so they are included in the PDF
	 
	if(UEE30820_DEBUG_MODE)
	{
		echo "Employer Party New: " . $form->employer_party_new . "<br/>";
	}
	 
	if(! $form->employer_party_new)
	{
		if(UEE30820_DEBUG_MODE)
	 	{
	 		echo "This is not a new employer, so load the employer details and set the form variables<br/>";
	 	}
	 	
	 	// Load the Employer Party from Job Ready
	 	$employer_party_result = JRAEmployerOperations::getJRAEmployer($employer_party_id);
	 	
	 	if(UEE30820_DEBUG_MODE)
	 	{
// 	 		echo "Employer Party Lookup Result: <br/>";
// 	 		var_dump($employer_party_result);
// 	 		echo "<br/><br/>";
	 	}
	 	
	 	// Confirms a valid response from Job Ready
	 	if($employer_party_result !== false)
	 	{
	 		
	 		// Convert the XML to an Object
	 		$employer_object = xmlToObject($employer_party_result);
	 		
	 		if(UEE30820_DEBUG_MODE)
	 		{
// 	 			echo "Employer Object: <br/>";
// 	 			var_dump($employer_object);
// 	 			echo "<br/><br/>";
	 		}
	 		
	 		$employer_id = $employer_object->id;
	 		$form->employer_party_id = $employer_object->party_id;
	 		
	 		// Set the Employer Address
	 		$employer_address = $employer_object->addresses->address;
	 		$employer_phone = '';
	 		
	 		// Loops through all contact detail
	 		if( isset($employer_object->{'contact-details'}->{'contact-detail'}) && is_array($employer_object->{'contact-details'}->{'contact-detail'}) )
	 		{
		 		foreach($employer_object->{'contact-details'}->{'contact-detail'} as $contact_detail )
		 		{
		 			if($contact_detail->{'contact-type'} == 'Phone')
		 			{
		 				$employer_phone =(string) $contact_detail->value;
		 				break;
		 			}
		 		}
	 		}
	 		
	 		$form->employer_company = isset($employer_object->{'trading-name'} ) ?(string) $employer_object->{'trading-name'} : '';
	 		$form->employer_address = isset($employer_address->{'street-address1'} ) ?(string) $employer_address->{'street-address1'} : '';
	 		$form->employer_suburb = isset($employer_address->suburb ) ?(string) $employer_address->suburb : '';
	 		$form->employer_state = isset($employer_address->state ) ?(string) $employer_address->state : '';
	 		$form->employer_postcode = isset($employer_address->{'post-code'} ) ?(string) $employer_address->{'post-code'} : '';
	 		$form->employer_office_phone =(string) $employer_phone;
	 		
	 		if(UEE30820_DEBUG_MODE)
	 		{
	 			echo "Form Variable updated after load employer details into it";
	 			var_dump($form);
	 			echo "<br/><br/>";
	 		}
	 		
	 		unset($employer_party_result);
	 		unset($employer_object);
	 		unset($employer_address);
	 		unset($employer_phone);
	 	}
	}
	*/
	 
	if(UEE30820_DEBUG_MODE)
	{
		echo "Create PDF<br/><br/>";
	}
	 
	// Create PDF
	$apprentice_pdf = uee30820_application_form_pdf($form);
	if(UEE30820_DEBUG_MODE)
	{
		echo '<a href="' . JR_ROOT_URL . '/pdf/' . $apprentice_pdf . '" target="_blank">Apprentice Applcation Form(PDF)</a><br/><br/>';
	}
	 
	// Create Skills First PDF
	/* 2023.01.16 - Removal request by Ashima Nakra (email)
	$course_name = 'Electrical Apprenticeship Application Form';
	$sfp = skills_first_pdf($form, $course_name);
	 
	if(UEE30820_DEBUG_MODE)
	{
		echo '<a href="' . JR_ROOT_URL . '/pdf/' . $sfp . '" target="_blank">Skills First(PDF)</a><br/><br/>';
	}
	*/
	
	 
	// Create Pre Training Review PDF
	/* 2023.01.16 - Removal request by Ashima Nakra (email)
	$course_name = 'UEE30811 Certificate III in Electrotechnology(Electrician)';
	$ptrp = pre_training_review_pdf($form, $course_name);
	
	if(UEE30820_DEBUG_MODE)
	{
		echo '<a href="' . JR_ROOT_URL . '/pdf/' . $ptrp . '" target="_blank">Pre Training Review(PDF)</a><br/><br/>';
	}
	*/
	
 
	// Setup "Party" Resource
	$party = new JRAParty();
	$party->party_type = 'Person';
	$party->contact_method = 'Email';
	$party->first_name = $form->first_name;
	//$party->middle_name = $form->middle_name;
	$party->surname = $form->surname;
	$party->known_by = $form->known_by;
	$party->birth_date = date_create_from_format("Y-m-d", $form->birth_date, timezone_open("Australia/Melbourne" ));
	$party->gender = $form->gender;
	$party->title = $form->title;
	$party->usi_number = $form->usi_number;
 
	// Setup "Party > Address" Child Resources
	$party_addresses = array();
	$party_address = new JRAPartyAddress();
	$party_address->primary = 'true';
	// $party_address->street_address1 = $form->street_address1;
	$party_address->street_number = $form->street_number;
	$party_address->street_name = $form->street_name;
	$party_address->suburb = $form->suburb;
	$party_address->state = $form->state;
	$party_address->country = 'Australia';
	$party_address->post_code = $form->postcode;
	$party_address->location = "Home";
 
	if(UEE30820_DEBUG_MODE)
	{
		echo '<div>Party Address: <br/><br/>';
		var_dump($party_address);
		echo '</div><br/><br/>';
	}
	
	// Add to party_addresses array
	array_push($party_addresses, $party_address);
 
	// Add Postal Address?
	if($form->postal_address_same != 'Yes') 
	{
		$postal_address = new JRAPartyAddress();
		$postal_address->primary = '';
		// $postal_address->street_address1 = $form->postal_street_address1;
		$postal_address->street_number = $form->postal_street_number;
		$postal_address->street_name = $form->postal_street_name;
		$postal_address->suburb = $form->postal_suburb;
		$postal_address->state = $form->postal_state;
		$postal_address->country = 'Australia';
		$postal_address->post_code = $form->postal_postcode;
		$postal_address->location = "Postal";
		
		if(UEE30820_DEBUG_MODE)
		{
			echo '<div>Party Postal Address: <br/><br/>';
			var_dump($postal_address);
			echo '</div><br/><br/>';
		}
		
		array_push($party_addresses, $postal_address);
 	}
 
	$party->address_child = $party_addresses;
 
	// Setup "Party > Contact Detail" Child Resources
	$contact_details = array();
	$contact_detail = new JRAPartyContactDetail();
	$contact_detail->primary = 'true';
	$contact_detail->contact_type = 'Email';
	$contact_detail->value = $form->email;
	array_push($contact_details, $contact_detail);
 
	if(trim($form->home_phone != ''))
	{
		$contact_detail = new JRAPartyContactDetail();
		$contact_detail->primary = '';
		$contact_detail->contact_type = 'Phone';
		$contact_detail->value = $form->home_phone;
		array_push($contact_details, $contact_detail);
	}
 
	if(trim($form->mobile_phone != ''))
	{
		$contact_detail = new JRAPartyContactDetail();
		$contact_detail->primary = 'true';
		$contact_detail->contact_type = 'Mobile';
		$contact_detail->value = $form->mobile_phone;
		array_push($contact_details, $contact_detail);
	}
 
	$party->contact_detail_child = $contact_details;
 
	// Setup "Party > AVETMISS" Child Resource
	$avetmiss = new JRAPartyAvetmiss();
	$avetmiss->labour_force_status = $form->labour_force_status;
	$avetmiss->country_of_birth = $form->country_of_birth;
 
	/*
	 * 27.02.2020 - Removed as requested by Irene
	 * if($form->australian_citizen == 'Yes')
	 * {
	 * $avetmiss->nationality = 'Australia';
	 * $avetmiss->citizenship_status = $form->citizenship_status;
	 * }
	 * else
	 * {
	 * $avetmiss->nationality = $form->citizenship_other;
	 * }
	 */

	$avetmiss->indigenous_status = $form->indigenous_status;
	$avetmiss->main_language = $form->main_language;
	$avetmiss->spoken_english_proficiency = $form->spoken_english_proficiency;
	$avetmiss->at_school_flag = $form->at_school_flag;
  
	/*
	 * 2018.09.05 - Removed
	 * $avetmiss->school = $form->school;
	 */
  
	$avetmiss->highest_school_level = $form->highest_school_level;
	$avetmiss->year_highest_school_level = $form->year_highest_school_level;
	$avetmiss->disability_flag = $form->disability_flag;
	if($avetmiss->disability_flag == 'Yes') 
	{
  		$avetmiss->disability_types = $form->disability_types;
	}
	$avetmiss->prior_education_flag = $form->prior_education_flag;
	if($avetmiss->prior_education_flag == 'Yes')
	{
		$avetmiss->prior_educations = $form->prior_educations;
		$avetmiss->prior_education_qualification = $form->prior_education_qualification;
	}
	if($form->city_of_birth != '')
	{
		$avetmiss->town_of_birth = $form->city_of_birth;
	}
  
	$party->avetmiss_child = $avetmiss;
  
	// CRICOS
	$cricos = new JRAPartyCricos();
	$cricos->country_of_birth = $form->country_of_birth;
	$cricos->citizenship_status = $form->citizenship_status;
	$cricos->nationality = $form->australian_citizen == 'Yes' ? 'Australia' : $form->citizenship_other;
	$party->cricos_child = $cricos;
  
	// ADHOC FIELD
	/*
	$adhoc_fields = array();
	$adhoc_field = new JRAPartyAdhoc();
	$adhoc_field->name = 'How did you hear about us?';
	$adhoc_field->value = $form->how_did_you_hear;
	array_push($adhoc_fields, $adhoc_field);
	$party->adhoc_child = $adhoc_fields;
	*/
  
	if(UEE30820_DEBUG_MODE)
	{
  		echo "Party Variable: <br/>";
  		var_dump($party);
  		echo "<br/><br/>";
  	}
  
	// Check if the Party Exists
	if(UEE30820_DEBUG_MODE)
	{
  		echo "Check if Party exists<br/>";
		error_log("Check if Party exists<br/>");
	}
  
	$party_result = JRAPartyOperations::getJRAParty($party);
	$party_attributes = $party_result->attributes();
	$count =(int) $party_attributes['total'];
  
	if($count > 0)
	{
  		// Set Party ID
  		$party_id =(string) $party_result->party->{'party-identifier'};
  	
	  	// Check if the existing party already has a middle name specified
	  	// If so, make the middle name "blank" so it does not update(workaround)
  		if($party_result->party->{'middle-name'} != '')
  		{
  			$party->middle_name = '';
  		}
  	
  		$new_party = false;
  	
  		if(UEE30820_DEBUG_MODE)
  		{
  			echo "Party exists - Party ID: " . $party_id . "<br/><br/>";
  			error_log("Party exists - Party ID: " . $party_id . "<br/><br/>");
  			echo "Result: <br/>";
  			var_dump($party_result);
  			echo "<br/><br/>";
  		}
  	
		// Update Party
  		$update_party_xml = JRAPartyOperations::updateJRAPartyXML($party);
  	
  		if(UEE30820_DEBUG_MODE)
  		{
  			// echo "Update Party XML: <br/>";
	  		// var_dump($update_party_xml);
	  		// echo "<br/><br/>";
  		}
  	
  		$update_party_result = JRAPartyOperations::updateJRAParty($update_party_xml, $party_id);
  	
  		if(isset($update_party_result->{'party-identifier'} ))
  		{
  			if(UEE30820_DEBUG_MODE)
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
  		if(UEE30820_DEBUG_MODE)
  		{
  			echo "Party does not exist - Create New Party<br/>";
  			error_log("Party does not exist - Create New Party<br/>");
  		}
  	
	  	// Create Party
	  	$party_xml = JRAPartyOperations::createJRAPartyXML($party);
  		$party_result = JRAPartyOperations::createJRAParty($party_xml);
  		$new_party = true;
  	
  		if(isset($party_result->{'party-identifier'} ))
  		{
  			$party_id =(string ) $party_result->{'party-identifier'};
  			if(UEE30820_DEBUG_MODE)
  			{
  				echo "Party created - Party ID: " . $party_id . "<br/><br/>";
  				error_log("Party created - Party ID: " . $party_id . "<br/><br/>");
  			}
  		}
  		else
  		{
  			if(UEE30820_DEBUG_MODE)
  			{
  				echo "An error occurred while creating a Party<br/><br/>";
  				error_log("An error occurred while creating a Party<br/><br/>");
  				echo "Party XML: <br/><div>";
  				var_dump($party_xml);
  				echo "</div><br/><br/>Party Result: <br/>";
  				var_dump($party_result);
  				echo "<br/><br/>";
  			}
  			return false;
  		}
  	}
  
  	// 2024.11.06 - Removed Employer Details as requested by Lyn Wang
  	/*
	if(UEE30820_DEBUG_MODE)
	{
  		echo "Party ID: " . $party_id . "<br/>";
  		echo "Employer Party ID: " . $employer_party_id . "<br/>";
	}
	
  
  	// if(isset($party_id ) && isset($employer_party_id ))
  	*/
  	
	if(isset($party_id ))
	{
		
		// 2024.11.06 - Removed Employer Details as requested by Lyn Wang
		/*
  		// Create Employee Resource
  		if(UEE30820_DEBUG_MODE)
  		{
  			echo "Create an Employee Resource<br/>";
  			error_log("Create an Employee Resource<br/>");
  		}
  	
	  	// Setup the "Employee" Resource
	  	$employee = new JRAEmployee();
  		$employee->employer_party_identifier = $employer_party_id;
  		$employee->start_date = current_time('Y-m-d');
  	
  		if(isset($supervisor_party_id ))
  		{
  			$employee->supervisor_contact_id = $supervisor_party_id;
  		}
  	
	  	// Check to see if the "Employee" resource already exists
	  	if($new_employer || $new_party)
  		{
  			// Sets an empty array for employers(no need to retrieve employee resources
  			$employers = array();
  		}
  		else
  		{
  			// Retrieve all employers for this party
  			$employers = JRAEmployeeOperations::getJRAEmployee($employer_party_id);
  		
  			if(UEE30820_DEBUG_MODE)
  			{
  				echo "Retrieve all Employers for this Party ID: " . count($employers ) . "<br/>";
  				error_log("Retrieve all Employers for this Party ID: " . count($employers ) . "<br/>");
  			}
  		}
  		*/
  	 
		// Create Enrolment
		$enrolment = new JRAEnrolment();
		$enrolment->party_identifier = $party_id;
		$enrolment->course_number = $form->course_number;
		$enrolment->study_reason = $form->study_reason;
  	 
		// 2020.07.06 - Add Enrolment Status(manually set)
		$enrolment->enrolment_status = 'Application Pending';
  	 
		$enrolment->client_occupation_identifier = $form->occupation;
		$enrolment->client_industry_employment = $form->industry_employment;
  	 
		// NOTE: Victorian student number must be an 9 digit value
		$enrolment->victorian_student_number =($form->vsn != '' && strlen($form->vsn ) == 9) ? $form->vsn : '';
		$enrolment->unknown_victorian_student_number = $form->vsn != '' ? 'false' : 'true';
		$enrolment->previous_victorian_education_enrolment = $form->previous_victorian_education != '' ? $form->previous_victorian_education : '';
  	 
		// ADHOC FIELD
		$adhoc_fields = array();
		$adhoc_field = new JRAEnrolmentAdhoc();
		$adhoc_field->name = 'How did you hear about us?';
		$adhoc_field->value = $form->how_did_you_hear;
		array_push($adhoc_fields, $adhoc_field);
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

		if(UEE30820_DEBUG_MODE)
		{
			echo "Create Enrolment<br/>";
		}
  	 
		// Create Enrolment
		$enrolment_xml = JRAEnrolmentOperations::createJRAEnrolmentXML($enrolment);
  	 
		if(UEE30820_DEBUG_MODE)
		{
  	 		echo "Enrolment XML: <br/>";
  	 		var_dump($enrolment_xml);
  	 		echo "<br/><br/>";
  	 	}
  	 
		$enrolment_result = JRAEnrolmentOperations::createJRAEnrolment($enrolment_xml);
  	 
		if(isset($enrolment_result->{'enrolment-identifier'} ))
		{
  	 		$enrolment_id =(string ) $enrolment_result->{'enrolment-identifier'};
  	 		if(UEE30820_DEBUG_MODE)
  	 		{
  	 			echo "Enrolment created - Enrolment ID: " . $enrolment_id . "<br/><br/>";
			}
		}
		else
		{
  	 		if(UEE30820_DEBUG_MODE)
  	 		{
				echo "Error occurred while creating Enrolment<br/><br/>";
			}
		}
  	 
		if(isset($enrolment_id))
		{
			// Update the VSN if it was specified
			if($enrolment->victorian_student_number)
			{
				$update_enrolment_vsn_result = JRAEnrolmentOperations::updateJRAEnrolmentVSN($enrolment, $enrolment_id);
			}
  	 	
			// 2020.04.27 - Add Employee Enrolment
			// 2024.11.06 - Removed Employer Details as requested by Lyn Wang
			/*
			if(isset($employer_party_id))
			{
  	 			$employee_enrolment = new JRAEmployeeEnrolment();
				$employee_enrolment->id = $employer_id;
				$employee_enrolment->party_identifier = $employer_party_id;
				$employee_enrolment->name = $employer_trading_name;
  	 		
				// Create the Employee Enrolment XML
				// Updated: 24.05.2023
				$employee_enrolment_xml = JRAEmployeeEnrolmentOperations::createJRAEmployeeEnrolmentXML($employee_enrolment);
  	 		
				if(UEE30820_DEBUG_MODE)
				{
  	 				echo "Employee Enrolment XML: <br/><pre>";
  	 				var_dump($employee_enrolment_xml);
  	 				echo "</pre><br/><br/>";
  	 			}
  	 		
				// Create the Employee Enrolment
				// Updated: 24.05.2023 - API Endpoint changed 
				//$employee_enrolment_result = JRAEmployeeEnrolmentOperations::createJRAParty($enrolment_id, $employee_enrolment_xml);
  	 			$employee_enrolment_result = JRAEmployeeEnrolmentOperations::createJRAEmployeeEnrolment($enrolment_id, $employee_enrolment_xml);
				
				
				// Validate the Employee Enrolment
				if( isset($employee_enrolment_result->{'id'}))
				{
					if(UEE30820_DEBUG_MODE)
					{
						echo "Employee Enrolment created!<br/><pre>";
						var_dump($employee_enrolment_result);
						echo "</pre><br/><br/>";
					}
				}
				else
				{
	  	 			if(UEE30820_DEBUG_MODE)
	  	 			{
	  	 				echo "Error occurred while creating Employee Enrolment<br/><pre>";
	  	 				var_dump($employee_enrolment_result);
	  	 				echo "</pre><br/><br/>";
	  	 			}
				}
			}
			*/
			
		}
		// End of Create Enrolment
  	 
		// Create "Party Contact" Resource for Emergency Contact Person
		// Setup "Party Contact" Resource
		$party_contact = new JRAPartyContact();
		$party_contact->contact_method = 'Phone';
		$party_contact->first_name = $form->emergency_contact_firstname;
		$party_contact->surname = $form->emergency_contact_surname;
		$party_contact->phone = preg_replace('/\s/', '', $form->emergency_contact_number);
		$party_contact->email = $form->emergency_contact_email;
		$party_contact->relationship = $form->emergency_contact_relationship;

		// Check if "Party Contact" exists already
		$jra_party_contacts = JRAPartyContactOperations::getJRAPartyContacts($party_id);

		// Loops through all party contacts linked to the party_id
		foreach($jra_party_contacts as $jra_party_contact )
		{
			if($jra_party_contact->first_name == $party_contact->first_name && $jra_party_contact->surname == $party_contact->surname && $jra_party_contact->phone == $party_contact->phone)
			{
				$party_contact_id =(string ) $jra_party_contact->id;
				break;
			}
		}
		
		if(isset($party_contact_id))
		{
			if(UEE30820_DEBUG_MODE)
			{
				echo "Party Contact Exists<br/><br/>";
			}
		
			// Update Party Contact(email + phone number)
			$party_contact_update_result = JRAPartyContactOperations::updateJRAPartyContact($party_id, $party_contact_id, $party_contact);
		}
		else
		{
			if(UEE30820_DEBUG_MODE)
			{
				echo "Create Party Contact<br/>";
			}
  	 	
			$party_contact_xml = JRAPartyContactOperations::createJRAPartyContactXML($party_contact);

			if(UEE30820_DEBUG_MODE)
			{
				echo "Party Contact XML: <br/><pre>" . $party_contact_xml . "</pre><br/><br/>";
			}
  	 	
			$party_contact_result = JRAPartyContactOperations::createJRAPartyContact($party_id, $party_contact_xml);
  	 	
			if(isset($party_contact_result->id ))
			{
				if(UEE30820_DEBUG_MODE)
				{
					echo "Party Contact created<br/>Party Contact ID: " . $party_contact->id . "<br/><pre>";
					var_dump($party_contact_result);
					echo "</pre><br/><br/>";
				}
			}
			else
			{
				if(UEE30820_DEBUG_MODE)
				{
					echo "Error occured when creating Party Contact<br/><pre>";
					var_dump($party_contact_result);
					echo "</pre><br/><br/>";
				}
			}
		}

		// Create the SKILL FIRST Party Document
		/* 2023.01.16 - Removal request by Ashima Nakra (email)
		$sf_pdf_file = JR_ROOT_PATH . '/pdf/' . $sfp;
		$document = new JRAPartyDocument();
		$document->party_id = $party_id;
		$document->name = 'Skills First Program (' . $form->course_scope_code . ' - ' . $form->course_number . ')';
		$document->description = 'Skills First Program (' . $form->course_scope_code . ' - ' . $form->course_number . ')';
		$document->filename = $sf_pdf_file;
		
		$sf_party_document_result = JRAPartyDocumentOperations::createJRAPartyDocument($party_id, $document);
  	 
		if(isset($sf_party_document_result->id ))
		{
			if(UEE30820_DEBUG_MODE)
			{
				echo "Party Document Created (Skills First) - Party Document ID: " . $sf_party_document_result->id . "<br/>";
			}
  	 	
			// Remove PDF from server - if not debugging
			if(!UEE30820_DEBUG_MODE)
			{
				unlink($sf_pdf_file);
			}

			if(UEE30820_DEBUG_MODE)
			{
  	 			echo "PDF file removed from web server<br/><br/>";
			}
		}
		else
		{
			if(UEE30820_DEBUG_MODE)
			{
				echo "Error occurred when creating Party Document (Skills First)<br/>";
				echo "<pre>";
				var_dump($sf_party_document_result);
				echo "</pre>";
			}
		}
		*/
		
  	 
		// Create the PRE TRAINING REVIEW Party Document
		/* 2023.01.16 - Removal request by Ashima Nakra (email)
		$ptr_pdf_file = JR_ROOT_PATH . '/pdf/' . $ptrp;
		$document = new JRAPartyDocument();
		$document->party_id = $party_id;
		$document->name = 'Pre Training Review (' . $form->course_scope_code . ' - ' . $form->course_number . ')';
		$document->description = 'Pre Training Review (' . $form->course_scope_code . ' - ' . $form->course_number . ')';
		$document->filename = $ptr_pdf_file;
  	 
		$ptr_party_document_result = JRAPartyDocumentOperations::createJRAPartyDocument($party_id, $document);
  	 
		if(isset($ptr_party_document_result->id ))
		{
			if(UEE30820_DEBUG_MODE)
			{
				echo "Party Document Created (Pre-Training Review) - Party Document ID: " . $ptr_party_document_result->id . "<br/>";
			}
  	 	
			// Remove PDF from server - if not debugging
			if(!UEE30820_DEBUG_MODE)
			{
				unlink($ptr_pdf_file);
			}

			if(UEE30820_DEBUG_MODE)
			{
				echo "PDF file removed from web server<br/><br/>";
			}
		}
		else
		{
			if(UEE30820_DEBUG_MODE)
			{
				echo "Error occurred when creating Party Document (Pre-Training Review)<br/><pre>";
				var_dump($ptr_party_document_result);
				echo "</pre>";
			}
		}
		*/
		
		
		// Upload the CONCESSION CARD FILE Party Document
		// 2023.06.08 - Remove Concession Card Upload - Requested by Ashima
		/*
		if(trim($form->concession_card_file) != '')
		{
			$url_components = parse_url($form->concession_card_file);
			
			$cc_file = ABSPATH . $url_components['path'];
			$document = new JRAPartyDocument();
			$document->party_id = $party_id;
			$document->name = 'Concession Card - ' . $form->first_name . ' ' . $form->surname;
			$document->description = 'Concession Card';
			$document->document_category = 'Concession Card';
			$document->document_type = 'Resources';
			$document->filename = $cc_file;
			
			$cc_party_document_result = JRAPartyDocumentOperations::createJRAPartyDocument($party_id, $document);
			
			if(isset($cc_party_document_result->id ))
			{
				if(UEE30820_DEBUG_MODE)
				{
					echo "Party Document Created (Digital Concession Card) - Party Document ID: " . $cc_party_document_result->id . "<br/>";
				}
				
				// Remove the file from server - if not debugging
				if(!UEE30820_DEBUG_MODE)
				{
					unlink($cc_file);
				}
				
				if(UEE30820_DEBUG_MODE)
				{
					echo "File removed from web server<br/><br/>";
				}
			}
			else
			{
				if(UEE30820_DEBUG_MODE)
				{
					echo "Error occurred when creating Party Document (Digital Concession Card)<br/>";
					echo "<pre>";
					var_dump($cc_party_document_result);
					echo "</pre>";
				}
			}
		}
		*/
		
		
		
		// Upload the USI TRANSCRIPT FILE Party Document
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
				if(UEE30820_DEBUG_MODE)
				{
					echo "Party Document Created (USI Transcript) - Party Document ID: " . $usi_party_document_result->id . "<br/>";
				}
				
				// Remove the file from server - if not debugging
				if(!UEE30820_DEBUG_MODE)
				{
					unlink($usi_file);
				}
				
				if(UEE30820_DEBUG_MODE)
				{
					echo "File removed from web server<br/><br/>";
				}
			}
			else
			{
				if(UEE30820_DEBUG_MODE)
				{
					echo "Error occurred when creating Party Document (USI Transcript)<br/>";
					echo "<pre>";
					var_dump($usi_party_document_result);
					echo "</pre>";
				}
			}
		}
		
		
  	 
		// Create "PartyDocument" and link the Application Form PDF to the Party
		$pdf_file = JR_ROOT_PATH . '/pdf/' . $apprentice_pdf;
		$document = new JRAPartyDocument();
		$document->party_id = $party_id;
		$document->name = 'Apprentice Application Form (' . $form->course_scope_code . ' - ' . $form->course_number . ')';
		$document->description = 'Apprentice Application Form';
		$document->filename = $pdf_file;

		$party_document_result = JRAPartyDocumentOperations::createJRAPartyDocument($party_id, $document);
  	 
		if(isset($party_document_result->id ))
		{
			if(UEE30820_DEBUG_MODE)
			{
				echo "Party Document Created - Party Document ID: " . $party_document_result->id . "<br/>";
			}
  	 	
			if(!UEE30820_DEBUG_MODE)
			{
				// Remove PDF from server
				unlink($pdf_file);
			}

			if(UEE30820_DEBUG_MODE)
			{
				echo "PDF file removed from web server<br/><br/>";
			}

			// 2024.11.06 - Removed Employer Details as requested by Lyn Wang
			/*
			// Sync the Employers if a new employer was created
			if($form->employer_party_new)
			{
				JRAEmployerOperations::syncEmployers();
			}
			*/

			if(UEE30820_DEBUG_MODE)
			{
				echo "Exit before Couse Sync and Cleanup";
				exit;
			}
			
			// Check course enrolment availability and sync from Job Ready if less than 3 remaining
			check_course_date_and_sync($form->course_number);
  	 	
			// Define Gravity Form Linked Entry ID(from entry array)
			$gravity_form_linked_entry_id =(int) $form->gform_id;
  	 	
			// Set up a Gravity Form keep array(array of form field id's to be kept on website database)
			$gf_keep_array = array(81, 82, 125, 126, 9,
  	 								8, 21, 45, 46, 47,
  	 								105, 118, 119, 58, 59 );
  	 	
			// Gravity Form Party ID field #
			$gf_party_id_field = 125;
  	 	
			// 2024.11.06 - Removed Employer Details as requested by Lyn Wang
			/*
			// Gravity Form Employer Party ID field #
			$gf_employer_party_id_field = 126;
			*/
  	 	
			// Clean up Gravity Form variables unless DEBUG MODE activated
			if(UEE30820_DEBUG_MODE)
			{
				echo "Confidential Clean Up currently disabled<br/><br/>";
			}
			else
			{
				// 2024.11.06 - Removed Employer Details as requested by Lyn Wang
				//confidential_clean_up_gf($gravity_form_linked_entry_id, $gf_keep_array, $party_id, $gf_party_id_field, $employer_party_id, $gf_employer_party_id_field);
				confidential_clean_up_gf($gravity_form_linked_entry_id, $gf_keep_array, $party_id, $gf_party_id_field);
			}
		}
		else
		{
			error_log("Error occurred when creating Party Document (Apprentice Application Form)<br/><br/>");
			if(UEE30820_DEBUG_MODE)
			{
				echo "Error occurred when creating Party Document (Apprentice Application Form)<br/><pre>";
				var_dump($party_document_result);
				echo "</pre>";
			}
		}
	}
}



/* *********** *
 * PDF-RELATED *
 * *********** */



// Apprentice Application Form PDF
function uee30820_application_form_pdf($form)
{
	// Start
	$content = neca_pdf_content_start();
	
	// Add Heading
	$content .= neca_pdf_content_heading( 'Apprentice Application Form', $form );
	
	// Previously Enrolled
	// 2024.01.17 - Added Previously Enrolled - Requested by Lyn Wang
	// 2024.11.06 - Removed Previously Enrolled - Request by Lyn Wang
	//$content .= neca_pdf_previously_enrolled($form);
		
	// Personal Details Content
	$content .= neca_pdf_content_personal_details( $form );
	
	// Emergency Contanct Details Content
	$content .= neca_pdf_content_emergency_content_details( $form );
	
	// 2024.11.06 - Removed Employer Details as requested by Lyn Wang 
	// Employer Content
	//$content .= neca_pdf_content_employer_details( $form );
	
	// Page End
	$content .= neca_pdf_page_end();
	
	// Page Start
	$content .= neca_pdf_page_start();
	
	// Avetmiss Heading
	$content .= neca_pdf_content_avetmiss_heading( $form );
	
	// Nationality Details Content
	$content .= neca_pdf_content_language_details( $form, false );

	// Disability Details Content
	$content .= neca_pdf_content_disability_details( $form );

	// Prior Education Details Content
	$content .= neca_pdf_content_education_details( $form );
	
	// Credit Transfer Details
	$content .= neca_pdf_content_credit_transfer( $form );
	
	// Recognition of Prior Learning
	$content .= neca_pdf_content_recognition_of_prior_learning( $form );
	
	// Labour Force Details Content
	$content .= neca_pdf_content_labour_details( $form );

	// Referred from a Job Seeker
	$content .= neca_pdf_content_referred_details( $form );

	// Page End
	$content .= neca_pdf_page_end();
	
	// Page Start
	$content .= neca_pdf_page_start();
	
	// Enrolment Avetmiss Details Content
	$content .= neca_pdf_content_enrolment_avetmiss_details_uee30820( $form );
	
	// VSN Details
	$content .= neca_pdf_content_vsn( $form );
		
	// USI Details
	$content .= neca_pdf_content_usi( $form );
	
	// How did you hear Details Content
	$content .= neca_pdf_content_cohort($form);
	
	// Concession Details Content
	$content .= neca_pdf_content_concession_details( $form );
	
	// How did you hear Details Content
	$content .= neca_pdf_content_how_did_you_hear( $form );

	// Page End
	$content .= neca_pdf_page_end();
	
	// Page Start
	$content .= neca_pdf_page_start();
	
	// Privacy Policy - Updated on FEB 2023 (specifically for UEE30820)
	$content .= neca_pdf_content_privacy_policy_feb2023();
	
	// Page End
	$content .= neca_pdf_page_end ();
	
	// Page Start
	$content .= neca_pdf_page_start ();
	
	// Victorian Enrolment Privacy Notice - Updated on FEB 2023
	$content .= neca_pdf_content_victorian_enrolment_privacy_notice();
	
	// Page End
	$content .= neca_pdf_page_end ();
	
	// Page Start
	$content .= neca_pdf_page_start ();
	
	// All Students Must Read
	$content .= neca_pdf_content_all_students_must_read_sign_date_2019 ( $form );
	
	// Tickboxes - Update JAN 2023
	$content .= neca_pdf_content_tickboxes_uee30820( true, true );
	
	// Signatures
	$content .= neca_pdf_content_signatures_2019 ( $form );
	
	// Page End
	$content .= neca_pdf_page_end ();
	
	/* Requested to remove Page 7 Completely - Lyn Wang 15/06/2023
	// Page Start
	$content .= neca_pdf_page_start ();
	
	// Office Use Only
	$content .= neca_pdf_content_office_use_only_UEE30820 ( $form );
	
	// Sign Off
	$content .= neca_pdf_content_sign_off ();
	
	// Page End
	$content .= neca_pdf_page_end ();
	*/
	
	// Setup Filename
	$filename = urlencode ( $form->course_number . '_' . $form->first_name . '_' . $form->surname . '_' . current_time ( 'Ymd_hms' ) ) . '.pdf';
	
	// Create PDF
	neca_create_pdf ( $filename, $content );
	
	return $filename;
}


function neca_pdf_content_tickboxes_uee30820($tick_all = false)
{
	$content = '<table class="tbl">
					<tr>
						<td style="width: 5%;">';
	$content .= $tick_all ? '[YES]' : '[&nbsp;&nbsp;&nbsp;]';
	
	$content .= '		</td>
						<td style="width: 95%;">
							<p class="small">I have read and understand the Policy Guide and Guide to Fees, Payments, and Refunds</p>
						</td>
					</tr>
					<tr>
						<td style="width: 5%;">';
	$content .= $tick_all ? '[YES]' : '[&nbsp;&nbsp;&nbsp;]';
	
	$content .= '		</td>
						<td style="width: 95%;">
							<p class="small">I have read and understand the Student Manual and Statement of Fees</p>
						</td>
					</tr>
					<tr>
						<td style="width: 5%;">';
	$content .= $tick_all ? '[YES]' : '[&nbsp;&nbsp;&nbsp;]';
	
	$content .= '		</td>
						<td style="width: 95%;">
							<p class="small">I acknowledge that I have read the VET Data Privacy Notice and Enrolment Privacy Notice (Victoria)</p>
						</td>
					</tr>

					<tr>
						<td style="width: 5%;">';
	
	$content .= $tick_all ? '[YES]' : '[&nbsp;&nbsp;&nbsp;]';
	
	$content .= '		</td>
						<td style="width: 95%;">
							<p class="small">I have read and understood the USI Privacy Statement</p>
						</td>
					</tr>';
	
	$content .= '	<tr>
						<td style="width: 5%;">';
	
	$content .= $tick_all ? '[YES]' : '[&nbsp;&nbsp;&nbsp;]';
	
	$content .= '		</td>
						<td style="width: 95%;">
							<p class="small">I agree to receive communications from NECA Education and Careers</p>
						</td>
					</tr>';
	
	$content .= '	<tr>
						<td style="width: 5%;">';
	
	$content .= $tick_all ? '[YES]' : '[&nbsp;&nbsp;&nbsp;]';
	
	$content .= '		</td>
						<td style="width: 95%;">
							<p class="small"><strong>ENROLMENT DECLARATION - </strong>I declare that the information I have provided on this enrolment form is true and correct and I can produce documents to verify this if required. I hereby agree to abide by the Code of Conduct and the regulations of NECA Educations and Careers. I understand that if any of this information is found to be incorrect or untrue it may result in the terms and conditions of my enrolment being null and void.</p>
						</td>
					</tr>';
	
	$content .= '</table>';
	
	return $content;
}


function neca_pdf_content_office_use_only_UEE30820($form) {
	$content = '<div class="heading3">OFFICE USE ONLY</div>';
	
	$content .= '<table class="tbl">
					<tr>
						<td colspan="2" class="small">
							<strong>PROGRAM DETAILS (APPRENTICESHIP)</strong><br/>
							Course Scope Code: ' . $form->course_scope_code . '
						</td>
					</tr>
				</table>';
	
	$content .= '<table class="tbl">
					<tr>
						<td style="width: 25%;" class="small">Qualification: </td>
						<td style="width: 75%;" class="small">Certificate III in Electrotechnology Electrician</td>
					</tr>
					<tr>
						<td style="width: 25%;" class="small">Group Name: </td>
						<td style="width: 75%;" class="small">&nbsp;</td>
					</tr>
					<tr>
						<td style="width: 25%;" class="small">Start Date: </td>
						<td style="width: 75%;" class="small">&nbsp;</td>
					</tr>
					<tr>
						<td style="width: 25%;" class="small">End Date: </td>
						<td style="width: 75%;" class="small">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2" class="small"><strong>APPRENTICE DETAILS</strong></td>
					</tr>
					<tr>
						<td style="width: 25%;" class="small">Student Name: </td>
						<td style="width: 75%;" class="small">' . $form->first_name . ' ' . $form->surname. '</td>
					</tr>
					<tr>
						<td style="width: 25%;" class="small">TCID: </td>
						<td style="width: 75%;" class="small">&nbsp;</td>
					</tr>
					<tr>
						<td style="width: 25%;" class="small">Client Identifier: </td>
						<td style="width: 75%;" class="small">&nbsp;</td>
					</tr>
					<tr>
						<td style="width: 25%;" class="small">AASN: </td>
						<td style="width: 75%;" class="small">&nbsp;</td>
					</tr>
				</table>
				<br/>
				<table class="tbl">
					<tr>
						<td style="width: 5%;" class="small">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width: 45%;" class="small">Government subsidised: </td>
						<td style="width: 25%;" class="small">Rates per SCH: </td>
						<td style="width: 25%;" class="small">$5.00</td>
					</tr>
					<tr>
						<td style="width: 5%;" class="small">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width: 45%;" class="small">Government subsidised - concession: </td>
						<td style="width: 25%;" class="small">Rates per SCH: </td>
						<td style="width: 25%;" class="small">$1.00</td>
					</tr>
					<tr>
						<td style="width: 5%;" class="small">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width: 45%;" class="small">Resources & Amenities: </td>
						<td style="width: 25%;" class="small">Rates per SCH: </td>
						<td style="width: 25%;" class="small">$16.91</td>
					</tr>
				</table>';

	return $content;
}

// Enrolment Avetmiss Details
function neca_pdf_content_enrolment_avetmiss_details_uee30820($form)
{
	$content = '<div class="heading3">ENROLMENT AVETMISS DETAILS</div>
					<table class="tbl">';
	
	if ($form->occupation != '') 
	{
		$content .= '	<tr>
							<td style="width: 40%;">Which role best describes your current or recent occupation?</td>
							<td style="width: 60%;">' . $form->occupation . '</td>
						</tr>';
	}

	if ($form->industry_employment != '') {
		$content .= '	<tr>
							<td style="width: 40%;">Which classification best describes the industry of current or previous employer?</td>
							<td style="width: 60%;">' . $form->industry_employment . '</td>
						</tr>';
	}
	
	if($form->study_reason != '')
	{
		$content .= '	<tr>
							<td style="width: 40%;">Which best describes your main reason for undertaking the program / apprenticeship?</td>
							<td style="width: 60%;">' . $form->study_reason . '</td>
						</tr>';
	}
	
	$content .= ' 	</table>';
	
	return $content;
}