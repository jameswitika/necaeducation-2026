<?php
include("../wp-load.php");

if( isset($_GET['entry_id']) )
{
	$entry_id = $_GET['entry_id'];
	
	// Load the Entry and retrieve the Form Data
	$entry = GFAPI::get_entry( $entry_id );
	
	$form = new JobReadyForm();
	
	// Gravity Form
	$form->gform_id = $entry['id'];
	$form->gform_form_id = $entry['form_id'];
	
	// Course Details
	$form->course_scope_code = rgar($entry, '81');
	$form->course_number = rgar($entry, '82');
	
	// Personal Details
	$form->gender = rgar($entry, '69');
	$form->title = rgar($entry, '2');
	$form->first_name = ucwords(strtolower(rgar($entry, '9')));
	$form->middle_name = ucwords(strtolower(rgar($entry, '83')));
	$form->surname = ucwords(strtolower(rgar($entry, '8')));
	$form->known_by = ucwords(strtolower(rgar($entry, '10')));
	$form->birth_date = rgar($entry, '11');
	
	// Contact Details
	$form->home_phone = rgar($entry,'20');
	$form->mobile_phone = rgar($entry,'19');
	$form->email = strtolower(rgar($entry,'21'));
	
	// Address
	$form->street_address1 = ucwords(strtolower(rgar($entry, '97')));
	$form->suburb = ucwords(strtolower(rgar($entry, '98')));
	$form->state = ucwords(strtolower(rgar($entry, '99')));
	$form->postcode = rgar($entry, '100');
	
	$form->postal_address_same = rgar($entry, '87.1'); // 87.1 because its a checkbox
	
	if($form->postal_address_same != 'Yes')
	{
		$form->postal_street_address1 = ucwords(strtolower(rgar($entry, '101')));
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
	
	// Unique Student Number
	$form->usi_flag = "Yes";
	$form->usi_number = rgar($entry, 54);
	
	// Enrolment > Skills VIC AVETMISS
	$form->study_reason = rgar($entry, '68');
	$form->industry_employment = rgar($entry, '96');
	$form->occupation = rgar($entry, '66');
	
	// Concession Card
	$form->concession_flag = rgar($entry, '119');
	
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
	$form->concession_card_file = $entry['208'];
	$form->file_usi_transcript = $entry['210'];
	
	echo "Form Variable: <br/>";
	var_dump($form);
	echo "<br/><br/>";
	
	echo "Create PDF<br/><br/>";
	// Create PDF
	$uee30820_pdf = uee30820_application_form_pdf($form);
	echo '<a href="' . JR_ROOT_URL . '/pdf/' . $uee30820_pdf. '" target="_blank">UEE30820 Applcation Form(PDF)</a><br/><br/>';
}