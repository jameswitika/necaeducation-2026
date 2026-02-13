<?php
/* ************** *
 * PRE-PROCESSING *
 * ************** */

// Short Course (Non-Accredited) Application Form (NASC)
add_filter("gform_pre_render_" . NASC_REGISTRATION_FORM, 'nasc_registration_form_prepopulate');
add_filter("gform_pre_validation_" . NASC_REGISTRATION_FORM, 'nasc_registration_form_prepopulate');
add_filter("gform_pre_submission_filter_" . NASC_REGISTRATION_FORM, 'nasc_registration_form_prepopulate');
add_filter("gform_admin_pre_render" . NASC_REGISTRATION_FORM, 'nasc_registration_form_prepopulate');

function nasc_registration_form_prepopulate($form)
{
	$prefill_fields = array();
	
	if(NASC_DEBUG_MODE)
	{
		echo "Prefill: <br/>";
		var_dump($_SESSION['prefill']);
		echo "<br/><br/>";
	}
	
	// Set NECA Member to FALSE initially. This will be updated if the member is logged in and are a NECA Member
	// This flag is used later to determine whether to charge NECA Member fee or standard fee
	$neca_member = false;
	
	if(isset($_GET['course_scope_code']))
	{
		$course_scope_code = $_GET['course_scope_code'];
		$get_default_course_scope_code = false;
	}
	else
	{
		$get_default_course_scope_code = true;
	}
	
	// Get the Course Number (from the URL variable or the default field value)
	if(isset($_GET['course_number']))
	{
		$course_number = $_GET['course_number'];
		$get_default_course_number = false;
	}
	else
	{
		$get_default_course_number = true;
	}
	
	if($get_default_course_number || $get_default_course_scope_code)
	{
		// Loop through Form Fields to retrieve the course number
		foreach($form['fields'] as $field)
		{
			if($field->inputName == 'course_number')
			{
				$course_number = $field->defaultValue;
			}
			
			if($field->inputName == 'course_scope_code')
			{
				$course_scope_code = $field->defaultValue;
			}
		}
	}
	
	// Load JobReadyDate
	$jrd =JobReadyDateOperations::loadJobReadyDateByCourseNumber($course_number);
	
	// Check if there no more enrolment spots remaining
	if(strpos($course_number, 'Holding Bay') === false && $jrd->enrolments_remaining <= 0 )
	{
		// Exit or Redirect
		$form['limitEntries'] = true;
		$form['limitEntriesCount'] = 0;
		$form['limitEntriesMessage'] = "There are no more enrolment spots left for this course date. Please select another date.";
		return $form;
	}
	
	if(isset($_SESSION['prefill']))
	{
		$prefill_fields['9'] = $_SESSION['prefill']->first_name;
		$prefill_fields['8'] = $_SESSION['prefill']->surname;
		$prefill_fields['19'] = $_SESSION['prefill']->mobile_phone;
        $prefill_fields['21'] = $_SESSION['prefill']->email;
	}
	
	// Loops through form fields
	foreach($form["fields"] as &$field)
	{
		// Cost
		if($field->id == 26)
		{
			// If course is "LEP", "SWP" or "LET" then we need to let the user select their preferred choice
			// Retrieve the costs from Job Ready and display them in a drop down list for selection
			if($course_scope_code == "LEP" || $course_scope_code == "SWP" || $course_scope_code == "LET")
			{
				$choices = jrar_invoice_options($course_number, $neca_member);
			}
			else
			{
				$choices = jrar_invoice_options($course_number, $neca_member);
			}
			
			$field->choices = $choices;
		}
		
		// Course Cost (update label to be course name and date)
		if($field->id == 29)
		{
			$total_label = $jrd->course_name . " (" . $jrd->start_date_clean. " to " . $jrd->end_date_clean . ")";
			if($course_scope_code == "LEP" || $course_scope_code == "SWP" || $course_scope_code == "LET")
			{
				$field->label = 'Cost: ';
			}
			else
			{
				$field->label = $total_label;
			}
		}
		
		// Set predefined values
		if(isset($prefill_fields))
		{
            // Check if field ID exists in prefill fields
			if(array_key_exists($field->id, $prefill_fields))
			{
                // Get the prefill value
				$prefill_value = $prefill_fields[$field->id];
				
				// Multiple values to be used to pre-populate
				if(is_array($prefill_value))
				{
					$new_inputs = array();
					foreach($field->inputs as $input)
					{
						$input_id = $input['id'];
						$input['defaultValue'] = $prefill_value[$input_id];
						array_push($new_inputs, $input);
					}
					$field->inputs = $new_inputs;
				}
				else
				{
					$field->defaultValue = $prefill_fields[$field->id];
				}
			}
		}
	}
	
	return $form;
}



/* *************** *
 * PRE-SUBMISSIONS *
 * *************** */
// Short Course (Non-Accredited) Application Form (NASC)
add_filter("gform_pre_submission_" . NASC_REGISTRATION_FORM, 'nasc_registration_form_presubmission');

function nasc_registration_form_presubmission()
{
	if(isset($_SESSION['prefill']))
	{
		$prefill = $_SESSION['prefill'];
	}
	else
	{
		$prefill = new stdClass();
	}
	
	$prefill->first_name = $_POST['input_9'];
	$prefill->surname = $_POST['input_8'];
	$prefill->mobile_phone = $_POST['input_19'];
	$prefill->email = $_POST['input_21'];
	$_SESSION['prefill'] = $prefill;
}



/* *************** *
 * POST-PROCESSING *
 * *************** */

// ****************************** //
// NASC REGISTRATION FORM PROCESS //
// ****************************** //
function nasc_registration_form_process($form_data, $order, $item_cost) {
	if (NASC_DEBUG_MODE) 
	{
		echo "<h3>NASC Registration Form - Non Accredited Submission Process</h3>";
	}
	
	// Set common variables used in the error emails
	$file = 'nasc_registration_form.php';
	$function = 'nasc_registration_form_submission_process()';
	
	$form = new JobReadyForm ();
	
	// Course Details
	$form->course_scope_code = $form_data->{'22'};
	$form->course_number = $form_data->{'23'};
	$form->invoice_option = $form_data->{'26'};
	$form->cost = $item_cost;
	
	// Personal Details
	$form->first_name = ucwords ( strtolower ( $form_data->{'9'} ) );
	$form->surname = ucwords ( strtolower ( $form_data->{'8'} ) );
		
	// Contact Details
	$form->mobile_phone = $form_data->{'19'};
	$form->email = strtolower ( $form_data->{'21'} );
	
	// Privacy Declaration	
	$form->privacy_declaration = isset ( $form_data->{'41.1'} ) ? $form_data->{'41.1'} : '';
	
	if (NASC_DEBUG_MODE) 
	{
		echo "Form Variable: <br/>";
		var_dump ( $form );
		echo "<br/><br/>";
	}
	
	/*
	 * Setup Job Ready Resources and Create Accordingly
	 */
	
	// Setup "Party" Resource
	$party = new JRAParty ();
	$party->party_type = 'Person';
	$party->contact_method = 'Email';
	$party->first_name = $form->first_name;
	$party->surname = $form->surname;
	
	// Setup "Party > Contact Detail" Child Resources
	$contact_details = array ();
	$contact_detail = new JRAPartyContactDetail ();
	$contact_detail->primary = 'true';
	$contact_detail->contact_type = 'Email';
	$contact_detail->value = $form->email;
	array_push ( $contact_details, $contact_detail );
	
	if (trim ( $form->mobile_phone != '' )) 
	{
		$contact_detail = new JRAPartyContactDetail ();
		$contact_detail->primary = 'true';
		$contact_detail->contact_type = 'Mobile';
		$contact_detail->value = $form->mobile_phone;
		array_push ( $contact_details, $contact_detail );
	}
	
	$party->contact_detail_child = $contact_details;
	
	// Unset unused fields from the Party
	unset ( $party->avetmiss_child );
	unset ( $party->vet_free_help_child );
	unset ( $party->cricos_child );
	
	if (NASC_DEBUG_MODE) 
	{
		echo "Party Variable: <br/>";
		var_dump ( $party );
		echo "<br/><br/>";
	}
	
	// Check if the Party Exists
	$party_result = JRAPartyOperations::getJRAParty ( $party );
	
	$party_attributes = $party_result->attributes ();
	$count = ( int ) $party_attributes ['total'];
	
	if ($count > 0) 
	{
		// Set Party ID
		$party_id = ( string ) $party_result->party->{'party-identifier'};
		$new_party = false;
		
		if (NASC_DEBUG_MODE) 
		{
			echo "Party Exists - Party ID: " . $party_id . "<br/><br/>";
			echo "Result: <br/>";
			var_dump ( $party_result );
			echo "<br/><br/>";
		}
	} 
	else
	{
		if (NASC_DEBUG_MODE) 
		{
			echo "Party does not exist. Create new Party<br/>";
		}
		
		// Create Party
		$party_xml = JRAPartyOperations::createJRAPartyXMLBasic ( $party );
		
		if (NASC_DEBUG_MODE) 
		{
			echo "Party XML: <br/>";
			var_dump ( $party_xml );
			echo "<br/><br/>";
		}
		
		$party_result = JRAPartyOperations::createJRAParty ( $party_xml );
		
		if (isset ( $party_result->{'party-identifier'} )) 
		{
			$party_id = ( string ) $party_result->{'party-identifier'};
			$new_party = true;
			
			if (NASC_DEBUG_MODE) 
			{
				echo "New Party Created - Party ID: " . $party_id . "<br/><br/>";
			}
		} 
		else 
		{
			if (NASC_DEBUG_MODE) 
			{
				echo "Error occurred when creating Party<br/><br/>";
			}
			return false;
		}
	}
	
	if (isset ( $party_id )) 
	{
		 // Create Enrolment
		 $enrolment = new JRAEnrolment ();
		 $enrolment->party_identifier = $party_id;
		 $enrolment->course_number = $form->course_number;
		 $enrolment->invoice_option = $form->invoice_option;
         $enrolment->enrolment_status = 'Application Pending';
		 
		 if (NASC_DEBUG_MODE) 
		 {
		 	echo "Create New Enrolment<br/>";
		 }
		 
		 $enrolment_xml = JRAEnrolmentOperations::createJRAEnrolmentXMLBasic ( $enrolment );
		 $enrolment_result = JRAEnrolmentOperations::createJRAEnrolment ( $enrolment_xml );
		 
		 if (isset ( $enrolment_result->{'party-identifier'} )) 
		 {
		 	$enrolment_id = ( string ) $enrolment_result->{'enrolment-identifier'};
		 	if (NASC_DEBUG_MODE) 
		 	{
		 		echo "New Enrolment created - Enrolment ID: " . $enrolment_id . "<br/><br/>";
		 	}
		 } 
		 else 
		 {
		 	if (NASC_DEBUG_MODE) 
		 	{
		 		echo "Error occurred when creating Enrolment<br/><br/>";
		 	}
		 }
		 
		 if (isset ( $enrolment_id )) 
		 {
		 	// Load Invoice by Enrolment ID
		 	$invoices = JRAInvoiceOperations::loadInvoiceByEnrolmentID ( $enrolment_id );
		 	
		 	if (NASC_DEBUG_MODE) 
		 	{
		 		echo "Invoices retrieved from Job Ready: " . count ( $invoices ) . "<br/><br/>";
		 	}
		 	
		 	// Load the Payment Details
		 	$order_id = ( string ) $order->get_id ();
		 	$payment_details = getPaymentByOrderID ( $order_id );
		 	
		 	foreach ( $invoices as $invoice ) 
		 	{
		 		// Create Payment
		 		$payment = new JRAPayment ();
		 		$payment->invoice_number = $invoice->{'invoice_number'}; // Job Ready Invoice Number
		 		$payment->party_identifier = $party_id; // Job Ready Party ID
		 		$payment->type = 'Payment';
		 		$payment->description = 'Payment via NECA Website - Order #' . $order_id;
		 		$payment->source = 'Credit Card';
		 		$payment->enabled = 'true';
		 		
		 		$payment->payment_items = array ();
		 		$payment_item = new JRAPaymentItem ();
		 		$payment_item->payment_amount = $form->cost;
		 		array_push ( $payment->payment_items, $payment_item );
		 		
		 		$payment_xml = JRAPaymentOperations::createJRAPaymentXML ( $payment );
		 		$payment_result = JRAPaymentOperations::createJRAPayment ( $party_id, $payment->invoice_number, $payment_xml );
		 		
		 		if (isset ( $payment_result->{'invoice-number'} )) 
		 		{
		 			if (NASC_DEBUG_MODE) 
		 			{
		 				echo "Payment Created<br/><br/>";
		 			}
		 		} 
		 		else 
		 		{
		 			if (NASC_DEBUG_MODE) 
		 			{
		 				echo "Error occured when creating Payment<br/><br/>";
		 			}
		 		}
		 	}
		}

        // Load Job Ready Data by Course Number
        $jrd = JobReadyDateOperations::loadJobReadyDateByCourseNumber($form->course_number);
        $form->course_start_date = $jrd->start_date_clean;

        // Send Email to User
        send_nasc_registration_confirmation_email ( $form, $party_id, $enrolment_id );
		 
		 // Check course enrolment availability and sync from Job Ready if less than 3 remaining
		 check_course_date_and_sync ( $form->course_number );
		 
		 return $party_id;
	} 
	else 
	{
		if (NASC_DEBUG_MODE) 
		{
			echo "Party was not created and submission process was stopped<br/><br/>";
		}
		return false;
	}
}


function send_nasc_registration_confirmation_email($form, $party_id, $enrolment_id)
{
    // Get Email Template
    $email_template = get_email_template_by_name('NASC Registration Confirmation');
    
    if($email_template)
    {
        // Prepare Email Content
        $to = $form->email;
        $subject = str_replace('{course_number}', $form->course_number, $email_template->subject);
        
        $search = array(
            '{first_name}',
            '{surname}',
            '{course_number}',
            '{course_start_date}',
            '{party_id}',
            '{enrolment_id}'
        );
        
        $replace = array(
            $form->first_name,
            $form->surname,
            $form->course_number,
            $form->course_start_date,
            $party_id,
            $enrolment_id
        );
        
        $body = str_replace($search, $replace, $email_template->body);
        
        // Send Email
        wp_mail($to, $subject, $body);
    }
}


function get_email_template_by_name($template_name)
{
    // Create a link to /nasc-enrol/ passing in the party_id and enrolment_id as URL parameters    
    $enrolment_link = home_url('/nasc-enrol/') . "?party_id={party_id}&enrolment_id={enrolment_id}";
    
    $email_template = new stdClass();
    $email_template->subject = 'Complete your enrolment for {course_number}';
    $email_template->preheader = 'Pre-header: It\'s time to finalise your NECA E&C course enrolment details';
    $email_template->body = '<div style="display:none!important;visibility:hidden;opacity:0;color:transparent;height:0;width:0;overflow:hidden;">' . esc_html($email_template->preheader) . '</div>';
    $email_template->body = "Hi {first_name},\n\n";
    $email_template->body .= "Thank you for registering for {course_number} starting on {course_start_date}. We're looking forward to welcoming you to our campus.\n\nTo complete your enrolment for your course, we just need a few more details from you. Please click the link below to provide the required information:\n\n";
    $email_template->body .= $enrolment_link;
    $email_template->body .= "\n\nIf you have any questions, please call Student Services on 9381 1922 or email studentservices@necaeducation.com.au.\n\nThe NECA Education and Careers Team";

    return $email_template;
}