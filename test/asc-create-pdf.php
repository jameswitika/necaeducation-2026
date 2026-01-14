<?php
include("../wp-load.php");

if( isset($_GET['entry_id']) )
{
	$entry_id = $_GET['entry_id'];

	// Load the Entry and retrieve the Form Data
	$entry = GFAPI::get_entry( $entry_id );
	
	$form = new JobReadyForm ();
	
	// Course Details
	$form->course_scope_code = rgar($entry, '69');
	$form->course_number = rgar($entry, '70');
	$form->invoice_option = rgar($entry, '72');
	$form->cost = $item_cost;
	
	// Personal Details
	$form->gender = rgar($entry, '73');
	$form->title = rgar($entry, '2');
	$form->first_name = ucwords ( strtolower ( rgar($entry, '9') ) );
	$form->middle_name = ucwords ( strtolower ( rgar($entry, '74') ) );
	$form->surname = ucwords ( strtolower ( rgar($entry, '8') ) );
	$form->known_by = ucwords ( strtolower ( rgar($entry, '10') ) );
	$form->birth_date = rgar($entry, '11');
	
	// Contact Details
	$form->home_phone = rgar($entry, '20');
	$form->mobile_phone = rgar($entry, '19');
	$form->email = strtolower ( rgar($entry, '21') );
	
	// Address
	$form->street_address1 = ucwords ( strtolower ( rgar($entry, '101') ) );
	$form->suburb = ucwords ( strtolower ( rgar($entry, '102') ) );
	$form->state = ucwords ( strtolower ( rgar($entry, '103') ) );
	$form->postcode = rgar($entry, '104');
	
	$form->postal_address_same = rgar($entry, '75.1');
	
	if ($form->postal_address_same != 'Yes')
	{
		// Address
		$form->postal_street_address1 = ucwords ( strtolower ( rgar($entry, '105') ) );
		$form->postal_suburb = ucwords ( strtolower ( rgar($entry, '106') ) );
		$form->postal_state = ucwords ( strtolower ( rgar($entry, '107') ) );
		$form->postal_postcode = rgar($entry, '108');
	}
	
	// Emergency Contact
	$form->emergency_contact_firstname = ucwords ( strtolower ( rgar($entry, '91') ) );
	$form->emergency_contact_surname = ucwords ( strtolower ( rgar($entry, '92') ) );
	$form->emergency_contact_number = rgar($entry, '93');
	$form->emergency_contact_relationship = ucwords ( strtolower ( rgar($entry, '94') ) );
	
	// Labour Force
	$form->labour_force_status = rgar($entry, '28');
	
	// Referred from a Job Seeker
	// $form->referred = rgar($entry, '95');
	// $form->referred_details = rgar($entry, '96');
	
	// Birth + Nationality + Indigenous + Language
	$form->country_of_birth = rgar($entry, '87');
	$form->indigenous_status = rgar($entry, '34');
	$language_other_than_english = rgar($entry, '38');
	
	if ($language_other_than_english != 'Yes')
	{
		$form->main_language = "English";
		// $form->spoken_english_proficiency= "Very Well";
	}
	else
	{
		$form->main_language = rgar($entry, '89');
		// $form->spoken_english_proficiency= "Very Well";
	}
	
	// School Details
	$form->at_school_flag = rgar($entry, '45');
	$form->highest_school_level = rgar($entry, '47');
	
	// Disability
	$form->disability_types = array ();
	$form->disability_flag = rgar($entry, '42');
	if ($form->disability_flag == 'Yes')
	{
		// Array fields are passed in as 43.1, 43.2, 43.3.... so we have iterate through them
		for($i = 1; $i < 10; $i ++)
		{
			$ref = '43.' . $i;
			if (rgar($entry, $ref) != '')
			{
				$form->disability_types [] = rgar($entry, $ref);
			}
		}
		if ($form->disabilities_other = rgar($entry, '44') != '')
		{
			$form->disabilities_other = ucwords ( strtolower ( rgar($entry, '44') ) );
		}
	}
	
	// Prior Education
	$form->prior_education_flag = rgar($entry, '50');
	$form->prior_educations = array ();
	if ($form->prior_education_flag == 'Yes')
	{
		// Array fields are passed in as 51.1, 51.2, 51.3.... so we have iterate through them
		for($i = 1; $i <= 20; $i ++)
		{
			$ref = '51.' . $i;
			if (rgar($entry, $ref) != '')
			{
				$form->prior_educations [] = rgar($entry, $ref);
			}
		}
		$form->prior_education_qualification = rgar($entry, '125');
	}
	
	// 2021.02.02 - Credit Transfer Added
	$form->credit_transfer = rgar($entry, '132');
	
	// Unique Student Number
	$form->usi_number = rgar($entry, '54');
	
	// 31.08.2020 - Added Industry Employment + Occupation
	$form->industry_employment = rgar($entry, '128');
	$form->occupation = rgar($entry, '129');
	
	
	// Enrolment > Skills VIC AVETMISS
	$form->study_reason = rgar($entry, '68');
	$form->how_did_you_hear = ucwords ( strtolower ( rgar($entry, '67') ) );
	
	// Language, Literacy or Numeracy
	$form->language_literacy_numeracy = rgar($entry, '126');
	
	// Student Declaraction
	$form->prerequisite_declaration = rgar($entry, '86.1');
	$form->privacy_declaration = rgar($entry, '60.1');
	
	$scaafp = short_course_accredited_application_form_pdf($form);
	echo '<a href="' . JR_ROOT_URL . '/pdf/' . $scaafp. '" target="_blank">Short Course Accredited Applcation Form (PDF)<a/><br/><br/>';
}