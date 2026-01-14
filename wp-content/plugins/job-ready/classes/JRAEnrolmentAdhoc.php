<?php
/*
 * JRAEnrolmentAdhoc class + JRAEnrolmentAdhocOperations class
 * Created by: James Witika
 * Company: Smooth Developments
 */

if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class JRAEnrolmentAdhoc
{
	var $name; 				// reference
	var $value; 			// string
	
	function __construct()
	{
		$this->name = ''; 				// reference
		$this->value = ''; 				// string
	}
}


class JRAEnrolmentAdhocOperations
{
	function __construct()
	{

	}
	

	// Create XML layout for all Party Adhoc
	static function createJRAEnrolmentAdhocXML( $adhoc_fields )
	{
		$xml = '<ad-hoc-fields>';

		foreach($adhoc_fields as $adhoc_field)
		{
			$xml .= '<ad-hoc-field>
						<name>' . $adhoc_field->name . '</name>
						<value>' . htmlspecialchars($adhoc_field->value, ENT_XML1) . '</value>
					 </ad-hoc-field>';
		}
		
		$xml .= '</ad-hoc-fields>';
		
		return $xml;
	}
}