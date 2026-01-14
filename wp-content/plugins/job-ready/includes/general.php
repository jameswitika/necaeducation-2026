<?php 

if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

// Convert XML to Object
function xmlToObject($xml)
{
	$result = simplexml_load_string($xml);
	//$result = simplexml_load_string(utf8_encode(html_entity_decode($xml)));
	// This was breaking on HTML entities in "web-description"
	return $result;
}

// Convert XML to Array
function xmlToArray($xml)
{
	$result = new stdClass();
	
	$parser = xml_parser_create();
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($parser, $xml, $result->values, $result->tags);
	xml_parser_free($parser);
	
	return $result;
}


// Convert Smart Quotes
function convert_smart_quotes($string)
{
	$search = array('”', '“', '’');
	$replace = array('"', '"', "'");
	return str_replace($search, $replace, $string);
}


// Get Payment Details by Order ID
function getPaymentByOrderID($order_id)
{
	// Get an instance of the WC_Order object
	$order = wc_get_order( $order_id );
	$order_data = $order->get_data(); // The Order data
	
	$order_id = $order_data['id'];
// 	$order_parent_id = $order_data['parent_id'];
// 	$order_status = $order_data['status'];
// 	$order_payment_method = $order_data['payment_method'];
// 	$order_payment_method_title = $order_data['payment_method_title'];
	
	## Creation and modified WC_DateTime Object date string ##
	
	// Using a formated date ( with php date() function as method)
	$order_date_created = $order_data['date_created']->date('Y-m-d H:i:s');
	$order_date_modified = $order_data['date_modified']->date('Y-m-d H:i:s');
	
	// Using a timestamp ( with php getTimestamp() function as method)
	$order_timestamp_created = $order_data['date_created']->getTimestamp();
	$order_timestamp_modified = $order_data['date_modified']->getTimestamp();
	
// 	$order_total = $order_data['cart_tax'];
// 	$order_total_tax = $order_data['total_tax'];
// 	$order_customer_id = $order_data['customer_id'];
	
	## BILLING INFORMATION:
// 	$order_billing_first_name = $order_data['billing']['first_name'];
// 	$order_billing_last_name = $order_data['billing']['last_name'];
// 	$order_billing_company = $order_data['billing']['company'];
// 	$order_billing_address_1 = $order_data['billing']['address_1'];
// 	$order_billing_address_2 = $order_data['billing']['address_2'];
// 	$order_billing_city = $order_data['billing']['city'];
// 	$order_billing_state = $order_data['billing']['state'];
// 	$order_billing_postcode = $order_data['billing']['postcode'];
// 	$order_billing_country = $order_data['billing']['country'];
// 	$order_billing_email = $order_data['billing']['email'];
// 	$order_billing_phone = $order_data['billing']['phone'];
	
	$payment = new stdClass();
	$payment->order_id = $order_data['id'];
	$payment->transaction_id = $order->get_transaction_id();
	
	return $payment;
}