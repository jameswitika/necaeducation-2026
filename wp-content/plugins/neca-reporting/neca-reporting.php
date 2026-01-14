<?php
/**
 * Plugin Name: NECA Reporting (Admin Version)
 * Plugin URI: https://smoothdevelopments.com.au
 * Description: Custom NECA Reporting (Admin Page, CSS and JS calls)
 * Version: 1.0
 * Author: James Witika
 * Author URI: https://smoothdevelopments.com.au
 * License: GPL2
 */

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Add a new submenu under DASHBOARD
function neca_reporting_plugin_starter_menu()
{
	// using array - same outcome, and can call JS with it
	// explained here: http://codex.wordpress.org/Function_Reference/wp_enqueue_script
	// and here: http://pippinsplugins.com/loading-scripts-correctly-in-the-wordpress-admin/
	global $starter_plugin_admin_page;
	$starter_plugin_admin_page = add_submenu_page ('index.php', __('NECA Reporting', 'plugin-starter'), __('NECA Reporting', 'plugin-starter'), 'manage_options', 'necaReporting', 'necaReporting');
	
}
add_action('admin_menu', 'neca_reporting_plugin_starter_menu');

// register our JS file
function neca_reporting_admin_init() {
	wp_register_script ('neca-reporting-script', plugins_url( '/neca-reporting-script.js', __FILE__ ));
}
add_action ('admin_init', 'neca_reporting_admin_init');

// now load the scripts we need
function neca_reporting_admin_scripts ($hook) {
	
	global $starter_plugin_admin_page;
	if ($hook != $starter_plugin_admin_page) {
		return;	
	}
	wp_enqueue_script ('jquery-ui-datepicker');
	wp_enqueue_script ('neca-reporting-script');
}
// and make sure it loads with our custom script
add_action('admin_enqueue_scripts', 'neca_reporting_admin_scripts');

// link some styles to the admin page
wp_enqueue_style( 'jquery-ui-datepicker-style' , '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
//$neca_reporting_styles = plugins_url ('neca-reporting-styles.css', __FILE__);
//wp_enqueue_style ('starterstyles', $neca_reporting_styles);


////////////////////////////////////////////
// here's the code for the actual admin page
function necaReporting () {
	
// check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient privileges to access this page. Sorry!') );
    }	
	
	///////////////////////////////////////
	// MAIN AMDIN CONTENT SECTION
	///////////////////////////////////////
	
	// display heading with icon WP style
	?>
    <div class="wrap">
	    <div id="icon-index" class="icon32"><br></div>
	    <h2>NECA Reporting Options</h2>
    
		<?php
    	// Create the nonce
    	$neca_report_nonce = wp_create_nonce( 'neca_report_generate' );
	    ?>
		<p>Please select your date range: </p> 
		<form target="_blank" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" id="neca_report_generate" novalidate="novalidate">
			<input type="hidden" name="neca_reporting_submit_hidden" value="Y">
			<table>
				<tr>
			  		<td valign="top">From Date</td>
			  		<td> to </td>
			  		<td valign="top">From Date</td>
				</tr>
				<tr>
			  		<td valign="top">
			  			<input type="text" class="jquery-datepicker" name="neca_reporting_start_date">
			  		</td>
			  		<td> to </td>
			  		<td valign="top">
			  			<input type="text" class="jquery-datepicker" name="neca_reporting_end_date">
			  		</td>
				</tr>
			</table>
			<hr />
			<p class="submit">
				<input type="hidden" name="action" value="neca_report_generate">
				<input type="hidden" name="_wpnonce" value="<?php echo $neca_report_nonce; ?>">
				<button type="submit" name="submit" class="button-primary" value="Generate Report">Generate Report</button>
			</p>
		</form>
	</div>
	
    <?php
} // end of main function


add_action( 'admin_post_neca_report_generate', 'neca_report_generate');
function neca_report_generate()
{
	// Check if the current user has sufficient permissions to perform this action
	neca_report_permissions();
	
	$report_args = array();
	$report_args['start_date'] = $_POST['neca_reporting_start_date'];
	$report_args['end_date'] = $_POST['neca_reporting_end_date'];
	
	// Check if the nonce was valid
	if( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'neca_report_generate') )
	{
		print_order_summary_report_xls($report_args);
		exit;
	}
	else
	{
		wp_die( 'Invalid nonce specified',
				'Error',
				array(	'response' 	=> 403 ) );
	}
		
}


function print_order_summary_report_xls($report_args)
{
	// Setup the wc_get_orders args
	$args = array(	'numberposts' => - 1,
			'orderby' => 'date_created',
			'order' => 'ASC',
			'type' => array( 'shop_order' ) );
	
	// Check if the "from_date" was specified
	if(isset($report_args['start_date']))
	{
		$end_date_from = date('Y-m-d 00:00:00', strtotime($report_args['start_date']));
		
		if(isset($report_args['end_date']))
		{
			$end_date_to = date('Y-m-d 00:00:00', strtotime('+1 day', strtotime($report_args['end_date']) ) );
		}
		else
		{
			$end_date_to = date('Y-m-d 00:00:00', strtotime('+1 day', strtotime($report_args['start_date']) ) );
		}
		
		$args['date_query'] = array('after' => $end_date_from,
									'before' => $end_date_to );
	}
	
	// Check if the "status_id" was specified
	if(isset($report_args['status_id']))
	{
		$args['post_status'] = array( $report_args['status_id'] );
	}
	
	// Load all order created in that month, which are on-hold or pending-payment
	$orders = wc_get_orders( $args );
	
	// Get all Payment Methods
	$payment_methods = getAllPaymentMethodsForSelect();
	
	// Get Status Options
	$status_list = wc_get_order_statuses();
	
	if(count($orders) > 0):
	
	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$sheet->setTitle('Order Summary Report');
	
	// Set the row_count
	$row_count = 1;
	
	// Output the table titles
	$titles = array('Order', 'Date', 'Status', 'Coupons Used', 'Coupon Amounts', 'Bulk Discount', 'Discount Total', 'Tax Total', 'Order Total', 'Payment Method', 'Billing Firstname', 'Billing Last Name');
	$start_cell = 'A' . $row_count;
	$sheet->fromArray($titles, NULL, $start_cell);
	$row_count++;
	
	// Setup variable for storing the totals
	$all_tax = 0;
	$all_subtotal = 0;
	
	foreach($orders as $order):
	
	$total_tax = 0;
	$total_amount = 0;
	
	// Load the Order Data
	$order_id = $order->get_id();
	$order_data = $order->get_data();
	$status = $order_data['status'];
	$order_payment_method = $order_data['payment_method'];
	$order_payment_method_title = $order_data['payment_method_title'];
	$order_date_created = $order_data['date_created']->date('M j, Y h:ia');
	$first_name = $order_data['billing']['first_name'];
	$last_name = $order_data['billing']['last_name'];
	
	// Load the Coupons related to this order
	$coupons = $order->get_used_coupons();
	
	$coupons_array = array();
	$coupon_amounts_array = array();
	$discount_total = 0;
	
	foreach( $coupons as $coupon_code )
	{
		$coupon = new WC_Coupon($coupon_code);
		$code = $coupon->get_code();
		$amount = $coupon->get_amount();
		$discount_total += $amount;
		
		$coupons_array[] = $code;
		$coupon_amounts_array[] = $amount;
	}
	
	$coupons_used = implode(',', $coupons_array);
	$coupon_amounts = implode(',', $coupon_amounts_array);
	
	// Determine if there were any bulk discounts applied (via woo_discount_log)
	$bulk_discount = 0;
	
	$woo_discount_log = $order->get_meta('woo_discount_log');
	if(strlen(trim($woo_discount_log)) > 0)
	{
		$woo_discount_log = str_replace("\"{","{",$woo_discount_log); // maybe_serialize adds an unwanted " before the first {
		$woo_discount_log = str_replace("}\"","}",$woo_discount_log); // maybe_serialize adds an unwanted " after the last }
		$woo_discount_log_decoded = json_decode($woo_discount_log, true);
		
		if(isset($woo_discount_log_decoded['price_discount']['line_discount']))
		{
			foreach($woo_discount_log_decoded['price_discount']['line_discount'] as $line => $line_data)
			{
				if(isset($line_data[0]['amount']['price_discount']))
				{
					$bulk_discount = (int) $line_data[0]['amount']['price_discount'];
				}
			}
		}
	}
	
	// Calculate the Discount Total
	$discount_total += $bulk_discount;
	
	// Loop through the Order Items
	foreach ($order->get_items() as $item_key => $item_values):
	
	$item_id = $item_values->get_id();
	$item_data = $item_values->get_data();
	
	$total_tax += $item_data['total_tax'];
	$total_amount += $item_data['total'];
	
	endforeach;
	
	$data = array(	$order_id,
			$order_date_created,
			$status,
			$coupons_used,
			$coupon_amounts,
			$bulk_discount,
			$discount_total,
			$total_tax,
			$total_amount,
			$order_payment_method_title,
			$first_name,
			$last_name
	);
	
	// Output the data
	$start_cell = 'A' . $row_count;
	$sheet->fromArray($data, NULL, $start_cell);
	$row_count++;
	
	endforeach;
	
	// File Name
	$writer = new Xlsx($spreadsheet);
	$filename = date('Ymd') . '_order_summary_report';
	
	// Save and download Excel
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'. $filename .'.xls"');
	header('Cache-Control: max-age=0');
	$writer->save('php://output');
	
	else:
		echo "No orders available";
	endif;
}


// Load the Payment Methods for Select
function getAllPaymentMethodsForSelect()
{
	$payment_methods = WC()->payment_gateways->payment_gateways();
	$results = array();
	foreach($payment_methods as $k => $v)
	{
		if($v->enabled == 'yes')
		{
			$results[$k] = $v->title;
		}
	}
	return $results;
}

function neca_report_permissions($display_error_and_die = true)
{
	// Check to make sure the user has sufficient capability
	if(is_user_logged_in())
	{
		if( current_user_can('manage_options') )
		{
			return true;
		}
	}
	
	if($display_error_and_die)
	{
		echo "Insufficient rights to generate reports";
		die();
	}
	else
	{
		return false;
	}
}
?>