<?php
/*
 * JRAEnrolment class + JRAEnrolmentOperations class
 * Created by: James Witika
 * Company: Smooth Developments
 */

if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class JRAEnrolment
{
	var $party_identifier; 							// reference	Cannot be changed after creation. Mandatory
	var $course_number;								// reference	Cannot be changed after creation. Mandatory
	var $study_reason;								// reference
	var $enrolment_status;							// reference	enrolment_status
	var $invoice_option;							// reference	Invoice Option / Payment Option
	var $victorian_student_number;					// string		Only relevant if the course state is Victoria.
	var $unknown_victorian_student_number;			// boolean
	var $previous_victorian_education_enrolment;	// string
	var $client_occupation_identifier;				// reference	Only relevant if the course state is Victoria.
	var $client_industry_employment;				// reference	Only relevant if the course state is Victoria.
	var $adhoc_child;								// array		Adhoc_fields
	var $commencing_program_cohort_identifiers;		// array

	function __construct()
	{
		$this->party_identifier = '';
		$this->course_number = '';
		$this->study_reason = '';
		$this->enrolment_status = '';
		$this->invoice_option = '';
		$this->victorian_student_number = '';
		$this->unknown_victorian_student_number = '';
		$this->previous_victorian_education_enrolment = '';
		$this->client_occupation_identifier = '';
		$this->client_industry_employment = '';
		//$this->employers = array();
		$this->adhoc_child = array();
		$this->commencing_program_cohort_identifiers = array();
	}
}

class JRAEnrolmentOperations
{
	function __construct()
	{
		
	}
	
	
	static function createJRAEnrolment( $xml )
	{
		global $jr_api_headers;
		
		$webservice = '/webservice/enrolment';
		$url = JR_API_SERVER . $webservice;
		$method = 'POST';
		
		// Call the Job Ready API
		try {
			
			//make POST request
			$response = wp_remote_request(	$url,
					array(	'method' 	=> $method,
							'headers' 	=> $jr_api_headers,
							'body' 		=> $xml,
							'timeout' 	=> 500 )
					);
			
			// Get the response
			$result = wp_remote_retrieve_body( $response );
			
			// Convert the XML to an Object
			$result_object = xmlToObject($result);
			
			if(isset($result_object->{'enrolment-identifier'}))
			{
				return $result_object;
			}
			else 
			{
				$error = var_export($result_object, true);
				send_error_email($url, $method, $xml, $error, $response);
				return false;
			}
		}
		catch (Exception $e)
		{
			$error = $e->getMessage();
			send_error_email($url, $method, $xml, $error, $response);
			return false;
		}
	}
	

	static function updateJRAEnrolment( $xml, $enrolment_id )
	{
		global $jr_api_headers;
		
		$webservice = '/webservice/enrolment/' . $enrolment_id;
		$url = JR_API_SERVER . $webservice;
		$method = 'POST';
		
		// Call the Job Ready API
		try {
			
			//make POST request
			$response = wp_remote_request(	$url,
					array(	'method' 	=> $method,
							'headers' 	=> $jr_api_headers,
							'body' 		=> $xml,
							'timeout' 	=> 500 )
					);
			
			// Get the response
			$result = wp_remote_retrieve_body( $response );
			
			// Convert the XML to an Object
			$result_object = xmlToObject($result);
			
			if(isset($result_object->{'enrolment-identifier'}))
			{
				return $result_object;
			}
			else
			{
				$error = var_export($result_object, true);
				send_error_email($url, $method, $xml, $error, $response);
				return false;
			}
		}
		catch (Exception $e)
		{
			$error = $e->getMessage();
			send_error_email($url, $method, $xml, $error, $response);
			return false;
		}
	}
	
	
	
	static function createJRAEnrolmentXML( $enrolment )
	{
		// XML Header
		$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<enrolment>';

		// Get the first string element from the invoice option (which returns name|price)
		$invoice_option = explode("|", $enrolment->invoice_option);
		
		$xml .= '	<party-identifier>'.$enrolment->party_identifier.'</party-identifier>
					<course-number>'.$enrolment->course_number.'</course-number>
					<study-reason>'.$enrolment->study_reason.'</study-reason>';
		
		// Only include if this field was set to something
		if(trim($enrolment->enrolment_status) != '')
		{
			$xml .= '<enrolment-status>'.$enrolment->enrolment_status.'</enrolment-status>';
		}
		
		
		$xml .= '	<invoice-option>'.$invoice_option[0].'</invoice-option>
					<client-occupation-identifier>'.$enrolment->client_occupation_identifier.'</client-occupation-identifier>
					<client-industry-employment>'.$enrolment->client_industry_employment.'</client-industry-employment>';

		if(isset($enrolment->adhoc_child) && count($enrolment->adhoc_child) > 0)
		{
			$xml .= JRAEnrolmentAdhocOperations::createJRAEnrolmentAdhocXML( $enrolment->adhoc_child );
		}
		
		if(isset($enrolment->commencing_program_cohort_identifiers) && count($enrolment->commencing_program_cohort_identifiers) > 0)
		{
			$xml .= JRAEnrolmentCommencingProgramCohortIdentifierOperations::createJRAEnrolmentCommencingProgramCohortIdentifierXML( $enrolment->commencing_program_cohort_identifiers);
		}
		
		
		// Close Prospect
		$xml .= '</enrolment>';
		
		return $xml;
	}
	
	
	static function updateJRAEnrolmentVSN( $enrolment, $enrolment_id )
	{
		$update_xml = JRAEnrolmentOperations::createJRAUpdateEnrolmentVSNXML($enrolment);
		$result = JRAEnrolmentOperations::updateJRAEnrolment($update_xml, $enrolment_id);
		return $result;
	}
	
	
	// Create XML to Update the VSN for an Enrolment
	static function createJRAUpdateEnrolmentVSNXML( $enrolment )
	{
		// XML Header
		$xml = '<?xml	version="1.0"	encoding="UTF-8"?>
				<enrolment>
					<victorian-student-number>'.$enrolment->victorian_student_number.'</victorian-student-number>
					<unknown-victorian-student-number>'.$enrolment->unknown_victorian_student_number.'</unknown-victorian-student-number>
					<previous-victorian-education-enrolment>'.htmlspecialchars($enrolment->previous_victorian_education_enrolment, ENT_XML1).'</previous-victorian-education-enrolment>
				</enrolment>';
		
		return $xml;
	}
}