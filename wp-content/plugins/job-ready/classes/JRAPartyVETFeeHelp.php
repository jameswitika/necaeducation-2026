<?php
/*
 * JRAPartyVETFeeHelp class + JRAPartyVETFeeHelpOperations class
 * Created by: James Witika
 * Company: Smooth Developments
 */

if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class JRAPartyVETFeeHelp
{
	var $fee_type; 				// reference
	var $chessn; 				// integer
	var $tax_file_number; 		// integer
	var $tfn_status; 			// reference
	var $visa; 					// reference
	var $year_of_arrival; 		// reference
	
	function __construct()
	{
		$this->fee_type = ''; 				// reference
		$this->chessn = ''; 				// integer
		$this->tax_file_number = ''; 		// integer
		$this->tfn_status = ''; 			// reference
		$this->visa = ''; 					// reference
		$this->year_of_arrival = ''; 		// reference
	}
}


class JRAPartyVETFeeHelpOperations
{
	function __construct()
	{

	}
	

	// Create XML layout for all Party Avetmiss
	static function createJRAPartyVETFeeHelpXML( $vet_fee_help )
	{
		$xml = '<party-vet-fee-help>';
		
		if($vet_fee_help->fee_type != '')
			$xml .= '	<fee-type>'.$vet_fee_help->fee_type.'</fee-type>';

		if($vet_fee_help->chessn!= '')
			$xml .= '	<chessn>'.$vet_fee_help->chessn.'</chessn>';

		if($vet_fee_help->tax_file_number!= '')
			$xml .= '	<tax-file-number>'.$vet_fee_help->tax_file_number.'</tax-file-number>';

		if($vet_fee_help->tfn_status!= '')
			$xml .= '	<tfn-status>'.$vet_fee_help->tfn_status.'</tfn-status>';

		if($vet_fee_help->visa!= '')
			$xml .= '	<visa>'.$vet_fee_help->visa.'</visa>';

		if($vet_fee_help->year_of_arrival!= '')
			$xml .= '	<year-of-arrival>'.$vet_fee_help->year_of_arrival.'</year-of-arrival>';
		
		$xml .= '</party-vet-fee-help>';
		
		return $xml;
	}
}