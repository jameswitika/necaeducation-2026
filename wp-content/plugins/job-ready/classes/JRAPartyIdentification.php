<?php
/*
 * JRAPartyIdentification class + JRAPartyIdentificationOperations class
 * Created by: James Witika
 * Company: Smooth Developments
 */

if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class JRAPartyIdentification
{
	var $identification_type; // reference Mandatory
	var $identification_number; //string Mandatory
	
	function __construct()
	{
		$this->identification_type = ''; // reference Mandatory
		$this->identification_number = ''; //string Mandatory
	}
}


class JRAPartyIdentificationOperations
{
	function __construct()
	{

	}
	

	// Create XML layout for all Party Identification
	static function createJRAPartyIdentificationXML( $identifications )
	{
		$xml = '<identifications>';
		
		foreach($identifications as $identification)
		{
			$xml .= '<identification>
							<identification-type>'.$identification->identification_type.'</identification-type>
							<identification-number>'.$identification->identification_number.'</identification-number>
					 </identification>';
		}
		
		$xml .= '</identifications>';
		
		return $xml;
	}
}