<?php
/*
 * JRAPartyContact class + JRAPartyContactOperations class
 * Created by: James Witika
 * Company: Smooth Developments
 */

if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class JRAPartyContact
{
	var $id; 					// integer Automatically generated – cannot be assigned
	var $primary; 				// boolean Whether this contact is the party’s primary
	var $surname; 				// string Mandatory unless party-identifier is provided instead
	var $first_name; 			// string
	var $title; 				// reference
	var $relationship; 			// string Used	for	parties	of	type	Person
	var $jobtitle; 				// string Used	for	parties	of	type	Employer
	var $phone; 				// string
	var $email; 				// string
	var $contact_method; 		// reference “Letter” or “Email”
	var $party_identifier; 		// reference If used, the party with the given identifier will be used instead of the details above.
	
	function __construct()
	{
		$this->id = ''; 					// integer Automatically generated – cannot be assigned
		$this->primary = false; 			// boolean Whether this contact is the party’s primary
		$this->surname = ''; 				// string Mandatory unless party-identifier is provided instead
		$this->first_name = ''; 			// string
		$this->title = ''; 					// reference
		$this->relationship = ''; 			// string Used	for	parties	of	type	Person
		$this->jobtitle = ''; 				// string Used	for	parties	of	type	Employer
		$this->phone = ''; 					// string
		$this->email = ''; 					// string
		$this->contact_method = 'Email';	// reference “Letter” or “Email”
		$this->party_identifier = ''; 		// reference If used, the party with the given identifier will be used instead of the details above.
	}
}

class JRAPartyContactOperations
{
	function __construct()
	{
		
	}
	
	
	static function getJRAPartyContacts($party_id)
	{
		global $jr_api_headers;
		
		$webservice = '/webservice/parties/'.$party_id.'/party_contacts';
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
			$party_contacts = xmlToObject($result);
			$contacts = array();
			
			foreach($party_contacts as $party_contact)
			{
				$contact = new stdClass();
				$contact->id = (string) $party_contact->{'id'};
				$contact->first_name = (string) $party_contact->{'first-name'};
				$contact->surname = (string) $party_contact->{'surname'};
				$contact->phone = (string) $party_contact->{'phone'};
				$contact->email = (string) $party_contact->{'email'};
				$contact->relationship = (string) $party_contact->{'relationship'};
				$contact->primary = (string) $party_contact->{'primary'};
				array_push($contacts, $contact);
			}
			
			return $contacts;
		}
		catch (Exception $e)
		{
			$error = $e->getMessage();
			send_error_email($url, $method, '', $error);
			return false;
		}
		
	}
	
	
	static function updateJRAPartyContact( $party_id, $party_contact_id, $party_contact)
	{
		global $jr_api_headers;
		
		$webservice = '/webservice/parties/' . $party_id . '/party_contacts/' . $party_contact_id;
		$url = JR_API_SERVER . $webservice;
		$method = 'POST';
		
		$xml = JRAPartyContactOperations::updateJRAPartyContactXML ($party_contact);
		
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
	
	static function createJRAPartyContact( $party_id, $xml )
	{
		global $jr_api_headers;
		
		$webservice = '/webservice/parties/' . $party_id . '/party_contacts';
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
	
	
	static function createJRAPartyContactXML( $party_contact )
	{

		// XML Header
		$xml = '<?xml	version="1.0"	encoding="UTF-8"?>
				<party-contact>';

		$xml .='	<primary>true</primary>
					<surname>'.$party_contact->surname.'</surname>
					<first-name>'.$party_contact->first_name.'</first-name>
					<title>'.$party_contact->title.'</title>					
					<relationship>'.$party_contact->relationship.'</relationship>
					<phone>'.$party_contact->phone.'</phone>
					<email>'.$party_contact->email.'</email>';

		if($party_contact->contact_method == 'Email')
		{
			$xml .= '<contact-method>'.$party_contact->contact_method.'</contact-method>';
		}

		// Close Party
		$xml .= '</party-contact>';
		
		return $xml;
	}
	
	
	static function updateJRAPartyContactXML( $party_contact )
	{
		
		// XML Header
		$xml = '<?xml	version="1.0"	encoding="UTF-8"?>
				<party-contact>';
		
		$xml .='	<relationship>'.$party_contact->relationship.'</relationship>
					<phone>'.$party_contact->phone.'</phone>
					<email>'.$party_contact->email.'</email>';
		
		if($party_contact->contact_method == 'Email')
		{
			$xml .= '	<contact-method>'.$party_contact->contact_method.'</contact-method>';
		}
		
		// Close Party
		$xml .= '</party-contact>';
		
		return $xml;
	}
}