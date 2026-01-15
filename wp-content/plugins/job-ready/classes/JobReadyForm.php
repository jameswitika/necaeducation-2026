<?php
/*
 * JobReadyForm
 * Created by: James Witika
 * Company: Smooth Developments
 */

if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class JobReadyForm
{
	var $gform_id;
	var $course_scope_code;
	var $course_number;
	var $invoice_option;
	var $cost;
	var $previously_enrolled_at_neca;
	var $gender;
	var $title;
	var $first_name;
	var $middle_name;
	var $surname;
	var $known_by;
	var $neca_member;
	var $birth_date;
	var $street_address1;
	var $street_number;
	var $street_name;
	var $suburb;
	var $state;
	var $country;
	var $postcode;
	var $postal_address_same;
	var $postal_street_address1;
	var $postal_street_number;
	var $postal_street_name;
	var $postal_suburb;
	var $postal_state;
	var $postal_postcode;
	var $postal_country;
	var $home_phone;
	var $mobile_phone;
	var $email;

	// Added 29.12.2022 for CPD Application 2023
	var $license_type;
	var $license_classes;
	
	// Added 09.06.2022 for CPD Application
	var $license_number;
	var $license_renewal_date;
	var $rec_flag;
	var $company_employer_name;
	
	var $emergency_contact_firstname;
	var $emergency_contact_surname;
	var $emergency_contact_number;
	var $emergency_contact_email;
	var $emergency_contact_relationship;
	var $labour_force_status;
	var $referred;
	var $referred_details;
	var $country_of_birth;
	var $australian_citizen;
	var $citizenship_status;
	var $citizenship_other;
	var $indigenous_status;
	var $main_language;
	var $spoken_english_proficiency;
	var $at_school_flag;
	var $school;
	var $highest_school_level;
	var $year_highest_school_level;
	var $prior_education_flag;
	var $prior_educations;
	var $prior_education_qualification;
	var $employer_party_id;
	var $employer_party_new;
	var $employer_company;
	var $employer_address;
	var $employer_suburb;
	var $employer_state;
	var $employer_postcode;
	var $employer_office_phone;
	var $employer_supervisor_firstname;
	var $employer_supervisor_surname;
	var $employer_supervisor_phone;
	var $employer_supervisor_email;
	var $employer_paying_invoice;
	var $usi_flag;
	var $usi_number;
    var $file_usi_transcript;
	var $city_of_birth;
	var $vsn;
	var $previous_victorian_education;
	var $previous_victorian_school;
	var $credit_transfer;
	var $language_literacy_numeracy;
	var $disability_flag;
	var $disability_types;
	var $disability_other;
	var $study_reason;
	var $industry_employment;
	var $occupation;
	var $concession_flag;
    var $cohorts;
	var $how_did_you_hear;
	
	// 2018.10.17 - Required for Skills First Program PDF
	var $highest_qualification_completed;
	var $government_funded_enrolments_this_year;
	var $government_funded_undertakings_at_present;
	var $government_funded_in_lifetime;
	// 2021.03.19 - Additional Skills First Program fields specifically for Pre Apprentice	
	var $jobtrainer;
	var $jobtrainer_previously_started;
	var $jobtrainer_recommence;
	var $jobtrainer_17_to_24;
	var $jobtrainer_job_seeker;
	var $jobtrainer_applicable;
	var $jobtrainer_declaration;
	// 2018.10.17 - Continued
	var $enrolled_in_a_school;
	var $enrolled_in_skills_for_education;
	var $subsidized_acknowledgement;
	var $contacted_by_department_acknowledgement;
	
	// 2018.10.17 - Required for the Pre-Training Review
	var $expectation_outline_reviewed;
	var $expectation_working_environment;
	var $expectation_reason_and_outcome;
	var $expectation_knowledge_and_appeal;
	var $expectation_why_neca;
	var $ple_difficulties_flag;
	var $ple_difficulties;
	var $ple_concerns;
	var $rpl;
	var $learning_style;
	var $learning_preference;
	
	// 2021.07.22 - Apprenticeship Pre-Training
	var $reading_skills; // Obsolete 
	var $writing_skills; // Obsolete
	var $numeracy_skills; // Obsolete
	var $computer_skills; // Obsolete

	var $computer_access_internet;
	var $computer_usage;
	var $computer_turn_on;
	var $computer_email;
	var $computer_website;
	var $computer_search;
	var $computer_attach_email;
	var $computer_online_system;
	
	
	var $declaration_a;
	var $declaration_b;
	var $declaration_c;
	var $declaration_d;
	var $privacy_declaration;
	var $prerequisite_declaration;
	var $declaration_full_name;
	var $declaration_date;
	var $signature;
	
	function __construct()
	{
		$this->gform_id = 0;
		$this->course_scope_code = '';
		$this->course_number = '';
		$this->invoice_option = '';
		$this->cost = '';
		$this->previously_enrolled_at_neca = '';
		$this->gender = '';
		$this->title = '';
		$this->first_name = '';
		$this->middle_name = '';
		$this->surname = '';
		$this->known_by = '';
		$this->neca_member = 'false';
		$this->birth_date = '';
		$this->street_address1 = '';
		$this->street_number = '';
		$this->street_name = '';
		$this->suburb = '';
		$this->state = '';
		$this->country = 'Australia';
		$this->postcode = '';
		$this->postal_address_same = 'Y';
		$this->postal_street_address1 = '';
		$this->postal_street_number = '';
		$this->postal_street_name = '';
		$this->postal_suburb = '';
		$this->postal_state = '';
		$this->postal_country = 'Australia';
		$this->postal_postcode = '';
		$this->home_phone = '';
		$this->mobile_phone = '';
		$this->email = '';
		
		// Added 29.12.2022 for CPD Application
		$this->license_type = '';
		$this->license_classes = '';
		
		// Added 09.06.2022 for CPD Application
		$this->license_number = '';
		$this->license_renewal_date = '';
		$this->rec_flag = '';
		$this->company_employer_name = '';
		
		$this->emergency_contact_firstname = '';
		$this->emergency_contact_surname = '';
		$this->emergency_contact_number = '';
		$this->emergency_contact_email = '';
		$this->emergency_contact_relationship = '';
		$this->labour_force_status = '';
		$this->referred = 'No';
		$this->referred_details = '';
		$this->country_of_birth = '';
		$this->australian_citizen = '';
		$this->citizenship_status = '';
		$this->citizenship_other = '';
		$this->indigenous_status = '';
		$this->main_language = '';
		$this->spoken_english_proficiency = '';
		$this->at_school_flag = '';
		$this->school = '';
		$this->highest_school_level = '';
		$this->year_highest_school_level = '';
		$this->prior_education_flag = '';
		$this->prior_educations = array();
		$this->prior_education_qualification = '';
		$this->employer_party_id = '';
		$this->employer_party_new = true;
		$this->employer_company = '';
		$this->employer_address = '';
		$this->employer_suburb = '';
		$this->employer_state = '';
		$this->employer_postcode = '';
		$this->employer_office_phone = '';
		$this->employer_supervisor_firstname = '';
		$this->employer_supervisor_surname = '';
		$this->employer_supervisor_phone = '';
		$this->employer_supervisor_email = '';
		$this->employer_paying_invoice = '';
		$this->usi_flag = '';
		$this->usi_number = '';
        $this->file_usi_transcript = '';
		$this->city_of_birth = '';
		$this->vsn = '';
		$this->previous_victorian_education = '';
		$this->previous_victorian_school = '';
		$this->credit_transfer = '';
		$this->language_literacy_numeracy = '';
		$this->disability_flag = '';
		$this->disability_types = array();
		$this->disability_other = '';
		$this->study_reason = '';
		$this->industry_employment = '';
		$this->occupation = '';
		$this->concession_flag = '';
		$this->cohorts = array();
		$this->how_did_you_hear = '';
		
		// 2018.10.17 - Required for Skills First Program PDF
		$this->highest_qualification_completed = '';
		$this->government_funded_enrolments_this_year = '';
		$this->government_funded_undertakings_at_present = '';
		$this->government_funded_in_lifetime = '';
		$this->jobtrainer = '';
		$this->jobtrainer_previously_started = '';
		$this->jobtrainer_recommence = '';
		$this->jobtrainer_17_to_24 = '';
		$this->jobtrainer_job_seeker = '';
		$this->jobtrainer_applicable = '';
		$this->jobtrainer_declaration = '';
		$this->enrolled_in_a_school = '';
		$this->enrolled_in_skills_for_education = '';
		$this->subsidized_acknowledgement = '';
		$this->contacted_by_department_acknowledgement = '';
		
		// 2018.10.17 - Required for the Pre-Training Review
		$this->expectation_outline_reviewed = '';
		$this->expectation_working_environment = '';
		$this->expectation_reason_and_outcome = '';
		$this->expectation_knowledge_and_appeal = '';
		$this->expectation_why_neca = '';
		$this->ple_difficulties_flag = '';
		$this->ple_difficulties = '';
		$this->ple_concerns = '';
		$this->rpl = '';
		$this->learning_style = array();
		$this->learning_preference = array();
		$this->reading_skills = '';
		$this->writing_skills = '';
		$this->numeracy_skills = '';
		$this->computer_skills = '';
		$this->declaration_a = '';
		$this->declaration_b = '';
		$this->declaration_c = '';
		$this->declaration_d = '';
		$this->privacy_declaration = '';
		$this->prerequisite_declaration = '';
		$this->declaration_full_name = '';
		$this->declaration_date = '';
		$this->signature = '';
	}
}

class JobReadyFormOperations
{
	function __construct()
	{
		
	}
	
	static function convertToJobReadyForm($party_xml_object)
	{
// 		echo "<strong>Party: </strong><br/>";
// 		var_dump($party_xml_object->{'party'});
// 		echo "<br/><br/>";
		
		$form_fields = new JobReadyForm();
		$party_type = (string) $party_xml_object->{'party'}->{'party-type'};

		// Gathers the appropriate fields based on the Party Type
		if($party_type == 'Employer')
		{
			// Employer specific fields
			$form_fields->employer_company = $party_xml_object->{'party'}->{'trading-name'};

			$employer_address = $party_xml_object->{'party'}->{'addresses'}->{'address'};
			
			$form_fields->employer_address= (string) $employer_address->{'street-address1'};
			$employer_street_address2 = (string) $employer_address->{'street-address2'};
			if( trim($employer_street_address2) != '')
			{
				$form_fields->employer_address.= ' ' . $employer_street_address2;
			}
			$form_fields->employer_suburb= (string) $employer_address->{'suburb'};
			$form_fields->employer_state= (string) $employer_address->{'state'};
			$form_fields->employer_postcode= (string) $employer_address->{'post-code'};
			
			$employer_contact_details = $party_xml_object->{'party'}->{'contact-details'}->{'contact-detail'};
			foreach($employer_contact_details as $employer_contact_detail)
			{
				$contact_type = (string) $employer_contact_detail->{'contact-type'};
				$location = (string) $employer_contact_detail->{'location'};
				if($contact_type == 'Email')
				{
					$form_fields->employer_supervisor_email = (string) $employer_contact_detail->{'value'};
				}
				elseif ($contact_type == 'Phone' && $location == 'Main Office')
				{
					$form_fields->employer_office_phone = (string) $employer_contact_detail->{'value'};
				}
				elseif ($contact_type == 'Phone' && $location == 'Other Office')
				{
					$form_fields->employer_supervisor_phone = (string) $employer_contact_detail->{'value'};
				}
			}
			
			$form_fields->employer_supervisor_firstname = '';
			$form_fields->employer_supervisor_surname = '';
			
			// Determines if the Party is a NECA member or not
			$adhoc_fields = $party_xml_object->{'party'}->{'ad-hoc-fields'}->{'ad-hoc-field'};
			
			foreach($adhoc_fields as $adhoc_field)
			{
				if($adhoc_field->name == 'NECA Member')
				{
					$form_fields->neca_member = (string) $adhoc_field->value;
				}
			}
			
		}
		else 
		{
			$form_fields->gender = (string) $party_xml_object->{'party'}->{'gender'};
			$form_fields->title = (string) $party_xml_object->{'party'}->{'title'};
			$form_fields->first_name = (string) $party_xml_object->{'party'}->{'first-name'};
			$form_fields->middle_name = (string) $party_xml_object->{'party'}->{'middle-name'};
			$form_fields->surname = (string) $party_xml_object->{'party'}->{'surname'};
			$form_fields->known_by = (string) $party_xml_object->{'party'}->{'known-by'};
			$form_fields->birth_date = (string) $party_xml_object->{'party'}->{'birth-date'};
			if(isset($party_xml_object->{'party'}->{'usi-number'}) && $party_xml_object->{'party'}->{'usi-number'} != '')
			{
				$form_fields->usi_flag = 'Yes';
				$form_fields->usi_number = (string) $party_xml_object->{'party'}->{'usi-number'};
			}
			else
			{
				$form_fields->usi_number = '';
			}
			
			$addresses = $party_xml_object->{'party'}->{'addresses'}->{'address'};
			
			// Set Postal Address Same before we loop through
			// If we find a postal address, we'll override this flag accordingly.
			$form_fields->postal_address_same = 'Yes';
			
			// Loops through all addresses
            if( is_array( $addresses ) || is_object( $addresses ) ) 
            {
                foreach($addresses as $address)
                {
                    $location = (string) $address->{'location'};
                    if($location == 'Home')
                    {
                        $form_fields->street_number = (string) $address->{'street-number'};
                        $form_fields->street_name = (string) $address->{'street-name'};

                        $form_fields->street_address1 = (string) $address->{'street-address1'};
                        $street_address2 = (string) $address->{'street-address2'};
                        if( trim($street_address2) != '')
                        {
                            $form_fields->street_address1 .= ' ' . $street_address2;
                        }
                        
                        $form_fields->suburb = (string) $address->{'suburb'};
                        $form_fields->state = (string) $address->{'state'};
                        $form_fields->country = (string) $address->{'country'};
                        $form_fields->postcode = (string) $address->{'post-code'};
                    }
                    elseif ($location == 'Postal')
                    {
                        $form_fields->postal_street_number = (string) $address->{'street-number'};
                        $form_fields->postal_street_name = (string) $address->{'street-name'};
                        
                        $form_fields->postal_street_address1 = (string) $address->{'street-address1'};
                        $postal_street_address2 = (string) $address->{'street-address2'};
                        if( trim($postal_street_address2) != '')
                        {
                            $form_fields->postal_street_address1 .= ' ' . $postal_street_address2;
                        }
                        $form_fields->postal_suburb = (string) $address->{'suburb'};
                        $form_fields->postal_state = (string) $address->{'state'};
                        $form_fields->postal_country = (string) $address->{'country'};
                        $form_fields->postal_postcode = (string) $address->{'post-code'};
                        $form_fields->postal_address_same = 'No';
                    }
                }
            }
	
			$contact_details = $party_xml_object->{'party'}->{'contact-details'}->{'contact-detail'};
			
            if( is_array( $contact_details ) || is_object( $contact_details ) ) 
            {
                foreach($contact_details as $contact_detail)
                {
                    $contact_type = (string) $contact_detail->{'contact-type'};
                    if($contact_type == 'Email')
                    {
                        $form_fields->email = (string) $contact_detail->{'value'};
                    }
                    elseif ($contact_type == 'Mobile')
                    {
                        $form_fields->mobile_phone = (string) $contact_detail->{'value'};
                    }
                    elseif ($contact_type == 'Phone')
                    {
                        $form_fields->home_phone = (string) $contact_detail->{'value'};
                    }
                }
            }

			$avetmiss = $party_xml_object->{'party'}->{'avetmiss'};
			
			$labour_force_status = (string) $avetmiss->{'labour-force-status'};
			$form_fields->labour_force_status = strstr($labour_force_status, '/', true);
			
			// Set default to Australia if not specified in Job Ready
			$country_of_birth = (string) $avetmiss->{'country-of-birth'};
			$form_fields->country_of_birth = trim($country_of_birth) == '' ? 'Australia' : $country_of_birth;
			
			
			$form_fields->citizenship_status = (string) $avetmiss->{'citizenship-status'};
			$form_fields->citizenship_other = (string) $avetmiss->{'nationality'};
			
			if( $form_fields->citizenship_status == 'Australian Citizenship' ||
				$form_fields->citizenship_status == 'Permanent Humanitarian Visa Holder' ||
				$form_fields->citizenship_status == 'New Zealand Citizen' )
			{
				$form_fields->australian_citizen = "Yes";
			}
			elseif($form_fields->citizenship_status == '')
			{
				$form_fields->australian_citizen = "";
			}
			else
			{
				$form_fields->australian_citizen = "No";
			}
			$form_fields->indigenous_status = (string) $avetmiss->{'indigenous-status'};

			$main_language = (string) $avetmiss->{'main-language'};
			$form_fields->main_language = $main_language;

			$form_fields->at_school_flag = (string) $avetmiss->{'at-school-flag'};
			$form_fields->school = (string) $avetmiss->{'school'};
			$highest_school_level = (string) $avetmiss->{'highest-school-level'};
			$form_fields->highest_school_level = strstr($highest_school_level, '/', true);
			$form_fields->year_highest_school_level = (string) $avetmiss->{'year-highest-school-level'};

	
			$form_fields->prior_education_flag = $avetmiss->{'prior-education-flag'};
			if($form_fields->prior_education_flag == 'Yes')
			{
				$form_fields->prior_educations = array();
				
				$prior_educations = $avetmiss->{'prior-educations'}->{'prior-education'};
				
				foreach($prior_educations as $prior_education)
				{
					array_push( $form_fields->prior_educations, (string) $prior_education->{'prior-education-type'} );
				}
			}
			
			$form_fields->city_of_birth = (string) $avetmiss->{'town-of-birth'};
			
			if(isset($avetmiss->disabilities->disability))
			{
				$form_fields->disability_flag = "Yes";

				$disabilities = $avetmiss->disabilities->disability;
				
				foreach($disabilities as $disability)
				{
					array_push($form_fields->disability_types, (string) $disability->{'disability-type'});
				}
			}
			else 
			{
				$form_fields->disability_flag = "No";
			}
		}
		
		return $form_fields;
	}

    static function convertPartyXMLToJobReadyForm($party_xml_object)
    {
        $form_fields = new JobReadyForm();
        $party_type = (string) $party_xml_object->{'party-type'};

        $form_fields->gender = (string) $party_xml_object->{'gender'};
        $form_fields->title = (string) $party_xml_object->{'title'};
        $form_fields->first_name = (string) $party_xml_object->{'first-name'};
        $form_fields->middle_name = (string) $party_xml_object->{'middle-name'};
        $form_fields->surname = (string) $party_xml_object->{'surname'};
        $form_fields->known_by = (string) $party_xml_object->{'known-by'};
        $form_fields->birth_date = (string) $party_xml_object->{'birth-date'};
        if(isset($party_xml_object->{'usi-number'}) && $party_xml_object->{'usi-number'} != '')
        {
            $form_fields->usi_flag = 'Yes';
            $form_fields->usi_number = (string) $party_xml_object->{'usi-number'};
        }
        else
        {
            $form_fields->usi_number = '';
        }
        
        $addresses = $party_xml_object->{'addresses'}->{'address'};
        
        // Set Postal Address Same before we loop through
        // If we find a postal address, we'll override this flag accordingly.
        $form_fields->postal_address_same = 'Yes';
        
        // Loops through all addresses
        if( is_array( $addresses ) || is_object( $addresses ) ) 
        {
            foreach($addresses as $address)
            {
                $location = (string) $address->{'location'};
                if($location == 'Home')
                {
                    $form_fields->street_number = (string) $address->{'street-number'};
                    $form_fields->street_name = (string) $address->{'street-name'};

                    $form_fields->street_address1 = (string) $address->{'street-address1'};
                    $street_address2 = (string) $address->{'street-address2'};
                    if( trim($street_address2) != '')
                    {
                        $form_fields->street_address1 .= ' ' . $street_address2;
                    }
                    
                    $form_fields->suburb = (string) $address->{'suburb'};
                    $form_fields->state = (string) $address->{'state'};
                    $form_fields->country = (string) $address->{'country'};
                    $form_fields->postcode = (string) $address->{'post-code'};
                }
                elseif ($location == 'Postal')
                {
                    $form_fields->postal_street_number = (string) $address->{'street-number'};
                    $form_fields->postal_street_name = (string) $address->{'street-name'};
                    
                    $form_fields->postal_street_address1 = (string) $address->{'street-address1'};
                    $postal_street_address2 = (string) $address->{'street-address2'};
                    if( trim($postal_street_address2) != '')
                    {
                        $form_fields->postal_street_address1 .= ' ' . $postal_street_address2;
                    }
                    $form_fields->postal_suburb = (string) $address->{'suburb'};
                    $form_fields->postal_state = (string) $address->{'state'};
                    $form_fields->postal_country = (string) $address->{'country'};
                    $form_fields->postal_postcode = (string) $address->{'post-code'};
                    $form_fields->postal_address_same = 'No';
                }
            }
        }

        $contact_details = $party_xml_object->{'contact-details'}->{'contact-detail'};
        
        if( is_array( $contact_details ) || is_object( $contact_details ) ) 
        {
            foreach($contact_details as $contact_detail)
            {
                $contact_type = (string) $contact_detail->{'contact-type'};
                if($contact_type == 'Email')
                {
                    $form_fields->email = (string) $contact_detail->{'value'};
                }
                elseif ($contact_type == 'Mobile')
                {
                    $form_fields->mobile_phone = (string) $contact_detail->{'value'};
                }
                elseif ($contact_type == 'Phone')
                {
                    $form_fields->home_phone = (string) $contact_detail->{'value'};
                }
            }
        }

        $avetmiss = $party_xml_object->{'avetmiss'};
        
        $labour_force_status = (string) $avetmiss->{'labour-force-status'};
        $form_fields->labour_force_status = strstr($labour_force_status, '/', true);
        
        // Set default to Australia if not specified in Job Ready
        $country_of_birth = (string) $avetmiss->{'country-of-birth'};
        $form_fields->country_of_birth = trim($country_of_birth) == '' ? 'Australia' : $country_of_birth;
        
        
        $form_fields->citizenship_status = (string) $avetmiss->{'citizenship-status'};
        $form_fields->citizenship_other = (string) $avetmiss->{'nationality'};
        
        if( $form_fields->citizenship_status == 'Australian Citizenship' ||
            $form_fields->citizenship_status == 'Permanent Humanitarian Visa Holder' ||
            $form_fields->citizenship_status == 'New Zealand Citizen' )
        {
            $form_fields->australian_citizen = "Yes";
        }
        elseif($form_fields->citizenship_status == '')
        {
            $form_fields->australian_citizen = "";
        }
        else
        {
            $form_fields->australian_citizen = "No";
        }
        $form_fields->indigenous_status = (string) $avetmiss->{'indigenous-status'};

        $main_language = (string) $avetmiss->{'main-language'};
        $form_fields->main_language = $main_language;

        $form_fields->at_school_flag = (string) $avetmiss->{'at-school-flag'};
        $form_fields->school = (string) $avetmiss->{'school'};
        $highest_school_level = (string) $avetmiss->{'highest-school-level'};
        $form_fields->highest_school_level = strstr($highest_school_level, '/', true);
        $form_fields->year_highest_school_level = (string) $avetmiss->{'year-highest-school-level'};


        $form_fields->prior_education_flag = $avetmiss->{'prior-education-flag'};
        if($form_fields->prior_education_flag == 'Yes')
        {
            $form_fields->prior_educations = array();
            
            $prior_educations = $avetmiss->{'prior-educations'}->{'prior-education'};
            
            foreach($prior_educations as $prior_education)
            {
                array_push( $form_fields->prior_educations, (string) $prior_education->{'prior-education-type'} );
            }
        }
        
        $form_fields->city_of_birth = (string) $avetmiss->{'town-of-birth'};
        
        if(isset($avetmiss->disabilities->disability))
        {
            $form_fields->disability_flag = "Yes";

            $disabilities = $avetmiss->disabilities->disability;
            
            foreach($disabilities as $disability)
            {
                array_push($form_fields->disability_types, (string) $disability->{'disability-type'});
            }
        }
        else 
        {
            $form_fields->disability_flag = "No";
        }
    return $form_fields;
    }
}