<?php

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



// Electrical Apprenticeship Form (ELEC-APP)
add_action( "gform_pre_submission_" . APPRENTICE_APPLICATION_FORM, 'electrical_apprenticeship_form_presubmission');

function electrical_apprenticeship_form_presubmission()
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
	
	if(isset($_POST['input_101']))
		$prefill->postal_street_address1 = $_POST['input_101'];
	
	if(isset($_POST['input_102']))
		$prefill->postal_suburb = $_POST['input_102'];
	
	if(isset($_POST['input_103']))
		$prefill->postal_state = $_POST['input_103'];
	
	if(isset($_POST['input_104']))
		$prefill->postal_postcode = $_POST['input_104'];
	
	$prefill->emergency_contact_firstname = $_POST['input_71'];
	$prefill->emergency_contact_surname = $_POST['input_92'];
	$prefill->emergency_contact_number = $_POST['input_73'];

	if(isset($_POST['input_133']))
		$prefill->emergency_contact_email = $_POST['input_133'];
	
	$prefill->emergency_contact_relationship = $_POST['input_74'];
	$prefill->labour_force_status = $_POST['input_28'];
	
	// 11.02.2021 - Added as requested by Ranjita
	$prefill->referred = $_POST['input_200'];
	$prefill->referred_details = $_POST['input_201'];
	
	$prefill->country_of_birth = $_POST['input_93'];
	
	// 27.02.2020 - Removed as requested by Irene
	// $prefill->australian_citizen = $_POST['input_153'];
	// $prefill->citizenship_status = $_POST['input_154'];
	// $prefill->citizenship_other = $_POST['input_155'];
	
	$prefill->indigenous_status = $_POST['input_34'];
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
	$prefill->highest_school_level = $_POST['input_47'];

	
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
	
	// 2018.10.17 - Required for Skills First Program PDF
	if(isset($_POST['input_158']))
		$prefill->highest_qualification_completed = ucwords(strtolower($_POST['input_158']));
	
	if(isset($_POST['input_159']))
		$prefill->government_funded_enrolments_this_year = $_POST['input_159'];
	
	if(isset($_POST['input_160']))
		$prefill->government_funded_undertakings_at_present = $_POST['input_160'];
	
	if(isset($_POST['input_161']))
		$prefill->government_funded_in_lifetime = $_POST['input_161'];
	
	if(isset($_POST['input_163']))
		$prefill->enrolled_in_a_school = $_POST['input_163'];
	
	if(isset($_POST['input_164']))
		$prefill->enrolled_in_skills_for_education = $_POST['input_164'];
	
	if(isset($_POST['input_165']))
		$prefill->subsidized_acknowledgement = $_POST['input_165'];
	
	if(isset($_POST['input_166']))
		$prefill->contacted_by_department_acknowledgement = $_POST['input_166'];
	
	// 2018.10.17 - Required for the Pre-Training Review
	if(isset($_POST['input_170']))
		$prefill->expectation_outline_reviewed = $_POST['input_170'];
	
	if(isset($_POST['input_171']))
		$prefill->expectation_working_environment = $_POST['input_171'];
	
	if(isset($_POST['input_172']))
		$prefill->expectation_reason_and_outcome = ucwords(strtolower($_POST['input_172']));
	
	if(isset($_POST['input_173']))
		$prefill->expectation_knowledge_and_appeal = ucwords(strtolower($_POST['input_173']));
	
	if(isset($_POST['input_174']))
		$prefill->expectation_why_neca = ucwords(strtolower($_POST['input_174']));
	
	if(isset($_POST['input_176']))
		$prefill->ple_difficulties_flag = $_POST['input_176'];
	
	if(isset($_POST['input_177']))
		$prefill->ple_difficulties = ucwords(strtolower($_POST['input_177']));
	
	if(isset($_POST['input_178']))
		$prefill->ple_concerns = ucwords(strtolower($_POST['input_178']));
	
	if(isset($_POST['input_187']))
		$prefill->learning_style = $_POST['input_187'];
	
	if(isset($_POST['input_188']))
		$prefill->learning_preference = $_POST['input_188'];
	
	if(isset($_POST['input_182']))
		$prefill->reading_skills = $_POST['input_182'];
	
	if(isset($_POST['input_183']))
		$prefill->writing_skills = $_POST['input_183'];
	
	if(isset($_POST['input_184']))
		$prefill->numeracy_skills = $_POST['input_184'];
	
	if(isset($_POST['input_185']))
		$prefill->computer_skills = $_POST['input_185'];
		
	$_SESSION['prefill'] = $prefill;
	
	if(JR_DEBUG_MODE)
	{
		echo "Prefill set: <br/>";
		var_dump($_SESSION['prefill']);
		echo "<br/><br/>";
	}
}



// Project Management Form (PM-APP)
add_action( "gform_pre_submission_" . PROJECT_MANAGEMENT_APPLICATION_FORM, 'project_management_form_presubmission');

function project_management_form_presubmission()
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
	$prefill->indigenous_status = $_POST['input_34'];
	
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
	$prefill->concession_flag = $_POST['input_119'];
	$prefill->how_did_you_hear = $_POST['input_67'];
	
	$prefill->previous_victorian_education = $_POST['input_57'];
	$prefill->vsn = isset($_POST['input_56']) ? $_POST['input_56'] : '';
	$prefill->previous_victorian_school = isset($_POST['input_58']) ? $_POST['input_58'] : '';
	$prefill->previous_victorian_training = isset($_POST['input_59']) ? $_POST['input_59'] : '';
	
	// 2020.03.04 - Required for Skills First Program PDF
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
	
	$_SESSION['prefill'] = $prefill;
	
	if(JR_DEBUG_MODE)
	{
		echo "Prefill set: <br/>";
		var_dump($_SESSION['prefill']);
		echo "<br/><br/>";
	}
}



// Pre-Apprentice Application Form is the same thing as Non-Apprentice Application Form (PRE-APP)
add_filter("gform_pre_submission_" . NON_APPRENTICE_APPLICATION_FORM, 'non_apprentice_application_form_presubmission');

function non_apprentice_application_form_presubmission()
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
	
	$prefill->title = $_POST['input_2'];
	$prefill->first_name = $_POST['input_9'];
	$prefill->middle_name = $_POST['input_81'];
	$prefill->surname = $_POST['input_8'];
	$prefill->known_by = $_POST['input_10'];
	$prefill->gender = $_POST['input_69'];
	$prefill->birth_date = $_POST['input_11'];
	$prefill->home_phone = $_POST['input_20'];
	$prefill->mobile_phone = $_POST['input_19'];
	$prefill->email = $_POST['input_21'];

	$prefill->street_address1 = $_POST['input_104'];
	$prefill->suburb = $_POST['input_105'];
	$prefill->state = $_POST['input_106'];
	$prefill->postcode = $_POST['input_107'];

	$prefill->postal_address_same = isset($_POST['input_82_1']) ? "Yes" : "";
	$prefill->postal_street_address1 = $_POST['input_108'];
	$prefill->postal_suburb = $_POST['input_109'];
	$prefill->postal_state = $_POST['input_110'];
	$prefill->postal_postcode = $_POST['input_111'];

	$prefill->emergency_contact_firstname = $_POST['input_72'];
	$prefill->emergency_contact_surname = $_POST['input_71'];
	$prefill->emergency_contact_number = $_POST['input_73'];
	$prefill->emergency_contact_email = $_POST['input_129'];
	$prefill->emergency_contact_relationship = $_POST['input_74'];

	$prefill->labour_force_status = $_POST['input_28'];
	
	$prefill->referred = $_POST['input_118'];
	$prefill->referred_details = $_POST['input_119'];
	
	$prefill->country_of_birth = $_POST['input_112'];
	$prefill->indigenous_status = $_POST['input_34'];
	$prefill->main_language = ($_POST['input_38'] == "No, English only") ? 'English' : $_POST['input_101'];

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
	$prefill->credit_transfer = $_POST['input_167'];
	$prefill->rpl = $_POST['input_169'];	
	
	$prefill->usi_number = $_POST['input_54'];

	$prefill->study_reason = $_POST['input_68'];
	$prefill->industry_employment = $_POST['input_116'];
	$prefill->occupation = $_POST['input_66'];
	$prefill->concession_flag = $_POST['input_117'];
	$prefill->how_did_you_hear = $_POST['input_67'];

	$prefill->previous_victorian_education = $_POST['input_57'];
	$prefill->vsn = $_POST['input_56'];
	$prefill->previous_victorian_school = $_POST['input_58'];
	$prefill->previous_victorian_training = $_POST['input_59'];
	
	// 2021.03.15 - Required for Skills First Program PDF
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
	
	$_SESSION['prefill'] = $prefill;
	
	if(JR_DEBUG_MODE)
	{
		echo "Prefill set: <br/>";
		var_dump($_SESSION['prefill']);
		echo "<br/><br/>";
	}
}

// Short Course (Accredited) Application Form (ASC)
add_filter("gform_pre_submission_" . SHORT_COURSE_APPLICATION_FORM_ACCREDITED, 'short_course_accredited_application_form_presubmission');

function short_course_accredited_application_form_presubmission()
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
	
	$prefill->street_address1 = $_POST['input_101'];
	$prefill->suburb = $_POST['input_102'];
	$prefill->state = $_POST['input_103'];
	$prefill->postcode = $_POST['input_104'];
	$prefill->postal_address_same = isset($_POST['input_75_1']) ? "Yes" : "";
	$prefill->postal_street_address1 = $_POST['input_105'];
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
	
	if(JR_DEBUG_MODE)
	{
		echo "Prefill set: <br/>";
		var_dump($_SESSION['prefill']);
		echo "<br/><br/>";
	}
}



// Short Course (NECCLV004) Application Form (ASC)
add_filter("gform_pre_submission_" . SHORT_COURSE_APPLICATION_NECCLV001, 'short_course_accredited_necclv_form_presubmission');

// Short Course (NECCLV004) Application Form (ASC)
add_filter("gform_pre_submission_" . SHORT_COURSE_APPLICATION_NECCLV004, 'short_course_accredited_necclv_form_presubmission');

function short_course_accredited_necclv_form_presubmission()
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
	
	$prefill->street_address1 = $_POST['input_101'];
	$prefill->suburb = $_POST['input_102'];
	$prefill->state = $_POST['input_103'];
	$prefill->postcode = $_POST['input_104'];
	$prefill->postal_address_same = isset($_POST['input_75_1']) ? "Yes" : "";
	$prefill->postal_street_address1 = $_POST['input_105'];
	$prefill->postal_suburb = $_POST['input_106'];
	$prefill->postal_state = $_POST['input_107'];
	$prefill->postal_postcode = $_POST['input_108'];
	
	$prefill->emergency_contact_firstname = $_POST['input_91'];
	$prefill->emergency_contact_surname = $_POST['input_92'];
	$prefill->emergency_contact_number = $_POST['input_93'];
	$prefill->emergency_contact_email = $_POST['input_121'];
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
	
	// 02.10.2020 - Industry and Occupation added
	$prefill->industry_employment = $_POST['input_128'];
	$prefill->occupation = $_POST['input_129'];
		
	$prefill->study_reason = $_POST['input_68'];
	$prefill->how_did_you_hear = $_POST['input_67'];
	$prefill->language_literacy_numeracy = $_POST['input_126'];
	
	$_SESSION['prefill'] = $prefill;
	
	if(JR_DEBUG_MODE)
	{
		echo "Prefill set: <br/>";
		var_dump($_SESSION['prefill']);
		echo "<br/><br/>";
	}
}



// Short Course (Non-Accredited) Application Form (NASC)
add_filter("gform_pre_submission_" . SHORT_COURSE_APPLICATION_FORM_NON_ACCREDITED, 'short_course_non_accredited_application_form_presubmission');

function short_course_non_accredited_application_form_presubmission()
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
	
	$prefill->title = $_POST['input_2'];
	$prefill->first_name = $_POST['input_9'];
	$prefill->middle_name= $_POST['input_28'];
	$prefill->surname = $_POST['input_8'];
	$prefill->known_by= $_POST['input_10'];
	$prefill->gender= $_POST['input_27'];
	$prefill->birth_date = $_POST['input_11'];
	$prefill->home_phone = $_POST['input_20'];
	$prefill->mobile_phone = $_POST['input_19'];
	$prefill->email = $_POST['input_21'];
	$prefill->street_address1 = $_POST['input_30'];
	$prefill->suburb = $_POST['input_31'];
	$prefill->state = $_POST['input_32'];
	$prefill->postcode = $_POST['input_33'];
	$prefill->postal_address_same = isset($_POST['input_43_1']) ? "Yes" : "";
	$prefill->postal_street_address1 = $_POST['input_44'];
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
	
	if(JR_DEBUG_MODE)
	{
		echo "Prefill set: <br/>";
		var_dump($_SESSION['prefill']);
		echo "<br/><br/>";
	}
}