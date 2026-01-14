<?php
/*
 * JRAPartyContactDetail class + JRAPartyContactDetailOperations
 * Created by: James Witika
 * Company: Smooth Developments
 */

if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class JRAPartyContactDetail
{
	var $primary; // boolean If true, this email address or phone number will be the party’s primary.
	var $value; // string Phone	number	or	email	address. The value must validate according to the format of a phone number or email address, depending on contact-type.
	var $contact_type; // reference “Phone”, “Mobile”, “Fax” or “Email”
	var $location; // reference Contact	location (“Home” or “Work”)
	
	function __construct()
	{
		$this->primary = false; // boolean If true, this email address or phone number will be the party’s	primary.
		$this->value = ''; // string Phone number or email address. The value must validate according to the format of a phone number or email address, depending	on	contact-type.
		$this->contact_type = 'Phone'; // reference “Phone”, “Mobile”, "Fax” or “Email”
		$this->location = 'Home'; // reference Contact location (“Home” or “Work”)
	}
}

class JRAPartyContactDetailOperations
{
	function __construct()
	{
		
	}
	
	
	static function createJRAPartyContactDetailXML( $contact_details )
	{
		$xml = '<contact-details>';
		
		foreach($contact_details as $contact_detail)
		{
			
			$xml .= '	<contact-detail>';
			
			if($contact_detail->primary != '')
			{
				$xml .= '		<primary>true</primary>';
			}
			
			$xml .= '		<value>'.$contact_detail->value.'</value>
							<contact-type>'.$contact_detail->contact_type.'</contact-type>
							<location>'.$contact_detail->location.'</location>
						</contact-detail>';
		}
		
		$xml .= '</contact-details>';
		
		return $xml;
		
	}
}