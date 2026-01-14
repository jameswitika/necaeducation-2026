<?php
/*
 Plugin Name: NECA Testing Tools
 Plugin URI: https://smoothdevelopments.com.au
 Description: NECA Testing Tools consists of a feature that allows administrators to manual test the processing of a "WooCommerce Order". It's primary purpose is to debug the Job Ready API calls and to make sure everything is working as expected 
 Author: James Witika
 Author URI: https://smoothdevelopments.com.au
 */

// Hook for adding admin menus
add_action('admin_menu', 'necatt_add_pages');

// action function for above hook
function necatt_add_pages() {

	// Add a new submenu under Tools:
	add_management_page( __('NECA Testing Tools','menu-test'), __('NECA Testing Tools','menu-test'), 'manage_options', 'necatestingtools', 'necatt_tools_page');
}

// mt_tools_page() displays the page content for the Test Tools submenu
function necatt_tools_page()
{
	//must check that the user has the required capability
	if (!current_user_can('manage_options'))
	{
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	?>
	
	<div class="wrap">
	<h2>NECA Testing Tools</h2>
	
	<?php
	// See if the user has posted us some information
	// If they did, this hidden field will be set to 'Y'
	if( isset($_POST['necatt_submit_hidden']) && $_POST['necatt_submit_hidden'] == 'Y' )
	{
		// Read their posted value
		$order_id = $_POST['necatt_order_id'];
		$transaction_id = isset($_POST['necatt_transaction_id']) ? $_POST['necatt_transaction_id'] : 'AAAA1111';

		if($order_id != '')
		{
			echo "Manually setting Payment Reference to: " . $transaction_id . "<br/>";
			
			// Manually update the Transaction ID as it's a COD payment
			update_post_meta( $order_id, '_transaction_id', $transaction_id);

			echo "Processing Order: " . $order_id . "<br/>";
			
			// Process the Order
			define('JR_DEBUG_MODE', true);
			 woocommerce_order_completed_jobready_post_processing($order_id);
			
			 $path = 'tools.php?page=necatestingtools';
			 $url = admin_url($path);
			 $link = "<br/><h3><a href='{$url}'>[ TEST ANOTHER ORDER PROCESS ]</a></h3>";
			 
			 echo $link;
			 echo "</div>";
		}
    }
    else
    {
    	$args = array('numberposts' => -1, 'post_status' => 'wc-processing');
    	$processing_orders = wc_get_orders( $args );
    	$orders = array();
    	
	    // Output The NECA Testing Tools Form
	    ?>
			<p>Please use the tools below to test and debug the Job Ready process for specific orders containing application form products (i.e. short course - accredited, short course - non-accredited, pre-apprenticeship) 
			<form name="form1" method="post" action="">
				<input type="hidden" name="necatt_submit_hidden" value="Y">
				<table>
					<tr>
				  		<td valign="top">Order ID: </td>
				  		<td>
					  		<?php
					  		echo "<select name='necatt_order_id'>";
					  		foreach($processing_orders as $processing_order)
					  		{
					  			$order_id = $processing_order->get_order_number();
					  			$firstname = $processing_order->get_billing_first_name();
					  			$surname = $processing_order->get_billing_last_name();
					  			
					  			echo '<option value="'.$order_id.'">Order #'.$order_id.' - '.$firstname.' '.$surname.'</option>';
					  		}
					  		echo "</select>";
					  		?>
					</tr>
					<tr>
						<td valign="top">Payment Reference: </td>
						<td><input type="text" name="necatt_transaction_id" value="ABCD1234" size="20"><br/>Normally created by PayPal when a payment is made<br/>This reference will be included in your Job Ready payment</td>
					</tr>
				</table>
				<hr />
				<p class="submit">
					<input type="submit" name="Submit" class="button-primary" value="Process Order" />
				</p>
			</form>
		</div>
		<?php
    }
}

?>