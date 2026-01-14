<?php
add_filter('woocommerce_paypal_payments_order_line_item_name', 'custom_modify_product_name', 10,3);

function custom_modify_product_name($product_name, $order_item_id, $order_id)
{
	// Load the Order
	$order = wc_get_order( $order_id );
	
	// Check if the Order was found
	if ( $order )
	{
		// Get all items from the order
		$items = $order->get_items();
		
		// Loop through the items to find the one with the specified item ID
		foreach ( $items as $item_id => $item )
		{
			if ( $item_id == $order_item_id )
			{
				$item_data = $item->get_data();
				$meta_data = $item->get_meta_data();
				
				$entry_id = $meta_data[0]->value['_gravity_form_linked_entry_id'];
				break;
			}
		}
	}
	else
	{
		echo "Order not found";
	}
	
	if(isset($entry_id) && $entry_id > 0)
	{
		// Load the entry
		$form_data = GFAPI::get_entry( $entry_id );
		$form_id = $form_data['form_id'];
		
		switch($form_id)
		{
			case PRE_APPRENTICE_APPLICATION_FORM:
				$course_scope_code = $form_data['77'];
				$course_number = $form_data['78'];
				$name = $form_data['9'] . ' ' . $form_data['8'];
				$payment_option = $form_data['98'];
				$payment_option = strstr($payment_option,"|",true);
				
				$jrd =JobReadyDateOperations::loadJobReadyDateByCourseNumber($course_number);
				$start_date = $jrd->start_date_clean;
				
				$product_name .= " (Student Name: $name | Course Name: $payment_option | Course Number: $course_number)";
				
				break;
				
			case SHORT_COURSE_APPLICATION_FORM_NON_ACCREDITED :
				
				$new_item = array();
				$course_option_array = explode("|", $form_data['26']);
				$course_option = $course_option_array[0];
				$course_scope_code = $form_data['22'];
				$course_number = $form_data['23'];
				$name = $form_data['9'] . ' ' . $form_data['8'];
				
				$product_name .= " (Student Name: $name | Course Name: $course_option | Course Number: $course_number)";
				
				break;
				
				
			case SHORT_COURSE_APPLICATION_FORM_ACCREDITED :
				
				$new_item = array();
				$course_scope_code = $form_data['69'];
				$course_number = $form_data['70'];
				$name = $form_data['9'] . ' ' . $form_data['8'];
				
				$product_name .= " (Student Name: $name | Course Name: $payment_option | Course Number: $course_number)";
				
				break;
				
				
			case SHORT_COURSE_APPLICATION_NECCLV004 :
				
				$new_item = array();
				$course_scope_code = $form_data['69'];
				$course_number = $form_data['70'];
				$name = $form_data['9'] . ' ' . $form_data['8'];
				$course_name = $course_number;
				
				$product_name .= " (Student Name: $name | Course Number: $course_number)";
				
				break;
		}
	}
	
	return $product_name;
}