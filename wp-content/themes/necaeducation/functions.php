<?php

// =============================================================================
// FUNCTIONS.PHP
// -----------------------------------------------------------------------------
// Overwrite or add your own custom functions to X Pro in this file.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Parent Stylesheet
//   02. Additional Functions
//   03. Remove WooCommerce Cart Product Title Link and replace with path to "Training with Us" instead
//   04. Remove X Theme Breadcrumb  and replace with Yoast Breadcrumbs
//   05. Updates the Breadcrumb and replaces the SHOP URL with a link to Training with us
//   06. Add Google Tag Manager Code
//   07. Add the Google Tag Manager (no script) option just after the opening <body> tag
//   07b. Add the HubSpot Embed Code option just after the opening <body> tag
//   08. Uses an ACF (Advanced Custom Field) called 'show_in_sidemenu' to determine pages to exclude
//   09. Add Banner menu in ADMIN OPTIONS
//   10. Create URL Shortcode for use in widgets (neca_site_url)
//   11. Function used to apply a color coding to all pages
//   12. Register Home Sidebar for Widgets
//   13. Sets up the blog layout
//   14. Tweak the Except string
//   15. Setup "the_pagination"
//   16. Overrides the URL for the Product for the Order Item Permalink
//   17. Changes the redirect URL for the Return To Shop button in the cart.
//   18. Change the return to shop text to something more applicable
//   19. Change the redirect for the "Continue Shopping" link on WooCommerce
//   20. Overrides the URL for the Product displayed in cart for various forms
//   21. Overrides the data being displayed in the cart for the various Gravity Forms
//   22. WooCommerce Order Completion Filter
//   23. X Theme + WooCommerce Cart issue fix.
//   24. Removes the Order Notes from the Checkout Fields in WooCommerce
//   25. Remove Order Notes Field
//   26. Pre-populate WooCommerce Billing Details
//   27. Disable autocomplete in password fields via Javascript on /login/ and /my-account/
//   28. ENABLE HTTP STRICT TRANSPORT SECURITY (HSTS) IN WORDPRESS
//   29. Block WP User Enumeration Scans
//   30. Disable Author Archives
//   31. Disable the "Order Again" button on the thank you screen
//   32. Unregistering Portfolio CPT
//   33. AUTO COMPLETE PAID ORDERS IN WOOCOMMERCE
//   34. Removing Query Strings from Scripts - As part of PageSpeed improvements
// =============================================================================

// Enqueue Parent Stylesheet
// =============================================================================

add_filter( 'x_enqueue_parent_stylesheet', '__return_true' );

function add_scripts() {
    wp_enqueue_style( 'fonts', get_stylesheet_directory_uri() . '/fonts/stylesheet.css' ); 
    
    wp_enqueue_script( 'scripts', get_stylesheet_directory_uri() . '/js/scripts.js' ); 
}
add_action( 'wp_enqueue_scripts', 'add_scripts' );

// Additional Functions
// =============================================================================


// 03. Remove WooCommerce Cart Product Title Link and replace with path to "Training with Us" instead
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
add_action( 'woocommerce_before_shop_loop_item', 'neca_my_training_loop_open', 10);

function neca_my_training_loop_open()
{
	$cart_link = '/training-with-us';
	echo '<a href="' . $cart_link . '" class="woocommerce-LoopProduct-link">';
}


// 04. Remove X Theme Breadcrumb  and replace with Yoast Breadcrumbs
if ( ! function_exists( 'x_breadcrumbs' ) ) :
function x_breadcrumbs() {
	
	if ( x_get_option( 'x_breadcrumb_display', 1 ) && function_exists('yoast_breadcrumb') ) {
		yoast_breadcrumb('<p id="breadcrumbs" class="x-breadcrumbs">','</p>');
	}
	
}
endif;

// 05. Updates the Breadcrumb and replaces the SHOP URL with a link to Training with us
// Replace the SHOP URL with a link to "Training with Us"
// Not quite working
add_filter( 'wpseo_breadcrumb_links', 'wpseo_breadcrumb_change_shop_url' );
function wpseo_breadcrumb_change_shop_url( $links ) {
	global $post;
	if ( is_woocommerce() )
	{
		$new_links = array();
		foreach($links as $link)
		{
			if(isset($link['ptarchive']) && $link['ptarchive'] == 'product')
			{
				$link['url'] = get_site_url() . '/training-with-us';
				$link['text'] = 'Courses';
				$link['allow_html'] = true;
				unset($link['ptarchive']);
			}
			
			array_push($new_links, $link);
		}
	}
	else
	{
		$new_links = $links;
	}
	return $new_links;
}




// 06. Add Google Tag Manager Code
// =============================================================================

// Add the Google Tag Manager after the opening <head> tag
function add_google_tag_manager_code()
{
	?>

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-TM3PSLZ');</script>
<!-- End Google Tag Manager -->

<?php 
}

add_action( 'wp_head', 'add_google_tag_manager_code', 0 );



// 07. Add the Google Tag Manager (no script) option just after the opening <body> tag

add_action( 'wp_after_body', 'add_google_tag_manager_noscript_code', 0 );
//add_action( 'x_before_site_begin', 'add_google_tag_manager_noscript_code', 0 );

function add_google_tag_manager_noscript_code()
{
	?>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TM3PSLZ"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<?php 
}


// 07b. Add the HubSpot Embed Code option just after the opening <body> tag

//add_action( 'wp_after_body', 'add_hubspot_code', 1 );
/* Removed by JW - 07.08.2024
function add_hubspot_code()
{
	?>

<!-- Start of HubSpot Embed Code -->
<script type="text/javascript" id="hs-script-loader" async defer src="//js.hs-scripts.com/6007074.js"></script>
<!-- End of HubSpot Embed Code -->

<?php 
}
*/



// 08. Uses an ACF (Advanced Custom Field) called 'show_in_sidemenu' to determine pages to exclude
// Excluded pages from the Advanced Sidebar Menu
add_action('advanced_sidebar_menu_excluded_pages','exclude_a_page', 10, 5);
function exclude_a_page( $excluded, $current_page, $widget_args, $widget_values, $menu_class )
{
	$posts = get_posts(array(
			'numberposts'	=> -1,
			'post_type'		=> 'page',
			'meta_key'		=> 'show_in_sidemenu',
			'meta_value'	=> 'no'
	));
	
	$excluded = array();
	foreach($posts as $post)
	{
		$excluded[] = $post->ID;
	}
	
	return $excluded;
}



// 09. Add Banner menu in ADMIN OPTIONS
if( function_exists('acf_add_options_page') ) {
	acf_add_options_sub_page(array(
		'page_title' 	=> 'Banners',
		'menu_title'	=> 'Banners',
		'parent_slug'	=> 'edit.php?post_type=page',
	));
}


// 10. Create URL Shortcode for use in widgets
function neca_site_url() {
	return site_url();
}
add_shortcode('neca_site_url','neca_site_url');


// 11. Function used to apply a color coding to all pages
function neca_color_coding(){
    global $post;
    
    if(is_page() || is_job_ready_course()):
        $ancestor_id = array_reverse(get_post_ancestors($post->ID));

        if($ancestor_id):
            $slug = get_post_field( 'post_name', $ancestor_id[0] );
        else:
            $slug = $post->post_name;
        endif;
    
        if($slug == 'job-seekers'):
            ?>
            <script>
                jQuery(document).ready(function($){
                    $('body').addClass('light-blue');
                });
            </script>
            <?php
        elseif($slug == 'employers'):
            ?>
            <script>
                jQuery(document).ready(function($){
                    $('body').addClass('lime-green');
                });
            </script>
            <?php
            elseif($slug == 'training-with-us' || is_job_ready_course()):
            ?>
            <script>
                jQuery(document).ready(function($){
                    $('body').addClass('orange');
                });
            </script>
            <?php
        elseif($slug == 'resource-hub' || $slug == 'category' || $slug == 'downloads'):
            ?>
            <script>
                jQuery(document).ready(function($){
                    $('body').addClass('dark-blue');
                });
            </script>
            <?php
        endif;
    
    endif;
}
add_action('wp_head', 'neca_color_coding');



// 12. Register Home Sidebar for Widgets
add_action( 'widgets_init', 'theme_slug_widgets_init' );
function theme_slug_widgets_init() {
    register_sidebar( array(
        'name' => __( 'Home Sidebar', 'theme-slug' ),
        'id' => 'home-sidebar',
        'description' => __( 'Widgets in this area will be shown in home pages.', 'theme-slug' ),
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
	'after_widget'  => '</li>',
	'before_title'  => '<h4 class="widgettitle">',
	'after_title'   => '</h4>',
    ) );
}



// 13. Sets up the blog layout
add_shortcode('blog', 'blog_func');

function blog_func()
{
	global $post;
    $paged = ( get_query_var( 'paged') ) ? get_query_var( 'paged') : 1;
    $args = array( 
        'post_type'=> 'post',
        'posts_per_page' => 6,
        'paged' => $paged,
    ); 
    $query = new WP_Query($args); 
?>

<div class="blog-posts">
    <?php if ($query->have_posts() ) while ( $query->have_posts() ) : $query->the_post(); ?>
    <section>
        <?php if(has_post_thumbnail()) { ?>
        <?php the_post_thumbnail('medium_large'); ?>
        <?php } // end of has_post_thumbnail() ?>
        <article>
            <h4><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h4>
            <div class="date"><?php echo date('j F Y'); ?></div>
            <?php the_excerpt(); ?>
        </article>
    </section>
    <?php endwhile; the_pagination($query->max_num_pages); wp_reset_query(); // end of the loop. ?>    
</div>
<?php
}



// 14. Tweak the Except string
function new_excerpt_more($more) {
    global $post;
	return '...';
}
add_filter('excerpt_more', 'new_excerpt_more', 999);

function custom_excerpt_length( $length ) {
	return 25;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );



// 15. Setup "the_pagination"
function the_pagination($pages = '', $range = 3)
{
     $showitems = ($range * 2)+1;

     global $paged;
     if(empty($paged)) $paged = 1;

     if($pages == '')
     {
         global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages)
         {
             $pages = 1;
         }
     }

     if(1 != $pages)
     {
         echo "<div class='paging'>";
         if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo;</a>";
         if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo;</a>";

         for ($i=1; $i <= $pages; $i++)
         {
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
             {
                 echo ($paged == $i)? "<span class='current'>".$i."</span>":"<a href='".get_pagenum_link($i)."' class='inactive' >".$i."</a>";
             }
         }

         if ($paged < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($paged + 1)."'>&rsaquo;</a>";
         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>&raquo;</a>";
         echo "</div>\n";
     }
}



// 16. Overrides the URL for the Product for the Order Item Permalnk
add_filter( 'woocommerce_order_item_permalink', 'neca_order_item_permalink', 10, 3 );

function neca_order_item_permalink($product_get_permalink_item, $item, $order)
{
	$form_id = 0;
	$meta_datas = $item->get_meta_data();

	foreach($meta_datas as $meta_data)
	{
		if($meta_data->key == '_gravity_forms_history')
		{
			$gfh = $meta_data->value;
			$form_id = $gfh['_gravity_form_lead']['form_id'];
			//echo "Form ID: " . $form_id . "<br/>";
			break;
		}
	}
	
	switch($form_id)
	{
		case PRE_APPRENTICE_APPLICATION_FORM :
		case SHORT_COURSE_APPLICATION_FORM_NON_ACCREDITED :
		case SHORT_COURSE_APPLICATION_FORM_ACCREDITED :
        case NASC_REGISTRATION_FORM :
        case IOT_FORM_ID :
			//$product_get_permalink_cart_item = site_url() . '/training-with-us/';
			$product_get_permalink_cart_item = '';
			break;
	}
	return $product_get_permalink_cart_item;
}


/**
 * 17. Changes the redirect URL for the Return To Shop button in the cart.
 *
 * @return string
 */
function wc_empty_cart_redirect_url() {
	return site_url() . '/training-with-us/';
}
add_filter( 'woocommerce_return_to_shop_redirect', 'wc_empty_cart_redirect_url' );



/**
 * 18. Change the return to shop text to something more applicable
 */

add_filter( 'gettext', 'change_woocommerce_text', 20, 3 );

function change_woocommerce_text( $translated, $text, $domain )
{
//	echo "Translated text: " .$translated_text . "<br/>";
	
	if ( $domain === 'woocommerce' )
	{
		$translated = str_replace(
				array( 'Return to shop', 'Product', 'Your order'),
				array( 'Return to Training With Us', 'Course', 'Your Registration'),
				$translated );
	}
	return $translated;
}



// 19. Change the redirect for the "Continue Shopping" link on WooCommerce
function my_woocommerce_continue_shopping_redirect( $return_to ) {
	$continue_shopping_url = site_url() . '/training-with-us/training/';
	return $continue_shopping_url;
}
add_filter( 'woocommerce_continue_shopping_redirect', 'my_woocommerce_continue_shopping_redirect', 20 );



// 20. Overrides the URL for the Product displayed in cart for various forms
add_filter( 'woocommerce_cart_item_permalink', 'neca_cart_item_permalink', 10, 3 );

function neca_cart_item_permalink( $product_get_permalink_cart_item, $cart_item, $cart_item_key )
{
	$form_id = 0;
	if(isset($cart_item['_gravity_form_lead']))
	{
		$form_data = $cart_item['_gravity_form_lead'];
		$form_id = (int) $form_data['form_id'];
		$new_item_data = array();
	}
	
	switch($form_id)
	{
		case PRE_APPRENTICE_APPLICATION_FORM :
			//$product_get_permalink_cart_item = site_url() . '/training-with-us/';
			$product_get_permalink_cart_item = '';
			break;
		
		case SHORT_COURSE_APPLICATION_FORM_NON_ACCREDITED :
			//$product_get_permalink_cart_item = site_url() . '/training-with-us/';
			$product_get_permalink_cart_item = '';
			break;
			
		case SHORT_COURSE_APPLICATION_FORM_ACCREDITED :
			//$product_get_permalink_cart_item = site_url() . '/training-with-us/';
			$product_get_permalink_cart_item = '';
			break;

		/*	
		case SHORT_COURSE_APPLICATION_NECCLV004 :
			//$product_get_permalink_cart_item = site_url() . '/training-with-us/';
			$product_get_permalink_cart_item = '';
			break;
		*/

        case IOT_FORM_ID :
            //$product_get_permalink_cart_item = site_url() . '/training-with-us/';
            $product_get_permalink_cart_item = '';
            break;
	}
	return $product_get_permalink_cart_item;
}


// 21. Overrides the data being displayed in the cart for the various Gravity Forms
function neca_woocommerce_get_item_data( $item_data, $cart_item )
{
	$form_id = 0;
	if(isset($cart_item['_gravity_form_lead']))
	{
		$form_data = $cart_item['_gravity_form_lead'];
		$form_id = (int) $form_data['form_id'];
		$new_item_data = array();
	}
	
	switch($form_id)
	{
		case PRE_APPRENTICE_APPLICATION_FORM :
			$new_item = array();
			$course_scope_code = $form_data['77'];
			$course_number = $form_data['78'];
			$name = $form_data['9'] . ' ' . $form_data['8'];
			$payment_option = $form_data['98'];
			$payment_option = strstr($payment_option,"|",true);
			
			$jrd =JobReadyDateOperations::loadJobReadyDateByCourseNumber($course_number);
			$start_date = $jrd->start_date_clean;

			$new_item = neca_gf_setup_item('Course Name', $payment_option);
			array_push($new_item_data, $new_item);

			$new_item = neca_gf_setup_item('Pre-Training Review', $course_number);
			array_push($new_item_data, $new_item);
			
			//$new_item = neca_gf_setup_item('Start Date', $start_date);
			//array_push($new_item_data, $new_item);
						
			$new_item = neca_gf_setup_item('Student Name', $name);
			array_push($new_item_data, $new_item);
						
			break;

		case SHORT_COURSE_APPLICATION_FORM_NON_ACCREDITED :
		
			$new_item = array();
			$course_option_array = explode("|", $form_data['26']);
			$course_option = $course_option_array[0];
			$course_scope_code = $form_data['22'];
			$course_number = $form_data['23'];
			$name = $form_data['9'] . ' ' . $form_data['8'];
			
			$jrd =JobReadyDateOperations::loadJobReadyDateByCourseNumber($course_number);
			$course_name = $jrd->course_name . " (" . $jrd->start_date_clean. " to " . $jrd->end_date_clean . ")";
			$course_duration = $jrd->start_date_clean. " to " . $jrd->end_date_clean;
						
			$new_item = neca_gf_setup_item('Course Name', $course_option);
			array_push($new_item_data, $new_item);
			
			$new_item = neca_gf_setup_item('Course Duration', $course_duration);
			array_push($new_item_data, $new_item);
			
			$new_item = neca_gf_setup_item('Student Name', $name);
			array_push($new_item_data, $new_item);
			
			break;
			
			
		case SHORT_COURSE_APPLICATION_FORM_ACCREDITED :
		
			$new_item = array();
			$course_scope_code = $form_data['69'];
			$course_number = $form_data['70'];
			$name = $form_data['9'] . ' ' . $form_data['8'];
			
			$jrd =JobReadyDateOperations::loadJobReadyDateByCourseNumber($course_number);
			$course_name = $jrd->course_name;
			$course_duration = $jrd->start_date_clean. " to " . $jrd->end_date_clean;
			$payment_option = $form_data['72'];
			$payment_option = strstr($payment_option,"|",true);
			
			$new_item = neca_gf_setup_item('Course Name', $payment_option);
			array_push($new_item_data, $new_item);
			
			$new_item = neca_gf_setup_item('Course Duration', $course_duration);
			array_push($new_item_data, $new_item);
			
			$new_item = neca_gf_setup_item('Student Name', $name);
			array_push($new_item_data, $new_item);
			
			break;
			
		/*	
		case SHORT_COURSE_APPLICATION_NECCLV004 :
		
			$new_item = array();
			$course_scope_code = $form_data['69'];
			$course_number = $form_data['70'];
			$name = $form_data['9'] . ' ' . $form_data['8'];
			$course_name = $course_number;
			
			//$course_name = $jrd->course_name;
			$course_duration = $jrd->start_date_clean. " to " . $jrd->end_date_clean;
			
			$new_item = neca_gf_setup_item('Course Name', $course_name);
			array_push($new_item_data, $new_item);
			
			$new_item = neca_gf_setup_item('Student Name', $name);
			array_push($new_item_data, $new_item);
			
			break;
		*/

        case IOT_FORM_ID :
            
            $new_item = array();
            $course_scope_code = $form_data['81'];
            $course_number = $form_data['192'];
            $name = $form_data['9'] . ' ' . $form_data['8'];
            $course_name = $course_number;
            
			$jrd =JobReadyDateOperations::loadJobReadyDateByCourseNumber($course_number);
            //$course_name = $jrd->course_name;
            $course_duration = $jrd->start_date_clean. " to " . $jrd->end_date_clean;
            
            $new_item = neca_gf_setup_item('Course Name', $course_name);
            array_push($new_item_data, $new_item);
            
            $new_item = neca_gf_setup_item('Student Name', $name);
            array_push($new_item_data, $new_item);
            
            break;
			
		default:
			$new_item_data = $item_data;
	}
	
	return $new_item_data;
}


/*
 * 22. WooCommerce Order Completion Filter
 * Removes the excessive information being displayed for each WC_Order_Item_Product containing gravity form meta
 * Simplifies the output to include the Course Scope Code + Course Number only
 */
function neca_filter_woocommerce_gf_order_items_meta_display( $output, $instance )
{
	$meta_datas = $instance->get_meta_data();
	$form_data = new stdClass();
	foreach($meta_datas as $meta_data)
	{
		if($meta_data->key == '_gravity_forms_history')
		{
			$form_data->gravity_form_linked_entry_id = isset($meta_data->value['_gravity_form_linked_entry_id']) ? (int) $meta_data->value['_gravity_form_linked_entry_id'] : 0;
			$form_data->form_id = (int) $meta_data->value['_gravity_form_lead']['form_id'];
		}
		else
		{
			$form_data->{$meta_data->key} = $meta_data->value;
		}
	}
	
	if(isset($form_data->form_id))
	{
		//var_dump($form_data);
		$course_scope_code = $form_data->{'_Course Scope Code'};
		$course_number = $form_data->{'_Course Number'};
		if(isset($form_data->{'Course'}))
		{
			$course_option = $form_data->{'Course'};
		}
		elseif(isset($form_data->{'Cost'}))
		{
			$course_option = $form_data->{'Cost'};
		}
		else 
		{
			$course_option = $form_data->{'Course Option'};
		}
		
		$jrd =JobReadyDateOperations::loadJobReadyDateByCourseNumber($course_number);
		$course_name = $jrd->course_name . " (" . $jrd->start_date_clean. " to " . $jrd->end_date_clean . ")";
		
		$new_output = "	<div><strong>Student Name:</strong> " . 
                        (isset($form_data->{'First Name'}) ? $form_data->{'First Name'} . " " : '') . 
                        (isset($form_data->{'Given Name/s'}) ? $form_data->{'Given Name/s'} . " " : '') .
                        (isset($form_data->{'Family Name'}) ? $form_data->{'Family Name'} : '') . "<br/>
						<strong>Course Name: </strong>" . $course_option . "<br/>
						<strong>Course Number: </strong> " . $course_number. "</div>";
		
		return $new_output;
	}
	else
	{
		// Return the original output
		return $output;
	}
};

// add the filter
add_filter( 'woocommerce_display_item_meta', 'neca_filter_woocommerce_gf_order_items_meta_display', 10, 2 );




// add the filter
add_filter( 'woocommerce_get_item_data', 'neca_woocommerce_get_item_data', 10, 2 ); 

function neca_gf_setup_item($name, $value)
{
	$result = array(	'name' 		=> $name,
						'display'	=> $value,
						'value' 	=> $value,
						'hidden'	=> false);
	
	return $result;
}


// 23. X Theme + WooCommerce Cart issue fix. The error was: 
// "Notice: WC_Cart::get_cart_url is deprecated since version 2.5! Use wc_get_cart_url instead. in /var/www/public/necaeducation.com.au/wp-includes/functions.php on line 3837"

function x_woocommerce_navbar_menu_item( $items, $args ) {
	
	if ( X_WOOCOMMERCE_IS_ACTIVE && x_get_option( 'x_woocommerce_header_menu_enable' ) == '1' ) {
		if ( $args->theme_location == 'primary' ) {
			$items .= '<li class="menu-item current-menu-parent x-menu-item x-menu-item-woocommerce">'
		. '<a href="' . wc_get_cart_url()  . '" class="x-btn-navbar-woocommerce">'
				. x_woocommerce_navbar_cart()
				. '</a>'
		. '</li>';
		}
	}
	
	return $items;
	
}
add_filter( 'wp_nav_menu_items', 'x_woocommerce_navbar_menu_item', 9999, 2 );



// 24. Removes the Order Notes from the Checkout Fields in WooCommerce
// Removes Order Notes Title - Additional Information
add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );



// 25. Remove Order Notes Field
add_filter( 'woocommerce_checkout_fields' , 'remove_order_notes' );

function remove_order_notes( $fields ) {
	unset($fields['order']['order_comments']);
	return $fields;
}


// 26. Pre-populate WooCommerce Billing Details
/**
 * Pre-populate Woocommerce checkout fields
 */
add_filter('woocommerce_checkout_get_value', 'prepopulate_woocommerce_billing_details', 10, 2);

function prepopulate_woocommerce_billing_details($input, $key) {

    if (empty($_SESSION['prefill']) || !is_object($_SESSION['prefill'])) {
        return $input;
    }

    $p = $_SESSION['prefill'];

    switch ($key) {

        case 'billing_first_name':
            return $p->first_name ?? $input;

        case 'billing_last_name':
            return $p->surname ?? $input;

        case 'billing_address_1':
            return $p->street_address1 ?? $input;

        case 'billing_city':
            return $p->suburb ?? $input;

        case 'billing_state':
            $states = [
                'ACT' => 'Australian Capital Territory',
                'NSW' => 'New South Wales',
                'NT'  => 'Northern Territory',
                'QLD' => 'Queensland',
                'SA'  => 'South Australia',
                'TAS' => 'Tasmania',
                'VIC' => 'Victoria',
                'WA'  => 'Western Australia',
            ];

            $search = $p->state ?? '';
            // If they already give you 'VIC', use it. If they give 'Victoria', map it.
            if (isset($states[$search])) {
                return $search;
            }
            $code = array_search($search, $states, true);
            return $code ?: $input;

        case 'billing_postcode':
            return $p->postcode ?? $input;

        case 'billing_phone':
            if (!empty($p->home_phone))   return $p->home_phone;
            if (!empty($p->mobile_phone)) return $p->mobile_phone;
            return $input;

        case 'billing_email':
            return $p->email ?? $input;
    }

    return $input;
}


// 27. Disable autocomplete in password fields via Javascript on /login/ and /my-account/ 
function neca_disable_autocomplete_login() {
	echo <<<html
<script>
    document.getElementById( "user_pass" ).autocomplete = "off";
	document.getElementById( "password" ).autocomplete = "off";
</script>
html;
}

function neca_disable_autocomplete_myaccount() {
	echo <<<html
<script>
	document.getElementById( "password" ).autocomplete = "off";
</script>
html;
}

add_action( 'login_form', 'neca_disable_autocomplete_login' );
add_action( 'woocommerce_login_form', 'neca_disable_autocomplete_myaccount' );



// 28. ENABLE HTTP STRICT TRANSPORT SECURITY (HSTS) IN WORDPRESS
add_action( 'send_headers', 'tgm_io_strict_transport_security' );
/**
 * Enables the HTTP Strict Transport Security (HSTS) header.
 *
 * @since 1.0.0
 */
function tgm_io_strict_transport_security()
{
	header( 'Strict-Transport-Security: max-age=10886400' );
}



// 29. Block WP User Enumeration Scans
if (!is_admin()) {
	// default URL format
	if (preg_match('/author=([0-9]*)/i', $_SERVER['QUERY_STRING'])) die();
	add_filter('redirect_canonical', 'shapeSpace_check_enum', 10, 2);
}
function shapeSpace_check_enum($redirect, $request) {
	// permalink URL format
	if (preg_match('/\?author=([0-9]*)(\/*)/i', $request)) die();
	else return $redirect;
}



// 30. Disable Author Archives
function shapeSpace_disable_author_archives()
{
	if (is_author())
	{
		global $wp_query;
		$wp_query->set_404();
		status_header(404);
	}
	else
	{
		redirect_canonical();
	}
}
remove_filter('template_redirect', 'redirect_canonical');
add_action('template_redirect', 'shapeSpace_disable_author_archives');



// 31. Disable the "Order Again" button on the thank you screen
remove_action( 'woocommerce_order_details_after_order_table', 'woocommerce_order_again_button' );



// 32. Unregistering Portfolio CPT
// =============================================================================

add_action( 'after_setup_theme','remove_portfolio_cpt', 100 );

function remove_portfolio_cpt() {   
  remove_action( 'init', 'x_portfolio_init');    
}



// 33. AUTO COMPLETE PAID ORDERS IN WOOCOMMERCE
// 07.12.2021 - Disabled by JW due to duplicate student creation for PayPal payments on Virtual Products.
//add_action( 'woocommerce_thankyou', 'custom_woocommerce_auto_complete_paid_order', 10, 1 );
add_action( 'woocommerce_order_status_processing', 'custom_woocommerce_auto_complete_paid_order', 10, 1);

function custom_woocommerce_auto_complete_paid_order( $order_id ) {
	if ( ! $order_id )
		return;
		
		$order = wc_get_order( $order_id );
		
		// No updated status for orders delivered with Bank wire, Cash on delivery and Cheque payment methods.
		if ( ( 'bacs' == get_post_meta($order_id, '_payment_method', true) ) || ( 'cod' == get_post_meta($order_id, '_payment_method', true) ) || ( 'cheque' == get_post_meta($order_id, '_payment_method', true) ) ) {
			return;
		}
		// "completed" updated status for paid Orders with all others payment methods
		else {
			$order->update_status( 'completed' );
		}
}



// 34. Removing Query Strings from Scripts - As part of PageSpeed improvements
function _remove_script_version( $src )
{
  $parts = explode( '?ver', $src );
  return $parts[0];
}
add_filter( 'script_loader_src', '_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', '_remove_script_version', 15, 1 );