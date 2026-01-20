<?php
/* ************** *
 * PRE-PROCESSING *
 * ************** */

// Short Course (Non-Accredited) Application Form (NASC)
add_filter("gform_pre_render_" . NASC_ENROLMENT_FORM, 'nasc_enrolment_form_prepopulate');
add_filter("gform_pre_validation_" . NASC_ENROLMENT_FORM, 'nasc_enrolment_form_prepopulate');
add_filter("gform_pre_submission_filter_" . NASC_ENROLMENT_FORM, 'nasc_enrolment_form_prepopulate');
add_filter("gform_admin_pre_render" . NASC_ENROLMENT_FORM, 'nasc_enrolment_form_prepopulate');

function nasc_enrolment_form_prepopulate($form)
{
	$prefill_fields= array();
	
	if(NASC_DEBUG_MODE)
	{
		echo "Prefill: <br/>";
		var_dump($_SESSION['prefill']);
		echo "<br/><br/>";
	}
	
	// Set NECA Member to FALSE initially. This will be updated if the member is logged in and are a NECA Member
	// This flag is used later to determine whether to charge NECA Member fee or standard fee
	$neca_member = false;
	
	$party_id = isset($_GET['party_id']) ? $_GET['party_id'] : null;
	$enrolment_id = isset($_GET['enrolment_id']) ? $_GET['enrolment_id'] : null;

    // Validate required parameters
    if(!$party_id || ! $enrolment_id) 
    {
        // Exit or Redirect
        $form['limitEntries'] = true;
        $form['limitEntriesCount'] = 0;
        $form['limitEntriesMessage'] = "Required parameters missing: party_id and/or enrolment_id.";
        return $form;
    }

    // Load the enrolment details from Job Ready
    $jra_enrolment = JRAEnrolmentOperations::getJRAEnrolmentByEnrolmentID($enrolment_id);

    // Validate the Party ID is associated with the Enrolment
    if ($jra_enrolment && isset($jra_enrolment->{'party-identifier'})) {
        $associated_party_id = (string)$jra_enrolment->{'party-identifier'};
        if ($associated_party_id !== $party_id) {
            // Exit or Redirect
            $form['limitEntries'] = true;
            $form['limitEntriesCount'] = 0;
            $form['limitEntriesMessage'] = "Invalid Party ID supplied. Party ID is NOT associated with this enrolment.";
	    	return $form;
        }
    } 
    else 
    {
        $form['limitEntries'] = true;
        $form['limitEntriesCount'] = 0;
        $form['limitEntriesMessage'] = "Enrolment data does not contain a party identifier.";
    }

    // Get the Enrolment Status
    if ($jra_enrolment && isset($jra_enrolment->{'enrolment-status'})) {
        $enrolment_status = (string)$jra_enrolment->{'enrolment-status'};
        if($enrolment_status !== "Application Pending")
        {
            $form['limitEntries'] = true;
            $form['limitEntriesCount'] = 0;
            $form['limitEntriesMessage'] = "Enrolment is not currently pending";
        }
    } else {
        $form['limitEntries'] = true;
        $form['limitEntriesCount'] = 0;
        $form['limitEntriesMessage'] = "Enrolment data does not contain an enrolment-status.";
    }

    // Get the Course Number
    if ($jra_enrolment && isset($jra_enrolment->{'course-number'})) {
        $course_number = (string)$jra_enrolment->{'course-number'};

        // Get the Course Scope Code by using the Course Number to lookup the Course Details
        $jrd = JobReadyDateOperations::loadJobReadyDateByCourseNumber($course_number);

        if ($jrd && isset($jrd->course_scope_code)) {
            $course_scope_code = (string)$jrd->course_scope_code;
        } else {
            $form['limitEntries'] = true;
            $form['limitEntriesCount'] = 0;
            $form['limitEntriesMessage'] = "Failed to retrieve Course Scope Code for Course Number: $course_number.";
        }

    } else {
        $form['limitEntries'] = true;
        $form['limitEntriesCount'] = 0;
        $form['limitEntriesMessage'] = "Enrolment data does not contain a course number.";
    }

    // Load the Party details from Job Ready
    $party_xml_object = JRAPartyOperations::loadJRAPartyByID( $party_id );

	// If the Employee Party Login session exists, load the Party from Job Ready and confirm it is valid by comparing it to the Employee Party ID (ID) also stored in session
	if($party_xml_object !== false)
	{
        // Convert XMLObject to JRAParty
        $form_fields = JobReadyFormOperations::convertPartyXMLToJobReadyForm($party_xml_object);

        $prefill_fields['22'] = $course_scope_code;
        $prefill_fields['23'] = $course_number;

        $prefill_fields['2'] = $form_fields->title;
        $prefill_fields['9'] = $form_fields->first_name;
        $prefill_fields['8'] = $form_fields->surname;
        $prefill_fields['10'] = $form_fields->known_by;
        $prefill_fields['11'] = $form_fields->birth_date;
        $prefill_fields['27'] = $form_fields->gender;
        $prefill_fields['64'] = $form_fields->street_number;
        $prefill_fields['30'] = $form_fields->street_name;
        $prefill_fields['31'] = $form_fields->suburb;
        $prefill_fields['32'] = $form_fields->state;
        $prefill_fields['33'] = $form_fields->postcode;
        
        //$prefill_fields['43'] = $form_fields->postal_address_same;
        $prefill_fields['65'] = $form_fields->postal_street_number;
        $prefill_fields['44'] = $form_fields->postal_street_name;
        $prefill_fields['47'] = $form_fields->postal_suburb;
        $prefill_fields['48'] = $form_fields->postal_state;
        $prefill_fields['46'] = $form_fields->postal_postcode;
        
        $prefill_fields['20'] = $form_fields->home_phone;
        $prefill_fields['19'] = $form_fields->mobile_phone;
		$prefill_fields['21'] = array(
			'21' => $form_fields->email,
			'21.1' => $form_fields->email,
			'21.2' => $form_fields->email,
		);
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
		
		// Title
		if($field->id == 2)
		{
			$field->choices = jrar_title();
		}
		
		// Gender
		if($field->id == 27)
		{
			$field->choices = jrar_gender();
		}
		
		// States
		if($field->id == 32 || $field->id == 48)
		{
			$field->choices = jrar_state();
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
		
		// Set predefined values if person logged in
		if(isset($prefill_fields))
		{
			if(array_key_exists($field->id, $prefill_fields))
			{
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
add_filter("gform_pre_submission_" . NASC_ENROLMENT_FORM, 'nasc_enrolment_form_presubmission');

function nasc_enrolment_form_presubmission()
{
	if(isset($_SESSION['prefill']))
	{
		$prefill = $_SESSION['prefill'];
	}
	else
	{
		$prefill = new stdClass();
	}
	
	$prefill->title = isset($_POST['input_2']) ? $_POST['input_2'] : '';
	$prefill->first_name = isset($_POST['input_9']) ? $_POST['input_9'] : '';
	$prefill->surname = isset($_POST['input_8']) ? $_POST['input_8'] : '';
	$prefill->known_by= isset($_POST['input_10']) ? $_POST['input_10'] : '';
	$prefill->gender= isset($_POST['input_27']) ? $_POST['input_27'] : '';
	$prefill->birth_date = isset($_POST['input_11']) ? $_POST['input_11'] : '';
	$prefill->home_phone = isset($_POST['input_20']) ? $_POST['input_20'] : '';
	$prefill->mobile_phone = isset($_POST['input_19']) ? $_POST['input_19'] : '';
	$prefill->email = isset($_POST['input_21']) ? $_POST['input_21'] : '';
	$prefill->street_number = isset($_POST['input_64']) ? $_POST['input_64'] : '';
	$prefill->street_name = isset($_POST['input_30']) ? $_POST['input_30'] : '';
	$prefill->suburb = isset($_POST['input_31']) ? $_POST['input_31'] : '';
	$prefill->state = isset($_POST['input_32']) ? $_POST['input_32'] : '';
	$prefill->postcode = isset($_POST['input_33']) ? $_POST['input_33'] : '';
	$prefill->postal_address_same = isset($_POST['input_43_1']) ? "Yes" : "";
	$prefill->postal_street_number = isset($_POST['input_65']) ? $_POST['input_65'] : '';
	$prefill->postal_street_name = isset($_POST['input_44']) ? $_POST['input_44'] : '';
	$prefill->postal_suburb = isset($_POST['input_47']) ? $_POST['input_47'] : '';
	$prefill->postal_state = isset($_POST['input_48']) ? $_POST['input_48'] : '';
	$prefill->postal_postcode = isset($_POST['input_46']) ? $_POST['input_46'] : '';
	
	$prefill->emergency_contact_firstname = isset($_POST['input_58']) ? $_POST['input_58'] : '';
	$prefill->emergency_contact_surname = isset($_POST['input_59']) ? $_POST['input_59'] : '';
	$prefill->emergency_contact_number = isset($_POST['input_60']) ? $_POST['input_60'] : '';
	$prefill->emergency_contact_relationship = isset($_POST['input_61']) ? $_POST['input_61'] : '';
	
	// Set some default values for other forms
	$prefill->country_of_birth = 'Australia';
	$prefill->main_language = 'English';
	
	$_SESSION['prefill'] = $prefill;
}



/* *************** *
 * POST-PROCESSING *
 * *************** */

// ********************************************************************* //
// NASC ENROLMENT FORM PROCESS (NASC) - START //
// ********************************************************************* //
add_action('gform_after_submission_' . NASC_ENROLMENT_FORM, 'nasc_enrolment_form_process', 10, 2);

function nasc_enrolment_form_process($entry, $form_data)
{
	if (NASC_DEBUG_MODE) 
	{
		echo "<h3>NASC Enrolment Form Submission Process</h3>";
	}
	
	// Set common variables used in the error emails
	$file = 'nasc_enrolment_form.php';
	$function = 'nasc_enrolment_form_process()';
	
	$form = new JobReadyForm ();
	
	// Course Details
	$enrolment_id = rgar($entry, '69');
    $party_id = rgar($entry, '35');

    $form->course_scope_code = rgar($entry, '22');
	$form->course_number = rgar($entry, '23');
	
	// Personal Details
	$form->gender = rgar($entry, '27');
	$form->title = rgar($entry, '2');
	$form->first_name = ucwords ( strtolower ( rgar($entry, '9') ) );
	$form->middle_name = ucwords ( strtolower ( rgar($entry, '28') ) );
	$form->surname = ucwords ( strtolower ( rgar($entry, '8') ) );
	$form->known_by = ucwords ( strtolower ( rgar($entry, '10') ) );
	$form->birth_date = rgar($entry, '11');
	
	// Contact Details
	$form->home_phone = rgar($entry, '20');
	$form->mobile_phone = rgar($entry, '19');
	$form->email = strtolower ( rgar($entry, '21') );
	
	// Address
	$form->street_number = strtolower ( rgar($entry, '64') );
	$form->street_name = ucwords ( strtolower ( rgar($entry, '30') ) );
	$form->suburb = ucwords ( strtolower ( rgar($entry, '31') ) );
	$form->state = ucwords ( strtolower ( rgar($entry, '32') ) );
	$form->postcode = rgar($entry, '33');
	
	$form->postal_address_same = rgar($entry, '43.1');
	
	if ($form->postal_address_same != 'Yes') 
	{
		// Address
		$form->postal_street_number = ucwords ( strtolower ( rgar($entry, '65') ) );
		$form->postal_street_name = ucwords ( strtolower ( rgar($entry, '44') ) );
		$form->postal_suburb = ucwords ( strtolower ( rgar($entry, '47') ) );
		$form->postal_state = ucwords ( strtolower ( rgar($entry, '48') ) );
		$form->postal_postcode = rgar($entry, '46');
	}
	
	// Emergency Contact
	$form->emergency_contact_firstname = ucwords ( strtolower ( rgar($entry, '58') ) );
	$form->emergency_contact_surname = ucwords ( strtolower ( rgar($entry, '59') ) );
	$form->emergency_contact_number = ucwords ( strtolower ( rgar($entry, '60') ) );
	$form->emergency_contact_relationship = ucwords ( strtolower ( rgar($entry, '61') ) );
	
	$form->privacy_declaration = rgar($entry, '82.1') == '1' ? 'Y' : '';
	
	if (NASC_DEBUG_MODE) 
	{
		echo "Form Variable: <br/>";
		var_dump ( $form );
		echo "<br/><br/>";
	}
	
	/*
	 * Setup Job Ready Resources and Create Accordingly
	 */
	
	// Create PDF
	$nasc_enrolment_form_pdf = nasc_enrolment_form_pdf ( $form );
	if(NASC_DEBUG_MODE)
	{
		echo '<a href="' . JR_ROOT_URL . '/pdf/' . $nasc_enrolment_form_pdf. '" target="_blank">Short Course Non Accredited Applcation Form (PDF)</a><br/><br/>';
	}

    // Load Party by Party ID
    $party_xml_object = JRAPartyOperations::loadJRAPartyByID( $party_id );

    if ($party_xml_object === false || !$party_xml_object) {
        if (NASC_DEBUG_MODE) {
            echo "Failed to load Party with ID: " . $party_id . "<br/><br/>";
        }
        return false;
    }

    // Convert Party XML Object to JRAParty Object
    $party = JRAPartyOperations::mapJRAPartyXMLObjectToJRAParty( $party_xml_object );

    if (!$party) {
        if (NASC_DEBUG_MODE) {
            echo "Failed to map Party XML to JRAParty object<br/><br/>";
        }
        return false;
    }

    if (NASC_DEBUG_MODE) 
    {
        echo "Party Exists - Party ID: " . $party_id . "<br/><br/>";
        echo "Result: <br/>";
        var_dump ( $party_xml_object );
        echo "<br/><br/>";
    }

	// Setup "Party" Resource
	$party->party_type = 'Person';
	$party->contact_method = 'Email';
	$party->first_name = $form->first_name;
	$party->middle_name = $form->middle_name;
	$party->surname = $form->surname;
	$party->known_by = $form->known_by;
	$party->birth_date = $form->birth_date;
	$party->gender = $form->gender;
	$party->title = $form->title;

	// Setup "Party > Address" Child Resources
	$party_addresses = array ();
	$party_address = new JRAPartyAddress ();
	$party_address->primary = 'true';
	$party_address->street_number = $form->street_number;
	$party_address->street_name = $form->street_name;
	$party_address->suburb = $form->suburb;
	$party_address->state = $form->state;
	$party_address->country = 'Australia';
	$party_address->post_code = $form->postcode;
	$party_address->location = "Home";

    // Add to party_addresses array
	array_push ( $party_addresses, $party_address );
	
	// Add Postal Address?
	if ($form->postal_address_same != 'Yes') 
	{
		$postal_address = new JRAPartyAddress ();
		$postal_address->primary = '';
		$postal_address->street_number = $form->postal_street_number;
		$postal_address->street_name = $form->postal_street_name;
		$postal_address->suburb = $form->postal_suburb;
		$postal_address->state = $form->postal_state;
		$postal_address->country = 'Australia';
		$postal_address->post_code = $form->postal_postcode;
		$postal_address->location = "Postal";
		array_push ( $party_addresses, $postal_address );
	}
	
	$party->address_child = $party_addresses;
	
	// Setup "Party > Contact Detail" Child Resources
	$contact_details = array ();
	$contact_detail = new JRAPartyContactDetail ();
	$contact_detail->primary = 'true';
	$contact_detail->contact_type = 'Email';
	$contact_detail->value = $form->email;
	array_push ( $contact_details, $contact_detail );
	
	if (trim ( $form->home_phone != '' )) 
	{
		$contact_detail = new JRAPartyContactDetail ();
		$contact_detail->primary = '';
		$contact_detail->contact_type = 'Phone';
		$contact_detail->value = $form->home_phone;
		array_push ( $contact_details, $contact_detail );
	}
	
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
	
	if (NASC_DEBUG_MODE) 
	{
		echo "Before Update Party <br/>";
	}

    // Update Party
    $update_party_xml = JRAPartyOperations::updateJRAPartyXML ( $party );
    
    if (NASC_DEBUG_MODE) 
    {
        echo "Update Party XML: <br/>";
        var_dump($update_party_xml);
        echo "<br/><br/>";
    }
    
    $update_party_result = JRAPartyOperations::updateJRAParty ( $update_party_xml, $party_id );
    
    if( isset( $update_party_result->{'party-identifier'} )) 
    {
        if (NASC_DEBUG_MODE) 
        {
            echo "Party Updated<br/><br/>";
        }
    }
	
	if ( isset($party_id)) 
	{
	    if (isset ( $enrolment_id )) 
		{
       		// Load the Enrolment
            $enrolment_xml_object = JRAEnrolmentOperations::getJRAEnrolmentByEnrolmentID( $enrolment_id );

            // Map the XML Object to JRAEnrolment Object
            $enrolment = JRAEnrolmentOperations::mapJRAEnrolmentXMLObjectToJRAEnrolment( $enrolment_xml_object);
            $enrolment->enrolment_status = "Currently Enrolled";

	 	    $update_enrolment_status_result = JRAEnrolmentOperations::updateJRAEnrolmentStatus( $enrolment, $enrolment_id );
		}
		 
		// Create "Party Contact" Resource for Emergency Contact Person
		// Setup "Party Contact" Resource
		$party_contact = new JRAPartyContact ();
		$party_contact->contact_method = 'Phone';
		$party_contact->first_name = $form->emergency_contact_firstname;
		$party_contact->surname = $form->emergency_contact_surname;
		$party_contact->phone = preg_replace ( '/\s/', '', $form->emergency_contact_number );
		$party_contact->relationship = $form->emergency_contact_relationship;
		 
		// Check if "Party Contact" exists already
		$jra_party_contacts = JRAPartyContactOperations::getJRAPartyContacts ( $party_id );
		 
		// Loops through all party contacts linked to the party_id
		foreach ( $jra_party_contacts as $jra_party_contact ) 
		{
		    if ($jra_party_contact->first_name == $party_contact->first_name && $jra_party_contact->surname == $party_contact->surname) 
		 	{
		 		$party_contact_id = ( string ) $jra_party_contact->id;
		 		break;
		 	}
		}
		 
		if (isset ( $party_contact_id )) 
		{
		 	if (NASC_DEBUG_MODE) 
		 	{
		 		echo "Party Contact Exists<br/><br/>";
		 	}
		 	
		 	// Update Party Contact (email + phone number)
		 	$party_contact_update_result = JRAPartyContactOperations::updateJRAPartyContact ( $party_id, $party_contact_id, $party_contact );
		} 
		else 
		{
		    if (NASC_DEBUG_MODE) 
		 	{
		 		echo "Create Party Contact<br/><br/>";
		 	}
		 	
		 	$party_contact_xml = JRAPartyContactOperations::createJRAPartyContactXML ( $party_contact );
		 	$party_contact_result = JRAPartyContactOperations::createJRAPartyContact ( $party_id, $party_contact_xml );
		 	
		 	if (isset ( $party_contact_result->id )) 
		 	{
		 		if (NASC_DEBUG_MODE) 
		 		{
		 			echo "Party Contact created - Party Contact ID: " . $party_contact->id . "<br/><br/>";
		 		}
		 	} 
		 	else 
		 	{
		 		if (NASC_DEBUG_MODE) 
		 		{
		 			echo "Error occured when creating Party Contact<br/><br/>";
		 		}
		 	}
		}
		 
		// Create "PartyDocument" and link the Application Form PDF to the Party
		$pdf_file = JR_ROOT_PATH . '/pdf/' . $nasc_enrolment_form_pdf;
		$document = new JRAPartyDocument ();
		$document->party_id = $party_id;
		$document->name = 'Course Application Form (' . $form->course_scope_code . ' - ' . $form->course_number . ')';
		$document->description = 'Non-Accredited Application Form (' . $form->course_scope_code . ' - ' . $form->course_number . ')';
		$document->filename = $pdf_file;
		 
		$party_document_result = JRAPartyDocumentOperations::createJRAPartyDocument ( $party_id, $document );
		 
		if (isset ( $party_document_result->id )) 
		{
		 	if (NASC_DEBUG_MODE) 
		 	{
		 		echo "Party Document Created - Party Document ID: " . $party_document_result->id . "<br/>";
		 	}
		 	
		 	// Remove PDF from server (unless debugging)
		 	if(!NASC_DEBUG_MODE)
		 	{
			 	unlink ( $pdf_file );
		 	}
		 	
		 	if (NASC_DEBUG_MODE) 
		 	{
		 		echo "PDF file removed from web server<br/><br/>";
		 	}
		} 
		else 
		{
		 	if (NASC_DEBUG_MODE) 
		 	{
		 		echo "Error occurred when created Party Document<br/><br/>";
		 	}
		}
		 
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



/* *********** *
 * PDF-RELATED *
 * *********** */

// Setup NASC Enrolment Form PDF
function nasc_enrolment_form_pdf($form)
{
	// Start
	$content = neca_pdf_content_start();
	
	// Add Heading
	$content .= neca_pdf_content_heading ( 'Non-Accredited Application Form', $form );
	
	// Personal Details Content
	$content .= neca_pdf_content_personal_details ( $form );
	
	// Emergency Contact Details Content
	$content .= neca_pdf_content_emergency_content_details ( $form );
	
	// Page End
	$content .= neca_pdf_page_end ();
	
	// Page Start
	$content .= neca_pdf_page_start ();
	
	// All students must read, sign and date
	$content .= neca_pdf_content_all_students_must_read_sign_date_nasc_enrolment ( $form );
	
	// Page End
	$content .= neca_pdf_page_end ();
	
	// Setup Filename
	$filename = urlencode ( $form->course_number . '_' . $form->first_name . '_' . $form->surname . '_' . current_time ( 'Ymd_hms' ) ) . '.pdf';
	
	// Create PDF
	neca_create_pdf ( $filename, $content );
	
	return $filename;
}


// All Students Must Read, Sign and Date
function neca_pdf_content_all_students_must_read_sign_date_nasc_enrolment($form)
{
	$content = '<div class="heading3">ALL STUDENTS MUST READ, SIGN AND DATE</div>';
	$content .= '<table class="tbl">
					<tr>
						<td style="width:5%">[' . $form->privacy_declaration . ']</td>
						<td style="width:95%">
							<p>
								<strong>PRIVACY DECLARATION - </strong><br/>
The information being sought in this form is collected for the purposes of processing your enrolment application.
The information will be held by NECA Education & Careers and may be accessed and used by people employed or
engaged by NECA Education & Careers in the delivery of services to you. The information may be used or disclosed
to organizations outside NECA Education & Careers where permitted by relevant Privacy Legislation. The provision
of the information is voluntary, however if this information is not provided NECA Education & Careers may be
unable to process your enrolment application. You have a right of access to, and correction of, your personal
information in accordance with the Privacy Legislation and NECA Education & Careers\' Privacy Policy. Please
direct any enquiries you may have in relation to this matter to NECA Education & Careers Privacy Officer.
							</p>
						</td>
					</tr>
				</table>
								
				<table class="tbl">
					<tr>
						<td style="width: 5%;">[&nbsp; &nbsp; &nbsp;]</td>
						<td style="width: 95%;">
							I have read and agree to the Fees, Charges and Policy Guide
						</td>
					</tr>
								
					<tr>
						<td style="width: 5%;">[&nbsp; &nbsp; &nbsp;]</td>
						<td style="width: 95%;">
							I have read and agree to the Refund Policy
						</td>
					</tr>
								
				</table>
				<br/>
				<table class="tbl">
					<tr>
						<td style="width: 25%;">Name: </td>
						<td style="width: 75%;">&nbsp;<br/></td>
					</tr>
					<tr>
						<td style="width: 25%;">Signature: </td>
						<td style="width: 75%;">&nbsp;<br/></td>
					</tr>
					<tr>
						<td style="width: 25%;">Date: </td>
						<td style="width: 75%;">&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
					</tr>
				</table>';
	
	return $content;
}