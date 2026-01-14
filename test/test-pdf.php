<?php
include("../wp-load.php");

$elec_app = true;
$pre_app = false;
$pm_app = false;
$asc = true;
$nasc = false;

$form = new JobReadyForm();
$form->course_scope_code = 'NECOPF001';
$form->course_number = 'CSENEC01230A';
$form->gender = 'Male';
$form->title = 'Mr';
$form->first_name = 'James';
$form->middle_name = 'Cameron';
$form->surname = 'Witika';
$form->known_by = 'James Witika';
$form->birth_date = '05/05/1978';
$form->street_address1 = '41 Greenside Circuit';
$form->suburb = 'Sandhurst';
$form->state = 'Victoria';
$form->country = 'Australia';
$form->postcode = '3933';
$form->home_phone = '(03) 9700 1440';
$form->mobile_phone = '0430 483 666';
$form->email = 'james@jameswitika.com';
$form->postal_address = '1 Ardea Court';
$form->postal_suburb = 'Endeavour Hills';
$form->postal_state = 'Victoria';
$form->postal_postcode = '3802';
$form->emergency_contact_firstname = 'Helen';
$form->emergency_contact_surname = 'Witika';
$form->emergency_contact_number = '03 9700 1440';
$form->emergency_contact_relationship = 'Mother';
//$form->emergency_contact_email = 'test@test.com.au';
$form->labour_force_status = 'Self-employed - not employing others';
$form->country_of_birth = 'Australian';
//$form->citizenship_status = 'Australian Citizenship';
$form->indigenous_status = 'No';
$form->main_language = 'English';
$form->spoken_english_proficiency = 'Very Well';
$form->at_school_flag = 'Yes';
//$form->school = 'Doveton Secondary College';
$form->highest_school_level = 'Year 12';
$form->concession_flag = 'Yes';
//$form->year_highest_school_level = '1995';
$form->prior_education_flag = 'Yes';
$form->prior_educations = array( 'Certificate I', 'Certificate II', 'Certificate III', 'Associate Diploma' );
//$form->prior_education_qualification = 'Australian';
$form->usi_number = '123456789';

if($elec_app || $pre_app || $pm_app)
{
	$form->vsn = 'ABC123456';
	$form->previous_victorian_education = 'No - I have not attended a Victorian school since 2009 or a TAFE or other VET training provider since the beginning of 2011';
	$form->previous_victorian_education = 'Yes - I have attended a Victorian schol since 2009.';
}

$form->previous_victorian_education = 'Yes - I have participated in training in a TAFE or other training organisation since the beginning of 2011.';
$form->previous_victorian_school = 'Doveton Secondary College';
$form->disability_flag = 'Yes';
$form->disability_types = array('hearing/deaf', 'blind', 'other');
$form->study_reason = 'To get a job';

if($elec_app || $pre_app || $asc || $pm_app)
{
	$form->industry_employment = 'Other services';
	$form->occupation = 'Manager';
}
$form->how_did_you_hear = 'Website';
$form->declaration_a = 'I AM';
$form->declaration_b = 'I AM NOT';
$form->declaration_c = 'Yes';
$form->declaration_d = 'Yes';
$form->prerequisite_declaration = 'Yes';
$form->privacy_declaration = 'Yes';
$form->declaration_full_name = 'James Cameron Witika';
$form->declaration_date = '18/07/2017';

// Used for PRE-APP
if($elec_app || $pre_app || $pm_app)
{
	$form->employer_party_new = 'Yes';
	$form->employer_company = 'Test Company';
	$form->employer_address = '123 Test Street';
	$form->employer_suburb = 'Melbourne';
	$form->employer_state = 'Victoria';
	$form->employer_postcode = '3000';
	$form->employer_office_phone = '123456789';
	$form->employer_supervisor_firstname = 'John';
	$form->employer_supervisor_surname = 'Smith';
	$form->employer_supervisor_phone = '123456789';
	$form->employer_supervisor_email = 'john.smith@work.com';
	$form->employer_paying_invoice = 'Yes';
	
	$form->signature = 'James Cameron Witika';
}

$form->credit_transfer = 'Yes';
$form->rpl = 'Yes';


if($nasc)
{
	$scnaafp = short_course_non_accredited_application_form_pdf($form);
	echo '<a href="' . JR_ROOT_URL . '/pdf/' . $scnaafp. '" target="_blank">Short Course Non-Accredited Applcation Form (PDF)<a/><br/><br/>';
}

if($asc)
{
	$scaafp = short_course_accredited_application_form_pdf($form);
	echo '<a href="' . JR_ROOT_URL . '/pdf/' . $scaafp. '" target="_blank">Short Course Accredited Applcation Form (PDF)<a/><br/><br/>';
}

if($elec_app)
{
	$aafp = apprentice_application_form_pdf($form);
	echo '<a href="' . JR_ROOT_URL . '/pdf/' . $aafp. '" target="_blank">Apprentice Applcation Form (PDF)<a/><br/><br/>';
}

if($pre_app)
{
	$naafp = non_apprentice_application_form_pdf($form);
	echo '<a href="' . JR_ROOT_URL . '/pdf/' . $naafp. '" target="_blank">Non-Apprentice Applcation Form (PDF)<a/><br/><br/>';
}

if($pm_app)
{
	$pmafp = project_management_application_form_pdf($form);
	echo '<a href="' . JR_ROOT_URL . '/pdf/' . $pmafp. '" target="_blank">Project Management Applcation Form (PDF)<a/><br/><br/>';
}