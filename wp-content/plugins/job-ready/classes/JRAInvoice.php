<?php
/*
 * JRAInvoice class + JRAInvoiceOperations class
 * Created by: James Witika
 * Company: Smooth Developments
 */

if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class JRAInvoice
{
	var $invoice_number; 			// string		Automatically generated for new records unless explicitly specified
	var $invoice_date; 				// date			Automatically defaulted to created date for new records unless explicitly specified
	var $internal_reference;		// string		Used to set an invoice ID from an integrated finance system
	var $purchase_order_identifier;	// string		Used to	track a related purchase order ID for the invoice
	var $party_identifier;			// reference	Mandatory - The party identifier of a valid party.
	var $delivery_method;			// string		Mandatory – Options: (Mail, Email)
	var $description;				// string
	var $category;					// reference
	var $payment_term_days;			// integer
	var $status;					// reference	Mandatory - Options: (Active, Cancelled)
	var $created_on;				// datetime		Read-only
	var $updated_on;				// datetime		Read-only
	var $invoice_items;				// Invoice Item child resource

	function __construct()
	{
		$this->invoice_number= '';
		$this->invoice_date= '';
		$this->internal_reference= '';
		$this->purchase_order_identifier= '';
		$this->party_identifier= '';
		$this->delivery_method= '';
		$this->description= '';
		$this->category= '';
		$this->payment_term_days= '';
		$this->status= '';
		$this->created_on= '';
		$this->updated_on= '';
		$this->invoice_items = array();
	}
}

class JRAInvoiceItem
{
	var $id; 				// integer		Automatically generated – cannot be assigned
	var $quantity; 			// integer		Mandatory – defaults to 1 if not specified
	var $ledger_code; 		// reference	Mandatory - A valid ledger code
	var $description; 		// String
	var $job_account; 		// reference	A valid job code
	var $amount; 			// float		Mandatory
	var $tax_treatment; 	// Reference	A valid tax treatment code e.g. GST, FRE
	var $tax_amount; 		// float		Read-only
	var $sub_total; 		// float		Read-only
	
	function __construct()
	{
		$this->invoice_amount = 0;
	}
}

class JRAInvoiceOperations
{
	function __construct()
	{
		
	}
	
	
	static function loadInvoiceByEnrolmentID( $enrolment_id )
	{
		global $jr_api_headers;
		$method = "GET";
		$webservice = '/webservice/invoices/?enrolment_identifier=' . $enrolment_id;
		$url = JR_API_SERVER . $webservice;
		
		// Call the Job Ready API
		try {
			//make POST request
			$response = wp_remote_request( $url, array(	'method' 	=> $method,
					'headers' 	=> $jr_api_headers,
					'timeout' 	=> 500 )
					);
			
			// Get the response
			$result = wp_remote_retrieve_body( $response );
			
			$invoices = array();
			
			// Convert the XML to an Object
			$jra_invoices = xmlToObject($result);
			
			foreach($jra_invoices as $jra_invoice)
			{
				
				$invoice = new JRAInvoice();
				$invoice->invoice_number = (string) $jra_invoice->{'invoice-number'};
				$invoice->invoice_date = (string) $jra_invoice->{'invoice-date'};
				$invoice->internal_reference = (string) $jra_invoice->{'internal-reference'};
				$invoice->purchase_order_identifier = (string) $jra_invoice->{'purchase-order-identifier'};
				$invoice->party_identifier = (string) $jra_invoice->{'party-identifier'};
				$invoice->delivery_method = (string) $jra_invoice->{'delivery-method'};
				$invoice->description = (string) $jra_invoice->{'description'};
				$invoice->category = (string) $jra_invoice->{'category'};
				$invoice->payment_term_days = (int) $jra_invoice->{'payment-term-days'};
				$invoice->status = (string) $jra_invoice->{'status'};
				$invoice->created_on = (string) $jra_invoice->{'created-on'};
				$invoice->updated_on = (string) $jra_invoice->{'updated-on'};
				
				// If the course date has "invoice-options" then there are pricing options available.
				if(isset($jra_course->{'invoice-items'}))
				{
					$jra_invoice_items = $jra_invoice->{'invoice-items'};
					
					// Loop through all invoice options
					foreach($jra_invoice_items as $jra_invoice_item)
					{
						// Creates a generic class for invoice option
						$invoice_option = new JRAInvoiceItem();
						$invoice_option->id = (int) $jra_invoice_item->id;
						$invoice_option->quantity = (int) $jra_invoice_item->quantity;
						$invoice_option->id = (string) $jra_invoice_item->{'ledger-code'};
						$invoice_option->id = (string) $jra_invoice_item->description;
						$invoice_option->id = (string) $jra_invoice_item->{'job-account'};
						$invoice_option->id = (float) $jra_invoice_item->amount;
						$invoice_option->id = (string) $jra_invoice_item->{'tax-treatment'};
						$invoice_option->id = (float) $jra_invoice_item->{'tax-amount'};
						$invoice_option->id = (float) $jra_invoice_item->{'sub-total'};
						
						// Add invoice option to the invoice options array
						array_push($invoice->invoice_items, $invoice_item);
					}
				}
				array_push($invoices, $invoice);
			}
			
			return $invoices;
		}
		catch (Exception $e)
		{
			$error = 'JRAInvoice > loadInvoiceByEnrolmentID() exception error: ' . $e->getMessage();
			
			// Send an email to the administrator
			$subject = 'NECA Education + Careers - loadInvoiceByEnrolmentID Error';
			$body_content = "The following error occurs on " . date('d-m-Y') . " while trying to load an Invoice from JobReady:<br/><br/>" . $error . "<br/><br/>Please contact the website administrator.";
			
			echo $body_content;
			wp_mail('james@smoothdevelopments.com.au', $subject, $body_content, $headers = '');
			
			return false;
		}
	}
	
	
	static function loadInvoiceByInvoiceID( $invoice_id )
	{
		global $jr_api_headers;
		$method = "GET";
		$webservice = '/webservice/parties/PA05685/invoices';
		$url = JR_API_SERVER . $webservice;
		
		// Call the Job Ready API
		try {
			//make POST request
			$response = wp_remote_request( $url, array(	'method' 	=> $method,
														'headers' 	=> $jr_api_headers,
														'timeout' 	=> 500 )
										);
			
			// Get the response
			$result = wp_remote_retrieve_body( $response );
			
			$invoices = array();
			
			// Convert the XML to an Object
			$jra_invoices = xmlToObject($result);
			
			foreach($jra_invoices as $jra_invoice)
			{
				$invoice = new JRAInvoice();
				$invoice->invoice_number = (string) $jra_invoice->{'invoice-number'};
				$invoice->invoice_date = (string) $jra_invoice->{'invoice-date'};
				$invoice->internal_reference = (string) $jra_invoice->{'internal-reference'};
				$invoice->purchase_order_identifier = (string) $jra_invoice->{'purchase-order-identifier'};
				$invoice->party_identifier = (string) $jra_invoice->{'party-identifier'};
				$invoice->delivery_method = (string) $jra_invoice->{'delivery-method'};
				$invoice->description = (string) $jra_invoice->{'description'};
				$invoice->category = (string) $jra_invoice->{'category'};
				$invoice->payment_term_days = (int) $jra_invoice->{'payment-term-days'};
				$invoice->status = (string) $jra_invoice->{'status'};
				$invoice->created_on = (string) $jra_invoice->{'created-on'};
				$invoice->updated_on = (string) $jra_invoice->{'updated-on'};
				
				// If the course date has "invoice-options" then there are pricing options available.
				if(isset($jra_course->{'invoice-items'}))
				{
					$jra_invoice_items = $jra_invoice->{'invoice-items'};
					
					// Loop through all invoice options
					foreach($jra_invoice_items as $jra_invoice_item)
					{
						// Creates a generic class for invoice option
						$invoice_option = new JRAInvoiceItem();
						$invoice_option->id = (int) $jra_invoice_item->id;
						$invoice_option->quantity = (int) $jra_invoice_item->quantity;
						$invoice_option->id = (string) $jra_invoice_item->{'ledger-code'};
						$invoice_option->id = (string) $jra_invoice_item->description;
						$invoice_option->id = (string) $jra_invoice_item->{'job-account'};
						$invoice_option->id = (float) $jra_invoice_item->amount;
						$invoice_option->id = (string) $jra_invoice_item->{'tax-treatment'};
						$invoice_option->id = (float) $jra_invoice_item->{'tax-amount'};
						$invoice_option->id = (float) $jra_invoice_item->{'sub-total'};
						
						// Add invoice option to the invoice options array
						array_push($invoice->invoice_items, $invoice_item);
					}
				}
				array_push($invoices, $invoice);
			}
			
			return $invoices;
		}
		catch (Exception $e)
		{
			$error = 'JRAInvoice > loadInvoiceByEnrolmentID() exception error: ' . $e->getMessage();
			
			// Send an email to the administrator
			$subject = 'NECA Education + Careers - loadInvoiceByEnrolmentID Error';
			$body_content = "The following error occurs on " . date('d-m-Y') . " while trying to load an Invoice from JobReady:<br/><br/>" . $error . "<br/><br/>Please contact the website administrator.";
			
			echo $body_content;
			wp_mail('james@smoothdevelopments.com.au', $subject, $body_content, $headers = '');
			
			return false;
		}
	}
	
	
}