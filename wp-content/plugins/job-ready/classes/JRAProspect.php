<?php
/*
 * JRAProspect class + JRAProspectOperations class
 * Created by: James Witika
 * Company: Smooth Developments
 */

if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class JRAProspect
{
	var $party_identifier; 		// string		Cannot be changed after creation. Mandatory
	var $course_number; 		// reference	Cannot be changed after creation. Mandatory
	var $prospect_type;			// reference	"Online Enrolment", "Waiting List", "Sales Lead", "Student Import", "Agent Referral", "DELTA"
	var $start_date; 			// date 		Read only
	var $target_end_date; 		// date 		Read only
	var $created_on; 			// datetime 	Read only
	var $updated_on; 			// datetime 	Read only
	var $referring_agent; 		// reference 	Read only
	
	function __construct()
	{
		$this->party_identifier = ''; 						// string		Relates back to "Party"
		$this->course_number = ''; 							// reference	Relates back to "Course
		$this->prospect_type = 'Online Enrolment';			// reference
	}
}

class JRAProspectOperations
{
	function __construct()
	{
		
	}
	
	
	static function getJRAProspect($party_id)
	{
		global $jr_api_headers;
		
		$webservice = '/webservice/prospects?party_id=' . $party_id;
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
			
			$prospects = xmlToObject($result);
			$courses = array();
			
			foreach($prospects as $prospect)
			{
				$course_number = (string) $prospect->{'course-number'};
				array_push($courses, $course_number);
			}
			
			return $courses;
		}
		catch (Exception $e)
		{
			$error = $e->getMessage();
			send_error_email($url, $method, $xml, $error);
			return false;
		}
		
	}
	
	static function createJRAProspect( $xml )
	{
		global $jr_api_headers;
		
		$webservice = '/webservice/prospects';
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

			if(isset($result_object->id))
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
	
	
	static function createJRAProspectXML( $prospect )
	{

		// XML Header
		$xml = '<?xml	version="1.0"	encoding="UTF-8"?>
				<prospect>';

		$xml .= '	<party-identifier>'.$prospect->party_identifier.'</party-identifier>
					<course-number>'.$prospect->course_number.'</course-number>
					<prospect-type>'.$prospect->prospect_type.'</prospect-type>';
		
		// Close Prospect
		$xml .= '</prospect>';
		
		return $xml;
	}
}