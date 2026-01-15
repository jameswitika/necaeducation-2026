<?php
/*
 * JRAParty class + JRAPartyOperations
 * Created by: James Witika
 * Company: Smooth Developments
 */

if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class JRAParty
{
	var $party_identifier; 	// string Automatically generated for new records unless explicitly specified
	var $party_type; 		// reference Either	“Person”	or	“Employer”	Mandatory
	var $contact_method; 	// reference Either “Letter” or “Email”
	var $surname; 			// string Used only for people Mandatory
	var $first_name; 		// string Used only for people Mandatory
	var $middle_name; 		// string Used only for people
	var $known_by; 			// string Used only for people
	var $birth_date; 		// string Used only	for	people	Mandatory
	var $gender; 			// reference Used only	for	people
	var $title; 			// reference Used only	for	people
	var $tags; 				// reference Used against	people	and	employers
	var $legal_name; 		// string Used only	for	employers	Mandatory
	var $trading_name;		// string Used only	for	employers	Mandatory
	var $website;			// string Used only	for	employers.	Must	be	prefixed	with	“http://”
	var $num_of_employees;	// integer Used	only for employers
	var $company_level;		// reference Used only for employers
	var $employer_source;	// reference Used only for employers
	var $anzsic_code;		// reference Used only for employers
	var $description;		// string Used only for	employers
	var $industry;			// string Used only for	employers
	var $image;				// Person’s	profile	picture or Employer’s logo
	var $do_not_market;		// boolean Used	to manage marketing related privacy for a party
	var $login;				// string Username for portals
	var $password;			// string Used to set a new password – will always be blank when returned
	var $password_temporary;// string Value visible in the application
	var $logon_enabled;		// boolean Whether to enable portal login
	var $created_on;		// datetime Read_only
	var $updated_on;		// datetime Read_only
	var $usi_number;		// string The Unique Student Identifier
	var $usi_status;		// string Read_only.
							/* Returns:	"Verified" if the USI is present and has been verified by the USI portal
							 			"Unverified" if the USI is present and has not been verified by the USI portal
							 			"No	USI" if USI is not present */
	// Child Resources
	
	var $address_child;			// Array of Addresses Child Resources
	var $contact_detail_child;	// Array of Contact Details Child Resources
	var $identification_child;	// Array of Identifications Child Resources
	var $avetmiss_child;		// AVETMISS Child Resource Object
	var $vet_free_help_child;	// VET Fee-Help Child Resource Object
	var $cricos_child;			// CRICOS Child Resource Object
	var $adhoc_child;			// Adhoc Child Resource Objects
	var $party_group_member_child; // Array of Party Group Member Child Resources

	//var $file_note_child;		// Array of File Note Child Resources
	//var $party_contact_child;	// Array of Party Contact Child Resources
	//var $employee_child;		// Array of Employee Child Resources
	
	function __construct()
	{
		$this->party_identifier = ''; 		// string Automatically generated for new records unless explicitly specified
		$this->party_type = 'Person'; 		// reference Either	“Person”	or	“Employer”	Mandatory
		$this->contact_method = 'Email';	// reference Either “Letter” or “Email”
		$this->surname = ''; 				// string Used only for people Mandatory
		$this->first_name = ''; 			// string Used only for people Mandatory
		$this->middle_name = ''; 			// string Used only for people
		$this->known_by = ''; 				// string Used only for people
		$this->birth_date = ''; 			// string Used	only	for	people	Mandatory
		$this->gender = ''; 				// reference Used	only	for	people
		$this->title = ''; 					// reference Used	only	for	people
		$this->tags = ''; 					// reference Used	against	people	and	employers
		$this->legal_name = ''; 			// string Used	only	for	employers	Mandatory
		$this->trading_name = '';			// string Used	only	for	employers	Mandatory
		$this->website = '';				//string Used	only	for	employers.	Must	be	prefixed	with	“http://”
		$this->num_of_employees = '';		// integer Used	only	for	employers
		$this->company_level = '';			// reference Used	only	for	employers
		$this->employer_source = '';		// reference Used	only	for	employers
		$this->anzsic_code = '';			// reference Used	only	for	employers
		$this->description = '';			// string Used	only	for	employers
		$this->industry = '';				// string Used	only	for	employers
		$this->image = '';					// Person’s	profile	picture	or	Employer’s	logo
		$this->do_not_market = false;		// boolean Used	to	manage	marketing	related	privacy	for	a	party
		$this->login = '';					// string Username	for	portals
		$this->password = '';				// string Used	to	set	a	new	password – will always be blank when returned
		$this->password_temporary = '';		// string Value	visible	in	the	application
		$this->logon_enabled = false;		// boolean Whether	to	enable	portal	login
		$this->usi_number = '';				// string The	Unique	Student	Identifier

		$this->address_child = array();							// Array of Addresses Child Resources
		$this->contact_detail_child = array();					// Array of Contact Details Child Resources
		$this->identification_child = array();					// Array of Identifications Child Resources
		$this->avetmiss_child = new JRAPartyAvetmiss();			// AVETMISS Child Resource object
		$this->vet_free_help_child = new JRAPartyVETFeeHelp();	// VET Fee-Help Child Resource object
		$this->cricos_child = new JRAPartyCricos();				// CRICOS Child Resource object
		$this->adhoc_child = array();							// Array of Adhoc Fields
		$this->party_group_member_child = array(); 				// Array of Party Group Member Child Resources

		//$this->file_note_child = array();		// Array of File Note Child Resources
		//$this->party_contact_child = array();	// Array of Party Contact Child Resources
		//$this->employee_child = array();		// Array of Employee Child Resources
		
	}
}

class JRAPartyOperations
{
	function __construct()
	{
		
	}
	
	
	static function loadEmployeesByEmployerID($employer_id)
	{
		global $jr_api_headers;
		$count = 0;
		$query_string = 'employer_party_identifier=' . $employer_id;
		$webservice = '/webservice/parties?' . htmlspecialchars($query_string);
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
			$parties = wp_remote_retrieve_body( $response );
			
			// Convert the XML to an Object
			// Access attribute with '-' hyphens using this syntax: "$result_object->{'party-identifier'}"
			$parties_object = xmlToObject($parties);
			
			// Return an array of an array employee details 
			$employees = array();
			
			foreach($parties_object as $party_object)
			{
				$employee = array(	'party_id'	=> (string) $party_object->{'id'},
									'party_login' => (string) $party_object->{'login'},
									'firstname'	=> (string) $party_object->{'first-name'},
									'surname'	=> (string) $party_object->{'surname'});
				
				// Add employee to employees array
				array_push($employees, $employee);
			}

			return $employees;
		}
		catch (Exception $e)
		{
			$error = $e->getMessage();
			send_error_email($url, $method, '', $error);
			return false;
		}
	}


    // Load a JRAParty by Party ID
    static function loadJRAPartyByID( $party_id )
    {
        global $jr_api_headers;

        $webservice = '/webservice/parties/' . htmlspecialchars($party_id);
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
            $result_object = xmlToObject($result);

            return $result_object;
        }
        catch (Exception $e)
        {
            $error = $e->getMessage();
            send_error_email($url, $method, '', $error);
            return false;
        }
    }
	
	
	static function getJRAParty( $party, $email = '' )
	{
		global $jr_api_headers;
		$count = 0;
		$query_string = '';
		
		if($party->party_type == 'Person')
		{
			$first_variable = true;
			if(isset($party->first_name) && $party->first_name != '')
			{
				$query_string .= 'first_name=' . $party->first_name;
				$first_variable = false;
			}
			
			if(isset($party->surname) && $party->surname != '')
			{
				$query_string .= $first_variable ? '' : '&'; // Adds an "&" if this is not the first variable
				$query_string .= 'surname='.$party->surname;
				$first_variable = false;
			}
				
			if(isset($party->birth_date) && $party->birth_date != '')
			{
				$query_string .= $first_variable ? '' : '&'; // Adds an "&" if this is not the first variable
				$query_string .= 'birth_date='.date_format($party->birth_date, "Y-m-d");
				$first_variable = false;
			}
			if($email != '')
			{
				$query_string .= $first_variable ? '' : '&'; // Adds an "&" if this is not the first variable
				$query_string .= 'email='.$email;
				$first_variable = false;
			}
		}
		elseif($party->party_type == 'Employer')
		{
			$query_string .= 'trading_name=' . $party->trading_name;
		}
		
		$webservice = '/webservice/parties?' . htmlspecialchars($query_string);
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
			$result_object = xmlToObject($result);
			
			return $result_object;
		}
		catch (Exception $e)
		{
			$error = $e->getMessage();
			send_error_email($url, $method, '', $error);
			return false;
		}
	}
	
	
	static function getJRAPartyByLogin( $login )
	{
		global $jr_api_headers;
		
		$query_string = 'login=' . $login;
		
		// Load Party by Login
		$party_webservice = '/webservice/parties?' . htmlspecialchars($query_string);
		$party_url = JR_API_SERVER . $party_webservice;
		$party_method = 'GET';
		
		//make POST request
		$party_response = wp_remote_request( $party_url,
				array(	'method' 	=> $party_method,
						'headers' 	=> $jr_api_headers,
						'timeout' 	=> 500 )
				);
		
		// Get the response
		$party_result = wp_remote_retrieve_body( $party_response );
		
		// Convert XML to an Object
		$party_result_object = xmlToObject($party_result);
		
		return $party_result_object;
	}
	
	
	static function loginJRAParty( $party )
	{
		global $jr_api_headers;
		
		// Call the Job Ready API
		try {
			
			$auth_webservice = '/webservice/party_authentication';
			$auth_url = JR_API_SERVER . $auth_webservice;
			$auth_method = 'POST';
			$login = $party->login;
			$password = $party->password;
			
			// XML Header
			$auth_xml = '<?xml version="1.0" encoding="UTF-8"?>
			<party>
				<login>'.htmlspecialchars($login).'</login>
				<password>'.htmlspecialchars($password).'</password>
			</party>';
			
			//make POST request
			$auth_response = wp_remote_request(	$auth_url,
					array(	'method' 	=> $auth_method,
							'headers' 	=> $jr_api_headers,
							'body' 		=> $auth_xml,
							'timeout' 	=> 500 )
					);
			
			// Get the response
			$auth_result = wp_remote_retrieve_body( $auth_response );
			
			// Convert the XML to an Object
			$auth_result_object = xmlToObject($auth_result);
			
			if(isset($auth_result_object->valid_password) && $auth_result_object->valid_password == 'true')
			{
				//echo "Working Login<br/>";
				$party = JRAPartyOperations::getJRAPartyByLogin( $login );
				return $party;
			}
			else
			{
				//echo "Invalid Login<br/>";
				return false;
			}
			
		}
		catch (Exception $e)
		{
			$error = $e->getMessage();
			send_error_email($auth_url, $auth_method, $auth_xml, $error);
			return false;
		}
	}
	
	
	static function createJRAParty( $xml )
	{
		global $jr_api_headers;
		
		$webservice = '/webservice/parties';
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
	
	
	
	static function updateJRAParty( $xml, $party_id )
	{
		global $jr_api_headers;
		
		$webservice = '/webservice/parties/' . $party_id;
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
	
	
	
	static function createJRAPartyXML( $party )
	{
		// XML Header
		$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<party>';

		$xml .= '	<party-type>'.$party->party_type.'</party-type>
					<contact-method>'.$party->contact_method.'</contact-method>';
		
		// Used for People Only
		if($party->party_type == "Person")
		{
			$xml .='	<surname>'.htmlspecialchars(convert_smart_quotes($party->surname), ENT_XML1, "utf-8").'</surname>
						<first-name>'.htmlspecialchars(convert_smart_quotes($party->first_name), ENT_XML1, "utf-8").'</first-name>
						<middle-name>'.htmlspecialchars(convert_smart_quotes($party->middle_name), ENT_XML1, "utf-8").'</middle-name>
						<known-by>'.htmlspecialchars(convert_smart_quotes($party->known_by), ENT_XML1, "utf-8").'</known-by>
						<gender>'.$party->gender.'</gender>
						<title>'.$party->title.'</title>';
			
			if( $party->birth_date != '' )
			{
				$xml .= '<birth-date>'.date_format($party->birth_date, "Y-m-d").'</birth-date>';
			}
		}
		
		// Used for People and Employers
		//$xml .='	<tags>'.$party->field.'</tags>';

		// Used for Employers only
		if($party->party_type == "Employer")
		{
			$xml .='	<legal-name>'.htmlspecialchars(convert_smart_quotes($party->legal_name), ENT_XML1, "utf-8").'</legal-name>
						<trading-name>'.htmlspecialchars(convert_smart_quotes($party->trading_name), ENT_XML1, "utf-8").'</trading-name>
						<website>'.$party->website.'</website>
						<num-of-employees>'.$party->num_of_employees.'</num-of-employees>
						<company-level>'.$party->company_level.'</company-level>
						<employer-source>'.$party->employer_source.'</employer-source>
						<anzsic-code>'.$party->anzsic_code.'</anzsic-code>
						<description>'.htmlspecialchars(convert_smart_quotes($party->description), ENT_XML1, "utf-8").'</description>
						<industry>'.$party->industry.'</industry>';
		}

		// Login related
		/*
		$xml .= '	<login>'.$party->login.'</login>
					<password>'.$party->password.'</password>
					<password-temporary>'.$party->password_temporary.'</password-temporary>
					<logon-enabled>'.$party->logon_enabled.'</logon-enabled>';
		*/

		// USI Related
		$xml .= '	<usi-number>'.$party->usi_number.'</usi-number>'; 
		
		
		// Generate the XML for Address
		if(isset($party->address_child) && count($party->address_child) > 0)
		{
			$xml .= JRAPartyAddressOperations::createJRAPartyAddressXML( $party->address_child );
		}
		
		// Generate the XML for Contact Details
		if(isset($party->contact_detail_child) && count($party->contact_detail_child) > 0)
		{
			$xml .= JRAPartyContactDetailOperations::createJRAPartyContactDetailXML( $party->contact_detail_child);
		}
		
		// Generate the XML for Identifications
		if(isset($party->identifications) && count($party->identifications) > 0)
		{
			$xml .= JRAPartyIdentificationOperations::createJRAPartyIdentificationXML( $party->identification_child );
		}
		
		// Generate the XML for AVETMISS
		if(isset($party->avetmiss_child))
		{
			$xml .= JRAPartyAvetmissOperations::createJRAPartyAvetmissXML( $party->avetmiss_child );
		}
		
		// Generate the XML for VET Fee-Help
		if(isset($party->vet_free_help_child))
		{
			$xml .= JRAPartyVETFeeHelpOperations::createJRAPartyVETFeeHelpXML( $party->vet_free_help_child);
		}

		// Generate the XML for CRICOS
		/* 05.07.2024 - Temporarily disabled due to Job Ready issue
		if(isset($party->cricos_child))
		{
			$xml .= JRAPartyCricosOperations::createJRAPartyCricosXML( $party->cricos_child);
		}
		*/
		
		if(isset($party->adhoc_child) && count($party->adhoc_child) > 0)
		{
			$xml .= JRAPartyAdhocOperations::createJRAPartyAdhocXML( $party->adhoc_child );
		}
		
		// Generate the XML for Party Group Members
		if(isset($party->party_group_member_child) && count($party->party_group_member_child) > 0)
		{
			$xml .= JRAPartyGroupMemberOperations::createJRAPartyGroupMemberXML( $party->party_group_member_child);
		}
		
		// Close Party
		$xml .= '</party>';
		
		return $xml;
	}


	static function createJRAPartyXMLBasic( $party )
	{
		// XML Header
		$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<party>';

		$xml .= '	<party-type>'.$party->party_type.'</party-type>
					<contact-method>'.$party->contact_method.'</contact-method>';
		
		// Used for People Only
		if($party->party_type == "Person")
		{
			$xml .='	<surname>'.htmlspecialchars(convert_smart_quotes($party->surname), ENT_XML1, "utf-8").'</surname>
						<first-name>'.htmlspecialchars(convert_smart_quotes($party->first_name), ENT_XML1, "utf-8").'</first-name>
						<middle-name>'.htmlspecialchars(convert_smart_quotes($party->middle_name), ENT_XML1, "utf-8").'</middle-name>';
		}
		
		// Generate the XML for Contact Details
		if(isset($party->contact_detail_child) && count($party->contact_detail_child) > 0)
		{
			$xml .= JRAPartyContactDetailOperations::createJRAPartyContactDetailXML( $party->contact_detail_child);
		}
		
		// Close Party
		$xml .= '</party>';
		
		return $xml;
	}
	
	
	static function updateJRAPartyXML( $party )
	{
		// XML Header
		$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<party>';
		
		$xml .= '	<contact-method>'.$party->contact_method.'</contact-method>';
		
		// If there is no value in middle_name, DO NOT update
		// Work-around used to identify when to update the middle name or not
		if($party->middle_name != '')
		{
			$xml .= '<middle-name>'.$party->middle_name.'</middle-name>';
		}
		
		// Used for People and Employers
		//$xml .='	<tags>'.$party->field.'</tags>';
		
		// Used for Employers only
		if($party->party_type == "Employer")
		{
			$xml .='	<legal-name>'.htmlspecialchars(convert_smart_quotes($party->legal_name), ENT_XML1, "utf-8").'</legal-name>
						<trading-name>'.htmlspecialchars(convert_smart_quotes($party->trading_name), ENT_XML1, "utf-8").'</trading-name>
						<website>'.$party->website.'</website>
						<num-of-employees>'.$party->num_of_employees.'</num-of-employees>
						<company-level>'.$party->company_level.'</company-level>
						<employer-source>'.$party->employer_source.'</employer-source>
						<anzsic-code>'.$party->anzsic_code.'</anzsic-code>
						<description>'.htmlspecialchars(convert_smart_quotes($party->description), ENT_XML1, "utf-8").'</description>
						<industry>'.$party->industry.'</industry>';
		}
		
		// Generate the XML for Address
		if(isset($party->address_child) && count($party->address_child) > 0)
		{
			$xml .= JRAPartyAddressOperations::createJRAPartyAddressXML( $party->address_child );
		}
		
		// Generate the XML for Contact Details
		if(isset($party->contact_detail_child) && count($party->contact_detail_child) > 0)
		{
			$xml .= JRAPartyContactDetailOperations::createJRAPartyContactDetailXML( $party->contact_detail_child);
		}
		
		// Generate the XML for Identifications
		if(isset($party->identifications) && count($party->identifications) > 0)
		{
			$xml .= JRAPartyIdentificationOperations::createJRAPartyIdentificationXML( $party->identification_child );
		}
		
		// Generate the XML for AVETMISS
		if(isset($party->avetmiss_child))
		{
			$xml .= JRAPartyAvetmissOperations::createJRAPartyAvetmissXML( $party->avetmiss_child );
		}
		
		// Generate the XML for VET Fee-Help
		if(isset($party->vet_free_help_child))
		{
			$xml .= JRAPartyVETFeeHelpOperations::createJRAPartyVETFeeHelpXML( $party->vet_free_help_child);
		}
		
		// Generate the XML for CRICOS
		/* 05.07.2024 - Temporarily disabled due to Job Ready issue
		if(isset($party->cricos_child))
		{
			$xml .= JRAPartyCricosOperations::createJRAPartyCricosXML( $party->cricos_child);
		}
		*/
		
		if(isset($party->adhoc_child) && count($party->adhoc_child) > 0)
		{
			$xml .= JRAPartyAdhocOperations::createJRAPartyAdhocXML( $party->adhoc_child );
		}
		
		// Generate the XML for Party Group Members
		if(isset($party->party_group_member_child) && count($party->party_group_member_child) > 0)
		{
			$xml .= JRAPartyGroupMemberOperations::createJRAPartyGroupMemberXML( $party->party_group_member_child);
		}
		
		// Close Party
		$xml .= '</party>';
		
		return $xml;
	}

    static function mapJRAPartyXMLObjectToJRAParty( $party_object )
    {
        $party = new JRAParty();

        // Basic party information
        $party->party_identifier = (string) $party_object->{'party-identifier'};
        $party->party_type = (string) $party_object->{'party-type'};
        $party->contact_method = (string) $party_object->{'contact-method'};
        
        // Personal details
        $party->surname = (string) $party_object->{'surname'};
        $party->first_name = (string) $party_object->{'first-name'};
        $party->middle_name = (string) $party_object->{'middle-name'};
        $party->known_by = (string) $party_object->{'known-by'};
        $party->birth_date = (string) $party_object->{'birth-date'};
        $party->gender = (string) $party_object->{'gender'};
        $party->title = (string) $party_object->{'title'};
        
        // Login details
        $party->login = (string) $party_object->{'login'};
        $party->password_temporary = (string) $party_object->{'password-temporary'};
        $party->logon_enabled = (string) $party_object->{'logon-enabled'} === 'true';
        
        // USI
        $party->usi_number = (string) $party_object->{'usi-number'};
        
        // Marketing
        $party->do_not_market = (string) $party_object->{'do-not-market'} === 'true';
        
        // Addresses
        if (isset($party_object->{'addresses'}->{'address'})) {
            $addresses = array();
            foreach ($party_object->{'addresses'}->{'address'} as $address_xml) {
                $address = new JRAPartyAddress();
                $address->primary = (string) $address_xml->{'primary'};
                $address->street_address1 = (string) $address_xml->{'street-address1'};
                $address->street_address2 = (string) $address_xml->{'street-address2'};
                $address->suburb = (string) $address_xml->{'suburb'};
                $address->state = (string) $address_xml->{'state'};
                $address->post_code = (string) $address_xml->{'post-code'};
                $address->country = (string) $address_xml->{'country'};
                $address->location = (string) $address_xml->{'location'};
                $addresses[] = $address;
            }
            $party->address_child = $addresses;
        }
        
        // Contact details
        if (isset($party_object->{'contact-details'}->{'contact-detail'})) {
            $contact_details = array();
            $contact_detail_array = $party_object->{'contact-details'}->{'contact-detail'};
            
            // Handle single or multiple contact details
            if (!is_array($contact_detail_array)) {
                $contact_detail_array = array($contact_detail_array);
            }
            
            foreach ($contact_detail_array as $contact_xml) {
                $contact = new JRAPartyContactDetail();
                $contact->primary = (string) $contact_xml->{'primary'};
                $contact->contact_type = (string) $contact_xml->{'contact-type'};
                $contact->value = (string) $contact_xml->{'value'};
                $contact_details[] = $contact;
            }
            $party->contact_detail_child = $contact_details;
        }
        
        // Ad-hoc fields
        if (isset($party_object->{'ad-hoc-fields'}->{'ad-hoc-field'})) {
            $adhoc_fields = array();
            $adhoc_array = $party_object->{'ad-hoc-fields'}->{'ad-hoc-field'};
            
            // Handle single or multiple ad-hoc fields
            if (!is_array($adhoc_array)) {
                $adhoc_array = array($adhoc_array);
            }
            
            foreach ($adhoc_array as $adhoc_xml) {
                $adhoc = new JRAPartyAdhoc();
                $adhoc->name = (string) $adhoc_xml->{'name'};
                $adhoc->value = (string) $adhoc_xml->{'value'};
                $adhoc_fields[] = $adhoc;
            }
            $party->adhoc_child = $adhoc_fields;
        }
        
        return $party;
    }
}