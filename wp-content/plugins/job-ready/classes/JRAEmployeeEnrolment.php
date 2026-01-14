<?php
/*
 * JRAEmployeeEnrolment class + JRAEmployeeEnrolmentOperations class
 * Created by: James Witika
 * Company: Smooth Developments
 */

if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class JRAEmployeeEnrolment
{
	var $id;							// Employer ID
	var $party_identifier; 				// Employer Party Identifier
	var $name;							// Employer Trading Name
	
	function __construct()
	{
		$this->id = '';
		$this->party_identifier = ''; 	// reference
		$this->name = '';
	}
}


class JRAEmployeeEnrolmentOperations
{
	function __construct()
	{

	}
	

	// Create XML layout for all Party Adhoc
	static function createJRAEmployeeEnrolmentXMLOld( $employee_enrolment )
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<employer>
					<id>'.$employee_enrolment->id.'</id>
					<party-identifier>' . $employee_enrolment->party_identifier . '</party-identifier>
					<name>' . $employee_enrolment->name . '</name>
					<employment-type-id>3</employment-type-id>
				 </employer>';
		
		return $xml;
	}
	
	
	// Create XML layout for all Employee Enrolment
	// Update: 24.05.2023 JW
	static function createJRAEmployeeEnrolmentXML( $employee_enrolment )
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<employer>
					<party-identifier>' . $employee_enrolment->party_identifier . '</party-identifier>
					<employment-type-id>3</employment-type-id>
				 </employer>';
		return $xml;
	}
	
	
	static function createJRAParty( $enrolment_id, $xml )
	{
		global $jr_api_headers;
		
		$webservice = '/webservice/employee_enrolments?enrolment_identifier=' . $enrolment_id;
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
			
			if(isset($result_object->{'party-identifier'}))
			{
				return $result_object;
			}
			else
			{
				$error = var_export($result_object, true);
				send_error_email($url, $method, $xml, $error);
				return false;
			}
		}
		catch (Exception $e)
		{
			$error = $e->getMessage();
			send_error_email($url, $method, $xml, $error);
			return false;
		}
	}
	
	static function createJRAEmployeeEnrolment( $enrolment_id, $xml )
	{
		global $jr_api_headers;
		
		$webservice = '/webservice/enrolments/' . $enrolment_id . '/employee_enrolments';
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
			
			if(isset($result_object->{'party-identifier'}))
			{
				return $result_object;
			}
			else
			{
				$error = var_export($result_object, true);
				send_error_email($url, $method, $xml, $error);
				return false;
			}
		}
		catch (Exception $e)
		{
			$error = $e->getMessage();
			send_error_email($url, $method, $xml, $error);
			return false;
		}
	}
}