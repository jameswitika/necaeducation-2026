<?php
/**
 * 01. AJAX processes for calendar frontend
 */

// Add our session scripts to the header and grant access to the LEA_AJAX object
function neca_enqueue_employer_lookup_scripts()
{
    if( is_page('pm-reg') || is_page( 'certiv-pm-reg' ) || is_page( 'diploma-pm-reg' ) || is_page( 'app-reg') || get_the_id() == IOT_PRODUCT_ID || is_page('uee30820-reg') )
	{
		wp_enqueue_style( 'neca_jquery_ui_css', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css', array(), '1.12.1');
		wp_enqueue_script( 'neca_jquery_ui', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js', array( 'jquery' ), '1.12.2', false );

        if ( is_page( 'pm-reg' ) || is_page( 'certiv-pm-reg' ) || is_page( 'diploma-pm-reg' ) || get_the_id() == IOT_PRODUCT_ID )
		{
			// Loads the Employer Lookup JS before the localalize script
			wp_enqueue_script( 'neca_employer_lookup_js', JR_ROOT_URL . '/includes/js/employer_lookup.js', array( 'jquery', 'neca_jquery_ui' ), '1.0.0', true );

			// This function will pass the LEA_Ajax object to our script so we can use the variables contained in it.
			wp_localize_script( 'neca_employer_lookup_js', 'NECA_Ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		}
		else if( is_page( 'app-reg' ) || is_page('uee30820-reg') )
		{
			// Loads the Employer Lookup JS before the localalize script
			wp_enqueue_script( 'neca_employer_lookup_app_reg_js', JR_ROOT_URL . '/includes/js/employer_lookup_app_reg.js', array( 'jquery', 'neca_jquery_ui' ), '1.0.0', true );
			
			// This function will pass the LEA_Ajax object to our script so we can use the variables contained in it.
			wp_localize_script( 'neca_employer_lookup_app_reg_js', 'NECA_Ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		}
	}
	else 
	{
		return;
	}
}

add_action( 'wp_enqueue_scripts', 'neca_enqueue_employer_lookup_scripts' );


/**
 * 02. AJAX Functions
 */
// Lookup Employer (whether logged in or not)
add_action( 'wp_ajax_neca_employer_lookup', 'ajax_neca_employer_lookup' );
add_action( 'wp_ajax_nopriv_neca_employer_lookup', 'ajax_neca_employer_lookup' );

function ajax_neca_employer_lookup()
{
	// If the nonce is not valid, die
	$valid_nonce = check_ajax_referer( 'neca_employer_lookup', 'nonce', false );
	if( $valid_nonce == false ) 
	{
		echo 'error'; die();
	}
	
	$keyword = $_REQUEST['keyword'];

	$employers = JobReadyEmployerOperations::searchJobReadyEmployers($keyword);
	
	if(count($employers) > 10)
	{
		echo $_GET['callback'] . '(' . json_encode(array('formError' => 'true', 'message' => 'Too many results') ) . ');';
		die(); 
	}
	
	$responses = array();
	$count = 0;
	
	// Output results as JSON
	foreach($employers as $employer)
	{
		$responses[$count] = array(	"party_id"	=> $employer->party_id,
									"label" => $employer->trading_name, 
									"value" => $employer->trading_name );
		$count++;
	}
	
	echo $_GET['callback'] . '(' . json_encode($responses) . ');';
	
	die();
}



// Lookup Employer (whether logged in or not)
add_action( 'wp_ajax_jra_employer_lookup', 'ajax_jra_employer_lookup' );
add_action( 'wp_ajax_nopriv_jra_employer_lookup', 'ajax_jra_employer_lookup' );

function ajax_jra_employer_lookup()
{
	// If the nonce is not valid, die
	$valid_nonce = check_ajax_referer( 'neca_employer_lookup', 'nonce', false );
	if( $valid_nonce == false )
	{
		echo 'error'; die();
	}
	
	$employer_party_id = $_REQUEST['employer_party_id'];
	
	// Load the Employer Party from Job Ready
	$result = JRAEmployerOperations::getJRAEmployer( $employer_party_id );
	
	// Convert the XML to an Object
	$employer = xmlToObject($result);
	
	// Set the Employer Address
	$employer_address = $employer->addresses->address;
	$employer_phone = '';
	
	// Loops through all contact detail
	foreach($employer->{'contact-details'}->{'contact-detail'} as $contact_detail)
	{
		if($contact_detail->{'contact-type'} == 'Phone')
		{
			$employer_phone = (string) $contact_detail->value;
			break;
		}
	}

	$employer->company = isset($employer->{'trading-name'}) ? (string) $employer->{'trading-name'} : '';
	$employer->address = isset($employer_address->{'street-address1'}) ? (string) $employer_address->{'street-address1'} : '';
	$employer->suburb = isset($employer_address->suburb) ? (string) $employer_address->suburb : '';
	$employer->state = isset($employer_address->state) ? (string) $employer_address->state : '';
	$employer->postcode = isset($employer_address->{'post-code'}) ? (string) $employer_address->{'post-code'} : '';
	$employer->office_phone = (string) $employer_phone;

	$response = "<table>
					<tr>
						<td width='30%'>Trading Name:</td>
						<td width='70%'>$employer->company</td>
					</tr>
					<tr>
						<td>Address</td>
						<td>
							$employer->address<br/>
							$employer->suburb, $employer->state $employer->postcode
						</td>
					</tr>
					<tr>
						<td>Office Phone</td>
						<td>$employer->office_phone</td>
					</tr>
				</table>";

	echo $response;
	
	die();
}



/**
 * 03. Shortcodes
 */

// Add the shortcode for the employer lookup
add_shortcode('neca_employer_lookup', 'neca_employer_lookup');

function neca_employer_lookup()
{
	$template = JR_ROOT_PATH . '/includes/views/employer_lookup.php';
	include $template;
}