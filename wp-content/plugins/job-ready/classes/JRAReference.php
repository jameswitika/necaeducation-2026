<?php
/*
 * JRAReference class + JRAReferenceOperations class
 * Created by: James Witika
 * Company: Smooth Developments
 */

/* Below is a list of all the references available
- Document Category: (document_category)
- Title: (title)
- State: (state)
- Contact  Type: (contact_type)
- Contact Location: (contact_location)
- Identification Type: (identification_type)
- Relationship Type: (relationship_type)
- Role Type: (role_type)
- Course Scope Pay Type: (course_scope_pay_type)
- Unit Scope Result: (unit_scope_result)
- Staff Status: (staff_status)
- Course Type: (course_type)
- Assessment Method: (assessment_method)
- Unit Status: (unit_status)
- Party Type: (party_type)
- Batch Log Type: (batch_log_type)
- Employment Basis: (employment_basis)
- Employee Title Type: (employee_title_type)
- Relationship Title Type: (relationship_title_type)
- Country: (country)
- At School Flag: (at_school_flag)
- Delivery Mode: (delivery_mode)
- Disability Flag: (disability_flag)
- Disability Type: (disability_type)
- Employment Category: (employment_category)
- Enrolment Status: (enrolment_status)
- Fee Exemption State: (fee_exemption_state)
- Funding Source State: (funding_source_state)
- Funding Source National: (funding_source_national)
- Gender: (gender)
- Highest School Level: (highest_school_level)
- Indigenous Status: (indigenous_status)
- Outcome Identifier: (outcome_identifier_avetmiss)
- Prior Education: (prior_education_flag)
- Prior Education Level: (prior_education_type)
- Qualification Level: (qualification_level)
- Recognition Status Type: (recognition_status_type)
- RTO Type: (rto_type)
- Spoken English Proficiency: (spoken_english_proficiency)
- Study Mode: (study_mode)
- Study Reason: (study_reason)
- ANZSCO Code: (anzsco_code)
- FOE Code: (foe_code)
- ANZSIC Code: (anzsic_code)
- Language: (language)
- Tax Treatment: (tax_treatment)
- Invoicing Method: (invoicing_method)
- Delivery Method: (delivery_method)
- Invoice Status: (invoice_status)
- Invoice Option Type: (invoice_option_type)
- Ledger Type: (ledger_type)
- Ledger Code Lookup: (ledger_code_lookup)
- Payment Type: (payment_type)
- Payment Source: (payment_source)
- Post Codes: (postcode)
- Fee Exemption State: (fee_exemption_state)
- Funding Source State: (funding_source_state)
- Ledger Category: (ledger_category)
- Staff Charge Type: (staff_charge_type)
- Invoice Template Type: (invoice_template_type)
- Contact Role: (contact_role)
- AAC: (aac)
- Party Group Type: (party_group_type)
- Party Group: (party_group)
- Finance System: (finance_system)
- Block Training: (block_training)
- Claim Loading: (claim_loading)
- Element Type: (element_type)
- Course Group Type: (course_group_type)
- Course Group: (course_group)
- End Reason: (end_reason)
- Fees: (fee)
- Sales Lead Source: (sales_lead_source)
- Event Type: (event_type)
- Region: (region)
- Visa Subtype: (visa_subtype)
- Citizenship Status: (citizenship_status)
- Outcome Identifier Training Organisation: (outcome_identifier_training_organisation)
- Service Status: (service_status)
- Visa Status: (visa_status)
- Priority: (priority)
- Prospect Type: (role_prospect_type)
- Sales Lead Status: (sales_lead_status)
- Visa Type: (visa_type)
- Ethnic Origin: (ethnic_origin)
- Agent Status: (agent_status)
- Pre Sort Indicator Range: (pre_sort_indicator_range)
- Svp Assessment Level: (svp_assessment_level)
- English Test Delivery Method: (english_test_delivery_method)
- Interview Rating: (interview_rating)
- Industry: (industry)
- Service Type: (service_type)
- Prospect Cancellation Reason: (prospect_cancellation_reason)
- Document Type: (document_type)
- Vocation: (vocations)
- English Test Type: (english_test_type)
- Accommodation Provider: (accommodation_provider)
- Transport Type: (transport_type)
- Equipment: (equipment)
- Reason for Rotation: (reason_for_rotations)
- Kanban Statuses (kanban_statuses_provider)
- Transport Provider (transport_provider)
- Unit Type (unit_scope_type)
- OSHC (oshc_provider)
- Study Period (study_period)
- Absence Reason (absence_reason)
- Prospect Referral Status (referral_status)
- Job Account (job_account)
- Service Provider (service_provider)
- Sales Lead Prospect Status (sales_lead_role_prospect_status)
- COE Status (coe_status)
- Visa Education Sector (visa_education_sector)
- Employment Service Provider (ESP) (employment_service_provider)
- Course Status (course_status)
- School (school)
- Event Attendance Status (event_attendance_status)
- Welfare Type (welfare_type)
- International Fee Tier (international_fee_tier)
- Fees Per Week (fees_per_week)
- Discount Code (discount_code)
- Fee Version (fee_version)
- Host Family Status (host_family_status)
 */

if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class JRAReference
{
	var $table_name; 			// string		The unique ID (see above)
	
	function __construct()
	{
		$this->table_name = ''; // string		The unique ID (see above)
	}
}

class JRAReferenceOperations
{
	function __construct()
	{
		
	}
	
	
	static function getJRAReference( $reference, $offset = 0 )
	{
		global $jr_api_headers;
		
		$webservice = '/webservice/references/' . $reference . '?offset=' . $offset;
		$url = JR_API_SERVER . $webservice;
		$method = 'GET';
		
		// Call the Job Ready API
		try {
			
			//make POST request
			$response = wp_remote_request(	$url,
					array(	'method' 	=> $method,
							'headers' 	=> $jr_api_headers,
							'timeout' 	=> 500 )
					);
			
			// Get the response
			$result = wp_remote_retrieve_body( $response );
			return $result;
		}
		catch (Exception $e)
		{
			echo "Error: " . print_r($e, true) . "<br/>";
			return false;
		}
	}
	
	
	static function filterStudyReasons($choices)
	{
		$new_choices = array();
		foreach($choices as $choice)
		{
			// Checks if the Study Reason has (WA) in it and removes it from the choices array
			if( strpos($choice['text'], '(WA)') === false)
			{
				array_push($new_choices, $choice);
			}
		}
		return $new_choices;
	}
	
	
	static function getReference( $reference, $reference_branch, $please_select = false)
	{
		$max_results = 100;
		$choices= array();
		$reference_xml = JRAReferenceOperations::getJRAReference( $reference );

		$reference_object = xmlToObject($reference_xml);
		
		if($reference_object != false)
		{
			// Get the total count (from attributes)
			$total_count = $reference_object->attributes()['total'];

			/*
			if($please_select)
			{
				// Please Select
				$choices[] = array ('text' => '-- Please Select --',
									'value' => NULL,
									'isSelected' => false );
			}
			*/
			
			// If more than 100 results, we need to get the rest of the results
			if($total_count> $max_results)
			{
				// Add the current results to the choices array
				foreach($reference_object->{$reference_branch} as $cs)
				{
					$field_text = (trim($cs->description) != '') ? (string) $cs->description : (string) $cs->name;
					
					$choices[] = array ('text' => $field_text,
										'value' => (string) $cs->name,
										'isSelected' => false );
				}
				
				// Retrieve the remaining results and loop through the process until the total results have been retrieved
				for($offset=$max_results; $offset <= $total_count; $offset += $max_results)
				{
					// Get the next set of results and process them
					$result = JRAReferenceOperations::getJRAReference($reference, $offset);
					$reference_object = xmlToObject($result);
					
					// Loop through next set of references
					foreach($reference_object->{$reference_branch} as $cs)
					{
						$field_text = (trim($cs->description) != '') ? (string) $cs->description : (string) $cs->name;
						
						$choices[] = array ('text' => (string) $field_text,
											'value' => (string) $cs->name,
											'isSelected' => false );
					}
				}
			}
			else
			{
				foreach($reference_object->{$reference_branch} as $cs)
				{
					$field_text = (trim($cs->description) != '') ? (string) $cs->description : (string) $cs->name;
					
					$choices[] = array ('text' => (string) $field_text,
										'value' => (string) $cs->name,
										'isSelected' => false );
				}
				
			}
			
			if($reference == 'study_reason')
			{
				$choices = JRAReferenceOperations::filterStudyReasons($choices);
			}
		}
		
		return $choices;
	}
	
}