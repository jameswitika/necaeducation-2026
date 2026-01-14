<?php
/*
 * JRAEmployee class + JRAEmployeeOperations class
 * Created by: James Witika
 * Company: Smooth Developments
 */

if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class JRAEmployee
{
	var $employer_party_identifier;	// reference	The party identifier of a valid Employer party. Mandatory
	var $employee_title;			// string	
	var $start_date; 				// date 		
	var $end_date; 					// date 		
	var $supervisor_contact_id;		// reference 	The ID number of a valid contact of the employer party
	
	function __construct()
	{
		$this->employer_party_identifier = '';
		$this->employee_title = '';
		$this->start_date = '';
		$this->end_date = '';
		$this->supervisor_contact_id = '';
	}
}

class JRAEmployeeOperations
{
	function __construct()
	{
		
	}
	
	
	// Returns an array of "employer party ids"
	static function getJRAEmployee( $party_id )
	{
		global $jr_api_headers;
		
		$webservice = '/webservice/parties/'.$party_id.'/employees';
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
			
			// Convert the XML to an Object
			$employees = xmlToObject($result);
			$employee_party_ids = array();
			
			foreach($employees as $employee)
			{
				$employer_party_id = (string) $employee->{'employer-party-identifier'};
				array_push($employee_party_ids, $employer_party_id);
			}
			
			return $employee_party_ids;
		}
		catch (Exception $e)
		{
			$error = 'JRAEmployee > getJRAEmployee() exception error: ' . $e->getMessage();
			
			// Send an email to the administrator
			$subject = 'NECA Education + Careers - getJRAEmployee error';
			$body_content = "The following error occurs on " . date('d-m-Y') . " while trying to get JRA Employee<br/><br/>" . $error . "<br/><br/>Please contact the website administrator.";
			
			echo $body_content;
			wp_mail('james@smoothdevelopments.com.au', $subject, $body_content, $headers = '');
			
			return false;
		}
	}
	
	
	static function createJRAEmployee( $party_id, $xml )
	{
		global $jr_api_headers;
		
		$webservice = '/webservice/parties/'.$party_id.'/employees';
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
			// Access attribute with '-' hyphens using this syntax: "$result_object->{'party-identifier'}"
			
			$result_object = xmlToObject($result);

			return $result_object;
		}
		catch (Exception $e)
		{
			$error = 'JRAEmployee > createJRAEmployee() exception error: ' . $e->getMessage();
			
			// Send an email to the administrator
			$subject = 'NECA Education + Careers - createJRAEmployee error';
			$body_content = "The following error occurs on " . date('d-m-Y') . " while trying to sync the Course data with JobReady:<br/><br/>" . $error . "<br/><br/>Please contact the website administrator.";
			
			echo $body_content;
			wp_mail('james@smoothdevelopments.com.au', $subject, $body_content, $headers = '');
			
			return false;
		}
	}
	
	
	static function createJRAEmployeeXML( $employee )
	{

		// XML Header
		$xml = '<?xml	version="1.0"	encoding="UTF-8"?>
				<employee>';

		$xml .= '	<employer-party-identifier>'.$employee->employer_party_identifier.'</employer-party-identifier>';
		
		if($employee->employee_title != '')
		{
			$xml .= '<employee-title>'.$employee->employee_title.'</employee-title>';
		}
		if($employee->start_date != '')
		{
			$xml .= '<start-date>'.$employee->start_date.'</start-date>';
		}
		if($employee->end_date != '')
		{
			$xml .= '<end-date>'.$employee->end_date.'</end-date>';
		}
		/*if($employee->supervisor_contact_id != '')
		{
			$xml .= '<supervisor-contact-id>'.$employee->supervisor_contact_id.'</supervisor-contact-id>';
		}*/
		
		// Close Prospect
		$xml .= '</employee>';
		
		return $xml;
	}
}