<?php
if (! defined ( 'JR_ROOT_FILE' )) {
	header ( 'HTTP/1.0 403 Forbidden' );
	exit ();
}

// Used to avoid timeouts
set_time_limit ( 0 );
ignore_user_abort ( 1 );

/*
 * GRAVITY FORM - Post submission processing
 * Performs this process after the "2017 Apprentice Application Form" has been submitted
 * The 2017 Apprentice Application Form does not have a charge and therefore it is
 * captured in JobReady as soon as the form is submitted.
 * Other forms with fees must go through the WooCommerce payment process before they
 * are captured in JobReady.
 */

/*
 * Special note about dynamic checkboxes - James Witika
 * If you have dynamically populated checkboxes and multi items are selected
 * you need to ensure you have 'blank' placeholders in the form (via ADMIN) because
 * the multiple items will not be selected as you navigate between pages
 * This issue was identified with Gravity Forms v2.23
 */
function wpdocs_set_html_mail_content_type() {
	return 'text/html';
}

// Job Ready related error email notifications
function send_error_email($url, $method, $xml, $error, $raw_error = '') {
	add_filter ( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
	
	$recipient = 'james@smoothdevelopments.com.au';
	
	// Send email notification to admin advising of error
	$body_content = 'An error occured with the JobReady API on ' . current_time ( 'd-m-Y H:i:s' ) . '<br/>';
	$body_content .= 'URL: ' . $url . '<br/>';
	$body_content .= 'Method: ' . $method . '<br/><br/>';
	$body_content .= 'XML: <pre>' . htmlentities ( $xml ) . '</pre><br/><br/>';
	$body_content .= 'Error: ' . $error . '<br/><br/>';
	$body_content .= 'Raw Response: <pre>';
	$body_content .= var_export($raw_error, true);
	$body_content .= "</pre><br/><br/>";
	
	$subject = 'JobReady API Error - ' . $url;
	
	// Output error
	if (JR_DEBUG_MODE) {
		echo $body_content;
	}
	
	// Send email
	wp_mail ( $recipient, $subject, $body_content, $headers = '' );
	
	// Reset content-type to avoid conflicts -- https://core.trac.wordpress.org/ticket/23578
	remove_filter ( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
}



// Email Language Literacy and Numeracy Quiz (ASC + NECCLV004)
function email_language_literacy_numeracy_quiz($form) 
{
	$recipient = $form->email;
	
	if (is_email ( $recipient )) {
		add_filter ( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
		
		// Send email notification to admin advising of error
		$body_content = '<html><body>';
		$body_content .= 'Dear Student<br/><br/>';
		$body_content .= 'As part of your course enrolment you advised you may require further assistance with language, literacy or numeracy and as such you are required to complete a <strong>short online quiz</strong> so we can understand your current skill levels and if required, tailor your training to suit your needs.<br/><br/>';
		$body_content .= '<strong>This needs to be completed at least 72 hours before the course commencement.</strong> Click on the link to start the online quiz.<br/><a href="https://necaeducation.quiz.lln.training/?quizId=acsf3shortcourse" target="_blank">https://necaeducation.quiz.lln.training/?quizId=acsf3shortcourse</a><br/><br/>';
		$body_content .= 'If you are unable to open this link please cut and paste into your URL browser.<br/>';
		$body_content .= 'If you have any issues completing this, please contact student services on 9381 1922.';
		$body_content .= 'Regards<br/>';
		$body_content .= 'Student Services';
		$body_content .= '</body></html>';
		
		$subject = 'NECA Education - Language, Literacy and Numeracy Quiz ' . $url;
		$headers [] = 'From: NECA Education and Careers <info@necaeducation.com.au>';
		
		// Output error
		if (JR_DEBUG_MODE) {
			echo $body_content;
		}
		
		// Send email
		wp_mail ( $recipient, $subject, $body_content, $headers );
		
		// Reset content-type to avoid conflicts -- https://core.trac.wordpress.org/ticket/23578
		remove_filter ( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
	}
}




/*
 * GRAVITY FORM - Post submission processing
 * Performs this process after the "Non-Accredited Application Form" has been submitted
 * The Non-Accredited Application Form has a cost associated to it which is retrieved
 * from JobReady and is added to the cart for payment. Once the payment process has been completed and
 * the order is marked as "Completed" the form information can be captured in JobReady.
 */

// Calls the fuction "woocommerce_order_completed_jobready_post_processing" after WooCommerce Order Status is marked as Completed
// This function will determine which form was submitted from the order items
add_action ( 'woocommerce_order_status_completed', 'woocommerce_order_completed_jobready_post_processing', 10, 2 );

function woocommerce_order_completed_jobready_post_processing($order_id) 
{
	// Get an instance of the WC_Order object
	$order = wc_get_order ( $order_id );
	
	// Load the Coupons related to this order
	$coupons = $order->get_used_coupons ();
	
	// Create an array of products to coupon discounts
	$product_coupons = array ();
	foreach ( $coupons as $coupon_code ) {
		$coupon = new WC_Coupon ( $coupon_code );
		$amount = $coupon->get_amount ();
		$product_ids = $coupon->get_product_ids ();
		
		// Creates an array using the Product ID as the key
		foreach ( $product_ids as $product_id ) {
			$product_coupons [$product_id] = $amount;
		}
	}
	
	// Get items in order
	$items = $order->get_items ();
	
	// Iterating through each WC_Order_Item objects
	foreach ( $items as $item_id => $item ) {
		// Gravity Forms History
		$gfh = wc_get_order_item_meta ( $item_id, "_gravity_forms_history" );
		
		if (JR_DEBUG_MODE) {
			echo "GFH Variable: <br/>";
			var_dump ( $gfh );
			echo "<br/><br/>";
		}
		
		// If there is meta data called "_gravity_forms_history"
		if ($gfh) {
			// Get the Gravity Form ID so we know what type of form we are processing
			$gravity_form_id = $gfh ['_gravity_form_data'] ['id'];
			$gravity_form_entry_id = $gfh ['_gravity_form_linked_entry_id'];
			//echo "Entry ID: " . $gravity_form_entry_id . "<br/>";
			
			// Get the Gravity Form Lead Data (submitted field_id and value)
			$gfl = $gfh ['_gravity_form_lead'];
			
			if (JR_DEBUG_MODE) 
			{
				echo "GFL Variable: <br/>";
				var_dump ( $gfl );
				echo "<br/><br/>";
			}
			
			// Clean up data for ease of use and access by processing functions
			$form_data = new stdClass ();
			foreach ( $gfl as $key => $value ) {
				$form_data->$key = $value;
			}
			
			// Get the Order Item Data
			$order_item = $item->get_data ();
			$product_id = $item->get_product_id ();
			
			// Calculate the item cost (after discounts)
			$item_discount = 0;
			if (isset ( $product_coupons [$product_id] )) 
			{
				$item_discount = $product_coupons [$product_id];
			}
			$item_cost = $order_item ['subtotal'] - $item_discount;
			
			// Check which "gravity_form_id" is linked to the product and process accordingly
			if ($gravity_form_id == SHORT_COURSE_APPLICATION_FORM_NON_ACCREDITED) 
			{
				// echo "Non Accredited Application Form Submission Process<br/>";
				$party_id = short_course_application_form_non_accredited_submission_process ( $form_data, $order, $item_cost );
				
				// Setup the WooCommerce keep array (array of keys to be kept on website database)
				$wc_keep_array = array (
						'_gravity_forms_history',
						'_Course Scope Code',
						'_Course Number',
						'Course Option',
						'First Name',
						'Family Name',
						'Party ID' 
				);
				
				// Set up a Gravity Form keep array (array of form field id's to be kept on website database)
				$gf_keep_array = array (22, 23, 26, 9, 8, 21 );
				
				// Gravity Form Party ID field #
				$gf_party_id_field = 35;
			}
			elseif ($gravity_form_id == SHORT_COURSE_APPLICATION_FORM_ACCREDITED) 
			{
				// echo "Accredited Application Form Submission Process<br/>";
				$party_id = short_course_application_form_accredited_submission_process ( $form_data, $order, $item_cost );
				
				if (ASC_DEBUG_MODE) 
				{
					echo "ASC Party ID returned: " . $party_id . "<br/>";
				}
				
				// Setup the WooCommerce keep array (array of keys to be kept on website database)
				$wc_keep_array = array (
						'_gravity_forms_history',
						'_Course Scope Code',
						'_Course Number',
						'Course',
						'First Name',
						'Family Name',
						'Party ID' 
				);
				
				// Set up a Gravity Form keep array (array of form field id's to be kept on website database)
				$gf_keep_array = array (69, 70, 72, 9, 8,
										21, 46, 49, 111, 54,
										68, 110, 66, 67, 57,
										56, 58, 59 );
				
				// Gravity Form Party ID field #
				$gf_party_id_field = 112;
			}
			elseif ($gravity_form_id == IOT_FORM_ID)
			{
				if (IOT_DEBUG_MODE)
				{
					echo "IOT Form Submission Process<br/>";
				}
				
				$party_id = iot_form_submission_process( $form_data, $order, $item_cost );
				
				if (IOT_DEBUG_MODE)
				{
					echo "IOT Party ID returned: " . $party_id . "<br/>";
				}
				
				// Setup the WooCommerce keep array (array of keys to be kept on website database)
				$wc_keep_array = array (
						'_gravity_forms_history',
						'_Course Scope Code',
						'_Course Number',
						'Course',
						'First Name',
						'Family Name',
						'Party ID'
				);
				
				// Set up a Gravity Form keep array (array of form field id's to be kept on website database)
				$gf_keep_array = array ( 81, 192, 190, 9, 8,
										 21, 124, 54, 68, 67,
										 125 );
				
				// Gravity Form Party ID field #
				$gf_party_id_field = 125;
			}
			
			/*
			// 05.04.2024 - Payment removed from PRE APPRENTICE APPLICATION FORM as requested by Lyn
			// Revert back to Non-Payment method
			
			elseif ($gravity_form_id == PRE_APPRENTICE_APPLICATION_FORM) 
			{
				// echo "Pre-Apprentice Application Form Submission Process<br/>";
				$party_id = pre_apprentice_application_form_submission_process ( $gravity_form_entry_id, $form_data, $order, $item_cost );
				 
				// Setup the WooCommerce keep array (array of keys to be kept on website database)
				$wc_keep_array = array (
						'_gravity_forms_history',
						'_Course Scope Code',
						'_Course Number',
						'Course Option',
						'First Name',
						'Family Name',
						'Party ID' 
				);
				
				// Set up a Gravity Form keep array (array of form field id's to be kept on website database)
				$gf_keep_array = array (77, 78, 98, 9, 8,
										21, 103, 49, 122, 113,
										54, 114, 68, 116, 66,
										117, 67, 57, 56, 58,
										59 );
				
				// Gravity Form Party ID field #
				$gf_party_id_field = 124;
			}
			*/
			
			if ($party_id != false) 
			{
				if($gravity_form_id == SHORT_COURSE_APPLICATION_FORM_ACCREDITED && !ASC_DEBUG_MODE)
				{
					// echo "Confidential information clean up for information stored with order and form currently disabled while testing.<br/>";
					confidential_clean_up_wc_gf ( $order_id, $wc_keep_array, $gf_keep_array, $party_id, $gf_party_id_field, $item_id );
				}
				else if($gravity_form_id == SHORT_COURSE_APPLICATION_FORM_NON_ACCREDITED && !NASC_DEBUG_MODE)
				{
					// echo "Confidential information clean up for information stored with order and form currently disabled while testing.<br/>";
					confidential_clean_up_wc_gf ( $order_id, $wc_keep_array, $gf_keep_array, $party_id, $gf_party_id_field, $item_id );
				}
				else if($gravity_form_id == IOT_FORM_ID && !IOT_DEBUG_MODE)
				{
					// echo "Confidential information clean up for information stored with order and form currently disabled while testing.<br/>";
					confidential_clean_up_wc_gf ( $order_id, $wc_keep_array, $gf_keep_array, $party_id, $gf_party_id_field, $item_id );
				}
				
				/*
				// 05.04.2024 - Payment removed from PRE APPRENTICE APPLICATION FORM as requestey by Lyn Wang
				// Confidential clean up will be performed on the the actual forms processing function (pre_apprentice_application_form.php)
				 
				else if($gravity_form_id == PRE_APPRENTICE_APPLICATION_FORM && !PREAPPRENTICE_DEBUG_MODE)
				{
					// echo "Confidential information clean up for information stored with order and form currently disabled while testing.<br/>";
					confidential_clean_up_wc_gf ( $order_id, $wc_keep_array, $gf_keep_array, $party_id, $gf_party_id_field, $item_id );
				}
				*/
				
				else 
				{
					echo "Confidential information clean up for information stored with order and form currently disabled while testing.<br/>";
					confidential_clean_up_wc_gf ( $order_id, $wc_keep_array, $gf_keep_array, $party_id, $gf_party_id_field, $item_id );
				}
			}
		}
	}
}


/*
 * **************************************
 * function confidential_clean_up_wc_gf 
 * @param unknown $order_id
 * @param unknown $wc_keep_array
 * @param unknown $gf_keep_array
 * @param unknown $party_id
 * @param unknown $enrolment_id
 * 
 * Used to clean up WooCommerce and Gravity Form data
 */

function confidential_clean_up_wc_gf($order_id, $wc_keep_array, $gf_keep_array, $party_id, $gf_party_id_field, $current_item_id) 
{
	// Loads the Order
	$order = wc_get_order ( $order_id );
	
	// Loads all items for this order (only line_item types)
	$items = $order->get_items ();
	
	if (JR_DEBUG_MODE)
	{
		echo "Items on Order ID: " . $order_id . "<br/>";
		var_dump ( $items );
		echo "<br/><br/>";
	}
	
	// Loops through all items on the order
	foreach ( $items as $item_id => $item ) 
	{
		// Only removes the values for the current_item_id
		if ($current_item_id == $item_id) 
		{
			/**
			 * ***************************
			 * WOOCOMMERCE ENTRY CLEANUP *
			 * ***************************
			 */
			
			// Retrieve all the meta information for this item
			$meta = $item->get_meta_data ();
			
			foreach ( $meta as $mk => $mv ) {
				// Check if the key is in the "wc keep array" and if not, deletes its from "the woocommerce_order_itemmeta" table
				if (! in_array ( $mv->key, $wc_keep_array )) {
					$delete_result = wc_delete_order_item_meta ( $item_id, $mv->key );
				}
			}
			
			// Add the Party ID
			$add_party_id_result = wc_add_order_item_meta ( $item_id, 'Party ID', $party_id );
			if (JR_DEBUG_MODE) {
				echo "Add Party ID item meta results: ";
				var_dump ( $add_party_id_result );
				echo "<br/><br/>";
			}
			
			// Retrieve specific Gravity Forms History meta item (from "woocommerce_order_itemmeta")
			$gfh = wc_get_order_item_meta ( $item_id, "_gravity_forms_history", true );
			
			if (JR_DEBUG_MODE) {
				echo "Gravity Forms History meta item: <br/>";
				var_dump ( $gfh );
				echo "<br/><br/>";
			}
			
			// Remove the "gravity_form_lead" itemmeta as it contains personal information
			if (isset ( $gfh ['_gravity_form_lead'] )) {
				unset ( $gfh ['_gravity_form_lead'] );
				
				// Update WooCommerce Order Item Meta (with gravity_form_history minus personal information
				$gfh_update = wc_update_order_item_meta ( $item_id, "_gravity_forms_history", $gfh );
			}
			
			/**
			 * ****************************
			 * GRAVITY FORM ENTRY CLEANUP *
			 * ****************************
			 */
			
			// Retrieve the Gravity Form Linked Entry ID
			$gravity_form_linked_entry_id = $gfh ['_gravity_form_linked_entry_id'];
			
			if (JR_DEBUG_MODE) {
				echo "Gravity Form Linked Entry ID: <br/>";
				var_dump ( $gravity_form_linked_entry_id );
				echo "<br/><br/>";
			}
			
			// Remove confidential information from Gravity Form
			// Get the Gravity Form Entry
			$entry = GFAPI::get_entry ( $gravity_form_linked_entry_id );
			
			foreach ( $entry as $k => $v ) {
				// Check if the key is not in the "gf_keep_array" and only bothers with keys that are numeric (form field id's)
				if (! in_array ( $k, $gf_keep_array ) && is_numeric ( $k )) {
					// If the key is not in the gf keep array, unset it
					unset ( $entry [$k] );
				}
			}
			
			// Adds the additional fields (currently containing empty values)
			$entry [$gf_party_id_field] = $party_id;
			
			if (JR_DEBUG_MODE) {
				echo "Entry after removing unwanted fields and adding the party id field: <br/>";
				var_dump ( $entry );
				echo "<br/><br/>";
			}
			
			// Updates the Gravity Form Entry
			$update_result = GFAPI::update_entry ( $entry, $gravity_form_linked_entry_id );
			
			if (JR_DEBUG_MODE) {
				echo "Gravity Form Update Result: <br/>";
				var_dump ( $update_result );
				echo "<br/><br/>";
			}
		}
	}
}



/* *********************************
 * function confidential_clean_up_gf
 * @param unknown $gf_keep_array
 * @param unknown $party_id
 * @param unknown $g_party_id_field
 * Used to clean up Gravity Form data
 */
function confidential_clean_up_gf($gravity_form_linked_entry_id, $gf_keep_array, $party_id, $gf_party_id_field, $employer_party_id = '', $gf_employer_party_id_field = '') {
	/**
	 * ****************************
	 * GRAVITY FORM ENTRY CLEANUP *
	 * ****************************
	 */
	
	// Remove confidential information from Gravity Form
	// Get the Gravity Form Entry
	$entry = GFAPI::get_entry ( $gravity_form_linked_entry_id );
	
	foreach ( $entry as $k => $v ) 
	{
		// Check if the key is not in the "gf_keep_array" and only bothers with keys that are numeric (form field id's)
		if (! in_array ( $k, $gf_keep_array ) && is_numeric ( $k )) 
		{
			// If the key is not in the gf keep array, unset it
			unset ( $entry [$k] );
		}
	}
	
	// Adds the additional fields (currently containing empty values)
	if(is_int($party_id))
	{
		$entry[$gf_party_id_field] = $party_id;
		
		if ($gf_employer_party_id_field != '' && $employer_party_id > 0) 
		{
			// Adds the additional fields (currently containing empty values)
			$entry [$gf_employer_party_id_field] = $employer_party_id;
		}
		
		// Updates the Gravity Form Entry
		$update_result = GFAPI::update_entry ( $entry, $gravity_form_linked_entry_id );
	}
}


