<?php
/*
 * JRAPayment class + JRAPaymentOperations class
 * Created by: James Witika
 * Company: Smooth Developments
 */

if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class JRAPayment
{
	var $invoice_number; 			// string 			Read_only
	var $payment_date; 				// date 			Automatically defaulted to created date for new records unless explicitly specified
	var $party_identifier;			// reference 		Mandatory _ The party identifier of a valid party.
	var $type; 						// reference		Mandatory _ Options: (Payment, Payroll Deduct, CentrePay)
	var $description;				// string
	var $source;					// reference		Mandatory _ Options: (Cash, Cheque, Credit Card, etc.)
	var $amount_total;				// float			Read_only. Total payment amount. Payment amounts are created on the Payment Item resource
	var $location;					// reference;
	var $enabled;					// boolean			Mandatory _ Options: (true, false)
	var $created_on;				// datetime			Read_only
	var $updated_on;				// datetime			Read_only
	var $payment_items;				// array			Array of JRAPaymentItems
	
	function __construct()
	{
		$this->invoice_number = '';
		$this->payment_date = '';
		$this->party_identifier = '';
		$this->type = '';
		$this->description = '';
		$this->source = '';
		$this->amount_total = 0;
		$this->location = '';
		$this->enabled = '';
		$this->payment_items = array();
	}
}

class JRAPaymentItem
{
	var $payment_amount; 			// float 			Mandatory. Amount being paid in the this transaction for the line item. Can be the full or partial amount of rthe line item. Note: Labelled as "Due" in the application
	
	function __construct()
	{
		$this->payment_amount = 0;
	}
}

class JRAPaymentOperations
{
	function __construct()
	{
		
	}
	
	
	static function createJRAPayment( $party_id, $invoice_number, $xml )
	{
		global $jr_api_headers;
		
		$webservice = '/webservice/payments';
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

			return $result_object;
		}
		catch (Exception $e)
		{
			$error = $e->getMessage();
			send_error_email($url, $method, $xml, $error);
			return false;
		}
	}
	
	
	static function createJRAPaymentXML( $payment )
	{

		// XML Header
		$xml = '<?xml	version="1.0"	encoding="UTF-8"?>
				<payment>';

		$xml .= '	<invoice-number>' . $payment->invoice_number . '</invoice-number>';
		$xml .= '	<party-identifier>' . $payment->party_identifier . '</party-identifier>';
		$xml .= '	<type>' . $payment->type . '</type>';
		$xml .= '	<description>' . $payment->description . '</description>';
		$xml .= '	<source>' . $payment->source . '</source>';
		$xml .= '	<location>' . $payment->location . '</location>';
		$xml .= '	<enabled>' . $payment->enabled . '</enabled>';
		
		if(count($payment->payment_items) > 0)
		{
			$xml .= '	<payment-items>';
			foreach($payment->payment_items as $payment_item)
			{
				$xml .= '	<payment-item>';
				$xml .= '		<amount>'.$payment_item->payment_amount.'</amount>';
				$xml .= '	</payment-item>';
			}
			$xml .= '	</payment-items>';
		}
		
		// Close Prospect
		$xml .= '</payment>';
		
		return $xml;
	}
}