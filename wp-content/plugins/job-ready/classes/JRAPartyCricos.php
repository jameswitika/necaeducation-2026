<?php
/*
 * JRAPartyCricos class + JRAPartyCricosOperations class
 * Created by: James Witika
 * Company: Smooth Developments
 */

if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class JRAPartyCricos
{
	var $visa_status; 					// reference
	var $visa_type; 					// reference
	var $visa_subtype; 					// reference
	var $visa_education_sector; 		// reference
	var $svp_assessment_level; 			// reference
	var $english_test_type; 			// reference
	var $english_test_delivery_method;	// reference
	var $english_test_score; 			// string
	var $english_test_date; 			// date
	var $agent_party_identifier; 		// reference Party identifier of an agent
	var $citizenship_status; 			// reference Available from Feb	17.
	var $nationality; 					// reference A country
	var $country_of_birth; 				// reference A country
	var $country_of_passport; 			// reference A country
	var $oshc; 							// Boolean Whether to enable OSHC
	var $oshc_provider; 				// reference
	var $oshc_member_number; 			// string
	var $oshc_expiry_date; 				// date
	var $created_on; 					// datetime Read_only
	var $updated_on; 					// datetime Read_only
	
	function __construct()
	{
		$this->visa_status = ''; 					// reference
		$this->visa_type = ''; 						// reference
		$this->visa_subtype = ''; 					// reference
		$this->visa_education_sector = ''; 			// reference
		$this->svp_assessment_level = ''; 			// reference
		$this->english_test_type = ''; 				// reference
		$this->english_test_delivery_method = ''; 	// reference
		$this->english_test_score = ''; 			// string
		$this->english_test_date = ''; 				// date
		$this->agent_party_identifier = ''; 		// reference Party identifier of an agent
		$this->citizenship_status = ''; 			// reference Available from Feb	17.
		$this->nationality = ''; 					// reference A country
		$this->country_of_birth = ''; 				// reference A country
		$this->country_of_passport = ''; 			// reference A country
		$this->oshc = false; 						// Boolean Whether to enable OSHC
		$this->oshc_provider = ''; 					// reference
		$this->oshc_member_number = ''; 			// string
		$this->oshc_expiry_date = ''; 				// date
	}
}


class JRAPartyCricosOperations
{
	function __construct()
	{

	}
	

	// Create XML layout for all Party Avetmiss
	static function createJRAPartyCricosXML( $cricos )
	{
		$xml = '<cricos>';
		
		if( $cricos->visa_status != '')
			$xml .= '	<visa-status>'.$cricos->visa_status.'</visa-status>';
		if( $cricos->visa_type!= '')
			$xml .= '	<visa-type>'.$cricos->visa_type.'</visa-type>';
		if( $cricos->visa_subtype!= '')
			$xml .= '	<visa-subtype>'.$cricos->visa_subtype.'</visa-subtype>';
		if( $cricos->visa_education_sector!= '')
			$xml .= '	<visa-education-sector>'.$cricos->visa_education_sector.'</visa-education-sector>';
		if( $cricos->svp_assessment_level!= '')
			$xml .= '	<svp-assessment-level>'.$cricos->svp_assessment_level.'</svp-assessment-level>';
		if( $cricos->english_test_type!= '')
			$xml .= '	<english-test-type>'.$cricos->english_test_type.'</english-test-type>';
		if( $cricos->english_test_delivery_method!= '')
			$xml .= '	<english-test-delivery-method>'.$cricos->english_test_delivery_method.'</english-test-delivery-method>';
		if( $cricos->english_test_score!= '')
			$xml .= '	<english-test-score>'.$cricos->english_test_score.'</english-test-score>';
		if( $cricos->english_test_date!= '')
			$xml .= '	<english-test-date>'.$cricos->english_test_date.'</english-test-date>';
		if( $cricos->agent_party_identifier!= '')
			$xml .= '	<agent-party-identifier>'.$cricos->agent_party_identifier.'</agent-party-identifier>';
		if( $cricos->citizenship_status!= '')
			$xml .= '	<citizenship-status>'.$cricos->citizenship_status.'</citizenship-status>';
		if( $cricos->nationality!= '')
			$xml .= '	<nationality>'.$cricos->nationality.'</nationality>';
		if( $cricos->country_of_birth!= '')
			$xml .= '	<country-of-birth>'.$cricos->country_of_birth.'</country-of-birth>';
		if( $cricos->country_of_passport!= '')
			$xml .= '	<country-of-passport>'.$cricos->country_of_passport.'</country-of-passport>';
		if( $cricos->oshc!= '')
			$xml .= '	<oshc>'.$cricos->oshc.'</oshc>';
		if( $cricos->oshc_provider!= '')
			$xml .= '	<oshc-provider>'.$cricos->oshc_provider.'</oshc-provider>';
		if( $cricos->oshc_member_number!= '')
			$xml .= '	<oshc-member-number>'.$cricos->oshc_member_number.'</oshc-member-number>';
		if( $cricos->oshc_expiry_date!= '')
			$xml .= '	<oshc-expiry-date>'.$cricos->oshc_expiry_date.'</oshc-expiry-date>';
		
		$xml .= '</cricos>';
		
		return $xml;
	}
}