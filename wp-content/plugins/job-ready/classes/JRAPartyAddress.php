<?php
/*
 * JRAPartyAddress class + JRAPartyAddressOperations class
 * Created by: James Witika
 * Company: Smooth Developments
 */

if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class JRAPartyAddress
{
	var $primary; 			// Boolean If true, this will be the party’s primary address.
	var $street_address1;	// String Mandatory - On Pull request, Building Name and Postal Delivery box is returned
	var $street_address2;	// String Optional
	var $unit_type; 		// String Read Only	– results from JobReady Plus address verification - e.g. Flat, Unit, Suite
	var $unit_number; 		// String Read Only	– results from JobReady Plus address verification
	var $street_number; 	// String Read Only	– results from JobReady Plus address verification
	var $street_name; 		// String Read Only	– results from JobReady Plus address verification
	var $street_suffix; 	// String Read Only	– results from JobReady Plus address verification - e.g. West, North
	var $street_type; 		// String Read Only	– results from JobReady Plus address verification - e.g. RD, ST, CCT
	var $suburb; 			// String Mandatory
	var $post_code; 		// String Mandatory if Country is Australia
	var $state; 			// reference Mandatory if Country is Australia
	var $country; 			// reference
	var $location; 			// reference Contact location (“Home” or “Work”)
	
	function __construct()
	{
		$this->primary = ''; 			// Boolean If true, this will be the party’s primary address.
		$this->street_address1 = '';	// String Mandatory
		$this->street_address2 = '';	// String Optional
		$this->unit_number = '';		// String - Used for Detailed Address
		$this->street_number = '';		// String - Used for Detailed Address
		$this->street_name = '';		// String - Used for Detailed Address
		$this->street_suffix = '';		// String - Used for Detailed Address
		$this->street_type = '';		// String - Used for Detailed Address
		$this->suburb = ''; 			// String Mandatory
		$this->post_code = ''; 			// String Mandatory if Country is Australia
		$this->state = ''; 				// reference Mandatory if Country is Australia
		$this->country = ''; 			// reference
		$this->location = 'Home';		// reference Contact location (“Home” or “Work”)
	}
}


class JRAPartyAddressOperations
{
	function __construct()
	{

	}
	

	// Create XML layout for all Party Addresses
	static function createJRAPartyAddressXML( $addresses )
	{
		$xml = '<addresses>';
		
		foreach($addresses as $address)
		{
			$xml .= '<address>';
			
			if($address->primary == 'true')
			{
				$xml .= '		<primary>'.$address->primary.'</primary>';
			}
			
			if(trim($address->street_address1) != '')
			{
				$xml .= '	<street-address1>'.htmlspecialchars($address->street_address1, ENT_XML1).'</street-address1>';
				
				if(trim($address->street_address2) != '')
				{
					$xml .= '<street-address2>'.htmlspecialchars($address->street_address2, ENT_XML1).'</street-address2>';
				}
			}
			else
			{
				$xml .= '	<street-number>'.htmlspecialchars($address->street_number, ENT_XML1).'</street-number>';
				$xml .= '	<street-name>'.htmlspecialchars($address->street_name, ENT_XML1).'</street-name>';
			}
			
			$xml .= '		<suburb>'.$address->suburb.'</suburb>
							<post-code>'.$address->post_code.'</post-code>
							<state>'.$address->state.'</state>
							<country>'.$address->country.'</country>
							<location>'.$address->location.'</location>
						</address>';
		}
		
		$xml .= '</addresses>';
		
		return $xml;
	}
}