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


// Add Google Tag Manager Code
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
})(window,document,'script','dataLayer','GTM-NJTRDGR');</script>
<!-- End Google Tag Manager -->

<?php 
}

add_action( 'wp_head', 'add_google_tag_manager_code', 0 );


// Add the Google Tag Manager (no script) option just after the opening <body> tag
function add_google_tag_manager_noscript_code()
{
	?>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NJTRDGR"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<?php 
}

add_action( 'x_before_site_begin', 'add_google_tag_manager_noscript_code', 0 );


// End Add Google Tag Manager Code
// =============================================================================


// Require authentication for all requests
// TODO: Determine if this is warranted or not
add_filter( 'rest_authentication_errors', function( $result ) {
	if ( ! empty( $result ) ) {
		return $result;
	}
	if ( ! is_user_logged_in() ) {
		return new WP_Error( 'rest_not_logged_in', 'You are not currently logged in.', array( 'status' => 401 ) );
	}
	return $result;
});


// CRON Related
// Disable the WP_CRON from the wp-config.php file 

// Setup your event code using wp_schedule_event
add_action( 'neca_daily_event',  'neca_job_ready_daily_sync' );

function activate() {
	wp_schedule_event( time(), 'daily', 'neca_daily_event' );
}

function deactivate() {
	wp_clear_scheduled_hook('neca_daily_event');
}

function neca_job_ready_daily_sync()
{
	// Remove expired job_ready_courses
	JobReadyDateOperations::removeExpiredJobReadyDates();

	// Perform the SYNC process to update the job_ready_courses
	job_ready_sync();
	
	// Send email notification based on success or fail
	$to = 'james@smoothdevelopments.com.au';
	$subject = 'CRON Daily Script successfully completed';
	$message = 'CRON Daily Script successfully completed on NECA Education website';
	wp_mail( $to, $subject, $message);
}



// Uses an ACF (Advanced Custom Field) called 'show_in_sidemenu' to determine pages to exclude
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



/*options*/
if( function_exists('acf_add_options_page') ) {
	acf_add_options_sub_page(array(
		'page_title' 	=> 'Banners',
		'menu_title'	=> 'Banners',
		'parent_slug'	=> 'edit.php?post_type=page',
	));
}


// Create URL Shortcode for use in widgets
function neca_site_url() {
	return site_url();
}
add_shortcode('neca_site_url','neca_site_url');


// Function used to apply a color coding to everything
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
        elseif($slug == 'career-advisor'):
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



add_action( 'widgets_init', 'theme_slug_widgets_init' );
function theme_slug_widgets_init() {
    register_sidebar( array(
        'name' => __( 'Home Sidebar', 'theme-slug' ),
        'id' => 'home-sidebar',
        'description' => __( 'Widgets in this area will be shown inn home pages.', 'theme-slug' ),
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
	'after_widget'  => '</li>',
	'before_title'  => '<h4 class="widgettitle">',
	'after_title'   => '</h4>',
    ) );
}

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

/*the excerpt*/
function new_excerpt_more($more) {
    global $post;
	return '...';
}
add_filter('excerpt_more', 'new_excerpt_more', 999);

function custom_excerpt_length( $length ) {
	return 25;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

/*pagination*/
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




// GRAVITY FORM: 5 - Job Registration custom javascript
// Disables the "job reference" dynamically populated field
function gf_job_registration_custom_js() { ?>

    <script type="text/javascript">
        jQuery(document).ready(function($){
            $("input#input_5_22.medium").attr("readonly", "readonly");
        });
    </script>

<?php

}

// Only loads this javascript for form 5: Job Registration
add_action( 'gform_enqueue_scripts_5', 'gf_job_registration_custom_js', 10, 2 );




// Overrides the data being displayed for the various Gravity Forms
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
		case NON_APPRENTICE_APPLICATION_FORM :
			$new_item = array();
			$course_scope_code = $form_data['77'];
			$course_number = $form_data['78'];
			$name = $form_data['9'] . ' ' . $form_data['8'];

			$new_item = neca_gf_setup_item('Course Scope Code', $course_scope_code);
			array_push($new_item_data, $new_item);

			$new_item = neca_gf_setup_item('Course Number', $course_number);
			array_push($new_item_data, $new_item);
			
			$new_item = neca_gf_setup_item('Name', $name);
			array_push($new_item_data, $new_item);
						
			break;

		case SHORT_COURSE_APPLICATION_FORM_NON_ACCREDITED :
		
			$new_item = array();
			$course_scope_code = $form_data['22'];
			$course_number = $form_data['23'];
			$name = $form_data['9'] . ' ' . $form_data['8'];
			
			$new_item = neca_gf_setup_item('Course Scope Code', $course_scope_code);
			array_push($new_item_data, $new_item);
			
			$new_item = neca_gf_setup_item('Course Number', $course_number);
			array_push($new_item_data, $new_item);
			
			$new_item = neca_gf_setup_item('Name', $name);
			array_push($new_item_data, $new_item);
			
			break;
			
			
		case SHORT_COURSE_APPLICATION_FORM_ACCREDITED :
		
			$new_item = array();
			$course_scope_code = $form_data['69'];
			$course_number = $form_data['70'];
			$name = $form_data['9'] . ' ' . $form_data['8'];
			
			$new_item = neca_gf_setup_item('Course Scope Code', $course_scope_code);
			array_push($new_item_data, $new_item);
			
			$new_item = neca_gf_setup_item('Course Number', $course_number);
			array_push($new_item_data, $new_item);
			
			$new_item = neca_gf_setup_item('Name', $name);
			array_push($new_item_data, $new_item);
			
			break;
			
		default:
			$new_item_data = $item_data;
	}
	
	return $new_item_data;
}

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