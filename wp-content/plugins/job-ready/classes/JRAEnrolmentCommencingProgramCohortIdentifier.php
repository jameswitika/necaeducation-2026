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

class JRAEnrolmentCommencingProgramCohortIdentifier
{
	var $code; 				// reference
	
	function __construct()
	{
		$this->code = ''; 				// reference
	}
}


class JRAEnrolmentCommencingProgramCohortIdentifierOperations
{
	function __construct()
	{
		
	}
	
	
	// Create XML layout for all Party Adhoc
	static function createJRAEnrolmentCommencingProgramCohortIdentifierXML( $cohorts )
	{
		$xml = '<commencing-program-cohort-identifiers>';
		
		foreach($cohorts as $cohort)
		{
			$xml .= '<commencing-program-cohort-identifier>
						<code>' . $cohort->code . '</code>
					 </commencing-program-cohort-identifier>';
		}
		
		$xml .= '</commencing-program-cohort-identifiers>';
		
		return $xml;
	}
}