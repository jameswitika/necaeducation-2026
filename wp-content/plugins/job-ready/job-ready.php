<?php
/**
 * Plugin Name: Job Ready
 * Plugin URI: http://smoothdevelopments.com.au
 * Description: This plugin was custom build for NECA Education for the Job Ready Integration
 * Version: 1.0.0
 * Author: James Witika
 * Author URI: http://smoothdevelopments.com.au
 */

// Prevent direct file access.
if (! defined ( 'ABSPATH' )) {
	header ( 'HTTP/1.0 403 Forbidden' );
	exit ();
}

// Path info
define ( 'JR_ROOT_FILE', __FILE__ );
define ( 'JR_ROOT_PATH', dirname ( __FILE__ ) );
define ( 'JR_ROOT_URL', plugins_url ( '', __FILE__ ) );

// Include various settings which will differ from environment to environment (so as to not overrite them)
// This includes defining the FORM ID's as well as the Job Ready Path (LIVE or STAGING)
include JR_ROOT_PATH . '/includes/settings.php';

// JobReady API Authentication Headers
// Basic Authentication headers (don't use in production) with Content-Type set to XML
global $jr_api_headers;
$jr_api_headers = array (
		'Authorization' => 'Basic ' . base64_encode ( 'key' . ':' . 'b90d211506c971a3e1cd8530cd3d4f0ae3602083' ),
		'Content-Type' => 'text/xml' 
);

// Includes additional general + sync related functions
include JR_ROOT_PATH . '/includes/general.php';
include JR_ROOT_PATH . '/classes/JobReadyCourse.php';
include JR_ROOT_PATH . '/classes/JobReadyDate.php';
include JR_ROOT_PATH . '/classes/JobReadyEmployer.php';
include JR_ROOT_PATH . '/classes/JobReadyForm.php';
include JR_ROOT_PATH . '/classes/JobReadyGTO.php';
include JR_ROOT_PATH . '/includes/sync.php';

// Includes JobReady API classes
include JR_ROOT_PATH . '/classes/JRACourse.php';
include JR_ROOT_PATH . '/classes/JRAEmployee.php';
include JR_ROOT_PATH . '/classes/JRAEmployer.php';
include JR_ROOT_PATH . '/classes/JRAEnrolment.php';
include JR_ROOT_PATH . '/classes/JRAEnrolmentCommencingProgramCohortIdentifier.php';
include JR_ROOT_PATH . '/classes/JRAEnrolmentAdhoc.php';
include JR_ROOT_PATH . '/classes/JRAEmployeeEnrolment.php';
include JR_ROOT_PATH . '/classes/JRAInvoice.php';
include JR_ROOT_PATH . '/classes/JRAParty.php';
include JR_ROOT_PATH . '/classes/JRAPartyAddress.php';
include JR_ROOT_PATH . '/classes/JRAPartyAdhoc.php';
include JR_ROOT_PATH . '/classes/JRAPartyAvetmiss.php';
include JR_ROOT_PATH . '/classes/JRAPartyContact.php';
include JR_ROOT_PATH . '/classes/JRAPartyContactDetail.php';
include JR_ROOT_PATH . '/classes/JRAPartyCricos.php';
include JR_ROOT_PATH . '/classes/JRAPartyDocument.php';
include JR_ROOT_PATH . '/classes/JRAPartyGroupMember.php';
include JR_ROOT_PATH . '/classes/JRAPartyIdentification.php';
include JR_ROOT_PATH . '/classes/JRAPayment.php';
include JR_ROOT_PATH . '/classes/JRAPartyVETFeeHelp.php';
include JR_ROOT_PATH . '/classes/JRAProspect.php';
include JR_ROOT_PATH . '/classes/JRAReference.php';

// Include the "form_processing" functions (to process the Gravity Forms into JobReady)
include JR_ROOT_PATH . '/includes/paypal_functions.php';
include JR_ROOT_PATH . '/includes/pdf_functions.php';
include JR_ROOT_PATH . '/includes/form_pre_processing.php';
include JR_ROOT_PATH . '/includes/form_validation.php';
include JR_ROOT_PATH . '/includes/form_post_processing.php';
include JR_ROOT_PATH . '/includes/gform_day_count.php';
include JR_ROOT_PATH . '/includes/employer_lookup_functions.php';
include JR_ROOT_PATH . '/includes/shortcodes.php';

// Dedicated application form scripts
include JR_ROOT_PATH . '/includes/accredited_holding_bay_application_form.php';
include JR_ROOT_PATH . '/includes/cpd_application_form.php';
include JR_ROOT_PATH . '/includes/ffs_accredited_application_form.php';
include JR_ROOT_PATH . '/includes/ffs_non_accredited_application_form.php';
include JR_ROOT_PATH . '/includes/jobready_registration_form.php';
include JR_ROOT_PATH . '/includes/nasc_registration_form.php';
include JR_ROOT_PATH . '/includes/nasc_enrolment_form.php';
include JR_ROOT_PATH . '/includes/pre_apprentice_application_form.php';
include JR_ROOT_PATH . '/includes/project_management_diploma_application_form.php';
include JR_ROOT_PATH . '/includes/uee30820_application_form.php';

// PDF Library
require_once(JR_ROOT_PATH.'/vendor/autoload.php');

// Remove OLD HTML2PDF vendor files
// require_once (JR_ROOT_PATH . '/vendor/fpdf/fpdf.php');
// require_once (JR_ROOT_PATH . '/vendor/html2pdf/html2pdf.class.php');

// Defines the "PAGE PARENT ID" based on a slug
define ( 'JOB_READY_COURSE_PAGE_PARENT_ID', get_page_by_path ( 'training-with-us' )->ID );

// Store data in post meta table if present in post data
global $jrc_fields;

$jrc_fields = array (
		'jrc_course_scope_code',
		'jrc_course_scope_name',
		'jrc_force_to_prospect',
		'jrc_duration',
		'jrc_mode_of_study',
		'jrc_description',
		'jrc_course_information',
		'jrc_selection_criteria',
		'jrc_pathways',
		'jrc_rpl',
		'jrc_course_structure',
		'jrc_fees',
		'jrc_how_to_apply',
		'jrc_register',
		'jrc_prerequisites',
		'jrc_licensing_exam',
		'jrc_footer',
		'jrc_featured',
		'jrc_apply_url' 
);

$jrd_fields = array (
		'jrd_course_id',
		'jrd_jr_id',
		'jrd_course_number',
		'jrd_course_name',
		'jrd_course_scope_code',
		'jrd_course_scope_name',
		'jrd_start_date',
		'jrd_end_date',
		'jrd_enrolment_start_date',
		'jrd_enrolment_end_date',
		'jrd_maximum_enrolments',
		'jrd_minimum_enrolments',
		'jrd_enrolment_count',
		'jrd_cost_student',
		'jrd_cost_neca_member' 
);

// Creates the Custom Post Type: Course
function create_job_ready_courses_cpt() {
	
	// Registers the Post Type
	register_post_type (	'job_ready_courses', 
							array (
								'labels' 		=> array (	'name' 			=> 'Courses',
															'singular_name' => 'Course',
															'add_new' 		=> 'Add New',
															'add_new_item' 	=> 'Add New Course',
															'edit' 			=> 'Edit',
															'edit_item' 	=> 'Edit Course',
															'new_item' 		=> 'New Course',
															'view' 			=> 'View',
															'view_item' 	=> 'View Course',
															'search_items' 	=> 'Search Courses',
															'not_found' 	=> 'No Couses found',
															'not_found_in_trash' => 'No Courses found in Trash',
															'parent'		=> 'Parent Course' ),
								'description'	=> 'Job Ready Course Scope',
								'public' 		=> true,
								'menu_position' => 10,
								'menu_icon' 	=> plugins_url ( 'images/course.png', __FILE__ ),
								'show_in_rest' 	=> true,
								'supports' 		=> array (	'title',
															'thumbnail' ),
								'taxonomies' 	=> array (	'' ),
								'has_archive' 	=> false,
								'rewrite' 		=> array (	'slug' 			=> 'course',
															'with_front'	=> false,
															'pages'			=> false ),
								'capability_type' => 'job_ready_course',
								'capabilities'	=> array(	'publish_posts'			=> 'publish_job_ready_courses',
															'edit_posts'			=> 'edit_job_ready_courses',
															'edit_others_posts'		=> 'edit_others_job_ready_courses',
															'read_private_posts'	=> 'read_private_job_ready_courses',
															'edit_post'				=> 'edit_job_ready_course',
															'delete_post'			=> 'delete_job_ready_course',
															'read_post'				=> 'read_job_ready_course' ),
								'map_meta_cap'	=> true
							)
						);
	
	// create_job_ready_course_dates_cpt
	init_job_ready_course_dates ();
	
	// Add custom "categories" taxonomy for "job_ready_courses"
	init_job_ready_courses_category_taxonomy ();
}

add_action ( 'init', 'create_job_ready_courses_cpt' );

// Create a Job Ready Course shortcode to output a single course based on course code passed in
add_shortcode ( 'job_ready_course', 'job_ready_course_shortcode' );
function job_ready_course_shortcode($atts) {
	$wp_query_atts = (array (
			'post_type' => 'job_ready_courses',
			'posts_per_page' => '1',
			'meta_query' => array (
					array (
							'key' => 'jrc_course_scope_code',
							'value' => $atts ['course'],
							'compare' => '=' 
					) 
			) 
	));
	
	global $post;
	
	$the_query = new WP_Query ( $wp_query_atts );
	// echo $the_query->request;
	
	if ($the_query->have_posts ()) {
		while ( $the_query->have_posts () ) {
			$the_query->the_post ();
			
			ob_start ();
			get_template_part ( 'partials/content', 'jobreadycourse' );
			$content = ob_get_clean ();
		}
		
		wp_reset_postdata ();
		
		return $content;
	}
}

// Creates the Meta Box Field for the Custom Post Type: Course
function job_ready_course_admin() {
	// Registers a metabox and associates it with the "job_ready_courses" custom post type
	add_meta_box ( 'job_ready_course_meta_box', 'Job Ready Course Details', 'display_job_ready_course_meta_box', 'job_ready_courses', 'normal', 'high' );
}

add_action ( 'admin_init', 'job_ready_course_admin' );

// This code renders the contents of the meta box
function display_job_ready_course_meta_box($post) {
	$job_ready_course = JobReadyCourseOperations::loadJobReadyCourse ( $post->ID );
	
	// - security -
	echo '<input type="hidden" name="jrc-nonce" id="jrc-nonce" value="' . wp_create_nonce ( 'jrc-nonce' ) . '" />';
	
	// Parent Page
	$mypages = get_pages ();
	
	if ($post->post_parent == 0) {
		$post->post_parent = JOB_READY_COURSE_PAGE_PARENT_ID;
	}
	
	$parent_dropdown = '<select name="post_parent">';
	foreach ( $mypages as $page ) {
		$parent_dropdown .= '<option value="' . $page->ID . '"';
		if ($page->ID == $post->post_parent) {
			$parent_dropdown .= ' selected';
		}
		$parent_dropdown .= '>' . $page->post_title . '</option>';
	}
	$parent_dropdown .= '</select>';
	
	?>

<table>
	<tr>
		<th style="width: 150px;">Parent Page:</th>
		<td><?php echo $parent_dropdown; ?></td>
	</tr>
	<tr>
		<th style="width: 150px;">Course Scope Code:</th>
		<td><?php echo $job_ready_course->course_scope_code; ?></td>
	</tr>
	<tr>
		<th>Course Scope Name:</th>
		<td><?php echo $job_ready_course->course_scope_name; ?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="checkbox" name="jrc_force_to_prospect" value="1"
			<?php if($job_ready_course->force_to_prospect) echo " checked"; ?> />
			Force to Prospect?</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="checkbox" name="jrc_featured" value="1"
			<?php if($job_ready_course->featured) echo " checked"; ?> /> Featured
			on home page?</td>
	</tr>
	<tr>
		<th>Duration:</th>
		<td><input type="text" size="80" name="jrc_duration"
			value="<?php echo $job_ready_course->duration; ?>" /></td>
	</tr>
	<tr>
		<th>Mode of Study</th>
		<td><input type="text" size="80" name="jrc_mode_of_study"
			value="<?php echo $job_ready_course->mode_of_study; ?>" /></td>
	</tr>
	<tr>
		<th>Application URL:</th>
		<td><input type="text" size="80" name="jrc_apply_url"
			value="<?php echo $job_ready_course->apply_url; ?>" /></td>
	</tr>

	<tr>
		<td colspan="2">
			<hr style="margin: 25px;" /> <strong>Description</strong><br />
			    <?php echo wp_editor( $job_ready_course->description, 'jrc_description', array( 'textarea_name' => 'jrc_description' )); ?>
			</td>
	</tr>
	<tr>
		<td colspan="2">
			<hr style="margin: 25px;" /> <strong>Register</strong><br />
			    <?php echo wp_editor( $job_ready_course->register, 'jrc_register', array( 'textarea_name' => 'jrc_register' )); ?>
			</td>
	</tr>
	<tr>
		<td colspan="2">
			<hr style="margin: 25px;" /> <strong>Course Information</strong><br />
			    <?php echo wp_editor( $job_ready_course->course_information, 'jrc_course_information', array( 'textarea_name' => 'jrc_course_information' )); ?>
			</td>
	</tr>
	<tr>
		<td colspan="2">
			<hr style="margin: 25px;" /> <strong>Prerequisites</strong><br />
			    <?php echo wp_editor( $job_ready_course->prerequisites, 'jrc_prerequisites', array( 'textarea_name' => 'jrc_prerequisites' )); ?>
			</td>
	</tr>
	<tr>
		<td colspan="2">
			<hr style="margin: 25px;" /> <strong>Selection Criteria</strong><br />
			    <?php echo wp_editor( $job_ready_course->selection_criteria, 'jrc_selection_criteria', array( 'textarea_name' => 'jrc_selection_criteria' )); ?>
			</td>
	</tr>
	<tr>
		<td colspan="2">
			<hr style="margin: 25px;" /> <strong>Pathways</strong>
			    <?php echo wp_editor( $job_ready_course->pathways, 'jrc_pathways', array( 'textarea_name' => 'jrc_pathways' )); ?>
			</td>
	</tr>
	<tr>
		<td colspan="2">
			<hr style="margin: 25px;" /> <strong>Licensing Exam</strong><br />
			    <?php echo wp_editor( $job_ready_course->licensing_exam, 'jrc_licensing_exam', array( 'textarea_name' => 'jrc_licensing_exam' )); ?>
			</td>
	</tr>
	<tr>
		<td colspan="2">
			<hr style="margin: 25px;" /> <strong>Recognition of Prior Learning
				(RPL)</strong><br />
			    <?php echo wp_editor( $job_ready_course->rpl, 'jrc_rpl', array( 'textarea_name' => 'jrc_rpl' )); ?>
			</td>
	</tr>
	<tr>
		<td colspan="2">
			<hr style="margin: 25px;" /> <strong>Course Structure</strong><br />
			    <?php echo wp_editor( $job_ready_course->course_structure, 'jrc_course_structure', array( 'textarea_name' => 'jrc_course_structure' )); ?>
			</td>
	</tr>
	<tr>
		<td colspan="2">
			<hr style="margin: 25px;" /> <strong>Fees</strong><br />
			    <?php echo wp_editor( $job_ready_course->fees, 'jrc_fees', array( 'textarea_name' => 'jrc_fees' )); ?>
			</td>
	</tr>
	<tr>
		<td colspan="2">
			<hr style="margin: 25px;" /> <strong>How to Apply</strong><br />
			    <?php echo wp_editor( $job_ready_course->how_to_apply, 'jrc_how_to_apply', array( 'textarea_name' => 'jrc_how_to_apply' )); ?>
			</td>
	</tr>
	<tr>
		<td colspan="2">
			<hr style="margin: 25px;" /> <strong>Footer Description</strong><br />
			    <?php echo wp_editor( $job_ready_course->footer, 'jrc_footer', array( 'textarea_name' => 'jrc_footer' )); ?>
			</td>
	</tr>
</table>
<?php
}

// Register a Save Post Function
// This function is executed when posts are saved or deleted from the admin panel
function add_job_ready_course_fields($post_id, $post_object) {
	global $jrc_fields;
	global $jrd_fields;
	
	// var_dump($post_id);
	// var_dump($post_object);
	
	if (defined ( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE)
		return;
	
	// Check post type for "job_ready_courses"
	if ($post_object->post_type == 'job_ready_courses') {
		
		if (! isset ( $_POST ['jrc-nonce'] ) || ! wp_verify_nonce ( $_POST ['jrc-nonce'], 'jrc-nonce' )) {
			return $post_id;
		}
		
		$parent_page_id = ( int ) $_POST ['post_parent'];
		
		// unhook this function so it doesn't loop infinitely
		remove_action ( 'save_post', 'add_job_ready_course_fields', 10, 2 );
		
		// JW: Tried to set the parent to force the side menu to load accordingly
		wp_update_post ( array (
				'ID' => $post_id,
				'post_parent' => $parent_page_id 
		) );
		
		// re-hook this function
		add_action ( 'save_post', 'add_job_ready_course_fields', 10, 2 );
		
		foreach ( $jrc_fields as $field )
		{
			if (isset ( $_POST [$field] ))
			{
				update_post_meta ( $post_id, $field, $_POST [$field] );
			}
			else
			{
				if ($field == 'jrc_force_to_prospect' || $field == 'jrc_featured')
				{
					update_post_meta ( $post_id, $field, 0 );
				}
			}
		}
	}
	
	// Check post type for "job_ready_dates"
	if ($post_object->post_type == 'job_ready_dates') {
		
		if (! isset ( $_POST ['jrd-nonce'] ) || ! wp_verify_nonce ( $_POST ['jrd-nonce'], 'jrd-nonce' )) {
			return $post_id;
		}
		
		foreach ( $jrd_fields as $field ) {
			if (isset ( $_POST [$field] ) && $_POST [$field] != '') {
				update_post_meta ( $post_id, $field, $_POST [$field] );
			}
		}
	}
}

// This function is called when posts get saved in the database
add_action ( 'save_post', 'add_job_ready_course_fields', 10, 2 );

// Create a custom template dedicated to "job_ready_courses" custom post type (i.e. Courses)
// Search for a template like "single-job_ready_courses.php in the current theme directory.
// If not found, it looks in the plugin directory for the template.
function job_ready_course_include_template($template_path) {
	if (get_post_type () == 'job_ready_courses') {
		if (is_single ()) {
			// checks if the file exists in the theme first,
			// otherwise serve the file from the plugin
			
			if ($theme_file = locate_template ( array (
					'single-job_ready_courses.php' 
			) )) {
				$template_path = $theme_file;
			} else {
				$template_path = plugin_dir_path ( __FILE__ ) . '/templates/single-job_ready_courses.php';
			}
		} elseif (is_archive ()) {
			if ($theme_file = locate_template ( array (
					'archive-job_ready_courses.php' 
			) )) {
				$template_path = $theme_file;
			} else {
				$template_path = plugin_dir_path ( __FILE__ ) . '/templates/archive-job_ready_courses.php';
			}
		}
	}
	return $template_path;
}

// Register a function to force the dedicated template
add_filter ( 'template_include', 'job_ready_course_include_template', 1 );

// Register the API field using the above generic callbacks
function job_ready_course_register_api_fields() {
	global $jrc_fields; // Grabs the JRC Fields as required
	
	foreach ( $jrc_fields as $field ) {
		register_rest_field ( 'job_ready_courses', $field, array (
				'get_callback' => 'slug_get_post_meta_cb',
				'update_callback' => 'slug_update_post_meta_cb',
				'schema' => null 
		) );
	}
}

add_action ( 'rest_api_init', 'job_ready_course_register_api_fields' );

// Add additional columns to the ADMIN > COURSES listing screen
function job_ready_courses_columns($columns) {
	// Remove the "comments" column
	unset ( $columns ['comments'] );
	
	// Setup the new columns
	$new_columns = array (
			'jrc_course_scope_code' => 'Course Scope Scope',
			'jrc_course_scope_name' => 'Course Scope Name' 
	);
	
	return array_merge ( $columns, $new_columns );
	
	return $columns;
}

add_filter ( 'manage_job_ready_courses_posts_columns', 'job_ready_courses_columns' );

// Populate the additional admin columns
function custom_columns($column, $post_id) {
	switch ($column) {
		case 'jrc_course_scope_code' :
			$course_scope_code = esc_html ( get_post_meta ( $post_id, 'jrc_course_scope_code', true ) );
			echo $course_scope_code;
			break;
		case 'jrc_course_scope_name' :
			$course_scope_name = esc_html ( get_post_meta ( $post_id, 'jrc_course_scope_name', true ) );
			echo $course_scope_name;
			break;
	}
}

add_action ( 'manage_posts_custom_column', 'custom_columns', 10, 2 );

// Make the new columns sortable
function job_ready_course_sort_me($columns) {
	$columns ['jrc_course_scope_code'] = 'jrc_course_scope_code';
	$columns ['jrc_course_scope_name'] = 'jrc_course_scope_name';
	
	return $columns;
}

add_filter ( 'manage_edit-job_ready_courses_sortable_columns', 'job_ready_course_sort_me' );

// Applies the "column orderby" logic for the new sortable columns
// Sorting mechanism doesn't actually work without this code
function column_orderby($vars) {
	if (! is_admin ())
		return $vars;
	if (isset ( $vars ['orderby'] ) && 'jrc_course_scope_code' == $vars ['orderby']) {
		$vars = array_merge ( $vars, array (
				'meta_key' => 'jrc_course_scope_code',
				'orderby' => 'meta_value' 
		) );
	} elseif (isset ( $vars ['orderby'] ) && 'jrc_course_scope_name' == $vars ['orderby']) {
		$vars = array_merge ( $vars, array (
				'meta_key' => 'jrc_course_scope_name',
				'orderby' => 'meta_value' 
		) );
	}
	return $vars;
}

add_filter ( 'request', 'column_orderby' );

/*
 * JOB READY EVENTS STARTS HERE
 */

// Register Custom Post Type
function init_job_ready_course_dates()
{
	register_post_type ('job_ready_dates',
						array (	'labels' => array (	'name' 					=> 'Course Dates',
													'singular_name' 		=> 'Course Date',
													'add_new' 				=> 'Add New',
													'add_new_item'			=> 'Add New Course Date',
													'edit' 					=> 'Edit',
													'edit_item' 			=> 'Edit Course Date',
													'new_item' 				=> 'New Course Date',
													'view' 					=> 'View',
													'view_item' 			=> 'View Course Date',
													'search_items'			=> 'Search Course Dates',
													'not_found'				=> 'No Course Dates found',
													'not_found_in_trash'	=> 'No Course Dates found in Trash',
													'parent' => '' ),
								'description'			=> 'Job Ready Date Scope',
								'public' 				=> true,
								'exclude_from_search'	=> true, 
								'menu_position'			=> 10,
								'menu_icon'				=> 'dashicons-book',
								'show_in_rest' 			=> true,
								'show_in_menu' 			=> 'edit.php?post_type=job_ready_courses',
								'supports' 				=> array (	'title',
																	'thumbnail' ),
								'taxonomies' 			=> array (	'' ),
								'has_archive' 			=> false,
								'rewrite' 				=> array (	'slug' => 'course_date',
																	'with_front' => false,
																	'pages' => false ),
								'capability_type' 		=> 'job_ready_date',
								'capabilities'			=> array(	'publish_posts'			=> 'publish_job_ready_dates',
																	'edit_posts'			=> 'edit_job_ready_dates',
																	'edit_others_posts'		=> 'edit_others_job_ready_dates',
																	'read_private_posts'	=> 'read_private_job_ready_dates',
																	'edit_post'				=> 'edit_job_ready_date',
																	'delete_post'			=> 'delete_job_ready_date',
																	'read_post'				=> 'read_job_ready_date' ),
								'map_meta_cap' 			=> true
						)
	);
}

// Creates the Meta Box Field for the Custom Post Type: "job_ready_dates"
function job_ready_dates_admin() {
	// Registers a metabox and associates it with the "job_ready_dates" custom post type
	add_meta_box ( 'job_ready_dates_meta_box', 'Job Ready Dates Details', 'display_job_ready_dates_meta_box', 'job_ready_dates', 'normal', 'high' );
}

add_action ( 'admin_init', 'job_ready_dates_admin' );

// This code renders the contents of the meta box
function display_job_ready_dates_meta_box($post) {
	// Set up a parent relationship between job_ready_dates and job_ready_courses
	$parents = get_posts ( array (
			'post_type' => 'job_ready_courses',
			'orderby' => 'title',
			'order' => 'ASC',
			'numberposts' => - 1 
	) );
	
	$job_ready_date = JobReadyDateOperations::loadJobReadyDate ( $post->ID );
	
	// - security -
	echo '<input type="hidden" name="jrd-nonce" id="jrd-nonce" value="' . wp_create_nonce ( 'jrd-nonce' ) . '" />';
	
	?>

<div class="jrd-meta">
	<table>
		<tr>
			<th style="width: 150px">Course:</th>
			<td><select name="jrd_course_id" class="widefat">
					<option value="">No Related Course</option>
					<?php
	foreach ( $parents as $parent ) {
		printf ( '<option value="%s"%s>%s</option>', esc_attr ( $parent->ID ), selected ( $parent->ID, $job_ready_date->course_id, false ), esc_html ( $parent->post_title ) );
	}
	?>
				</select></td>
		</tr>
		<tr>
			<th>Job Ready (ID):</th>
			<td><?php echo $job_ready_date->jr_id; ?></td>
		</tr>
		<tr>
			<th>Course Number:</th>
			<td><?php echo $job_ready_date->course_number; ?></td>
		</tr>
		<tr>
			<th>Course Name:</th>
			<td><?php echo $job_ready_date->course_name; ?></td>
		</tr>
		<tr>
			<th>Course Scope Code:</th>
			<td><?php echo $job_ready_date->course_scope_code; ?></td>
		</tr>
		<tr>
			<th>Course Scope Name:</th>
			<td><?php echo $job_ready_date->course_scope_name; ?></td>
		</tr>
		<tr>
			<td colspan="2"><hr style="margin-top: 25;" /></td>
		</tr>
		<tr>
			<th>Course Date:</th>
			<td><?php echo $job_ready_date->start_date_clean . ' to ' . $job_ready_date->end_date_clean; ?></td>
		</tr>
		<tr>
			<th>Enrolment Date:</th>
			<td>
            	<?php
	if ($job_ready_date->enrolment_end_date_clean != '' && $job_ready_date->enrolment_start_date_clean != '') {
		echo $job_ready_date->enrolment_start_date_clean . ' to ' . $job_ready_date->enrolment_end_date_clean;
	}
	?>
            </td>
		</tr>
		<tr>
			<td colspan="2"><hr style="margin-top: 25;" /></td>
		</tr>
		<tr>
			<th>Maximum Enrolments:</th>
			<td><?php echo $job_ready_date->maximum_enrolments; ?></td>
		</tr>
		<tr>
			<th>Minimum Enrolments:</th>
			<td><?php echo $job_ready_date->minimum_enrolments; ?></td>
		</tr>
		<tr>
			<th>Enrolment Count:</th>
			<td><?php echo $job_ready_date->enrolment_count; ?></td>
		</tr>
	</table>

</div>

<?php
}

// Register the API field using the above generic callbacks
function job_ready_dates_register_api_fields() {
	// Grabs the JRD Fields as required
	global $jrd_fields;
	
	// Loops through each field and registers it with the API
	foreach ( $jrd_fields as $field ) {
		register_rest_field ( 'job_ready_dates', $field, array (
				'get_callback' => 'slug_get_post_meta_cb',
				'update_callback' => 'slug_update_post_meta_cb',
				'schema' => null 
		) );
	}
}

add_action ( 'rest_api_init', 'job_ready_dates_register_api_fields' );

// Create a custom template dedicated to Custom Post Type: Course
// Search for a template like "single-job_ready_courses.php in the current theme directory.
// If not found, it looks in the plugin directory for the template.
function job_ready_date_include_template($template_path) {
	if (get_post_type () == 'job_ready_dates') {
		
		if (is_single ()) {
			// checks if the file exists in the theme first,
			// otherwise serve the file from the plugin
			
			if ($theme_file = locate_template ( array (
					'single-job_ready_dates.php' 
			) )) {
				$template_path = $theme_file;
			} else {
				$template_path = plugin_dir_path ( __FILE__ ) . '/templates/single-job_ready_dates.php';
			}
		} elseif (is_archive ()) {
			if ($theme_file = locate_template ( array (
					'archive-job_ready_dates.php' 
			) )) {
				$template_path = $theme_file;
			} else {
				$template_path = plugin_dir_path ( __FILE__ ) . '/templates/archive-job_ready_dates.php';
			}
		}
	}
	return $template_path;
}

// Register a function to force the dedicated template
add_filter ( 'template_include', 'job_ready_date_include_template', 1 );

// Add additional columns to the ADMIN > COURSES listing screen
function job_ready_dates_columns($columns) {
	// Remove the "comments" column
	unset ( $columns ['comments'] );
	
	// Setup the new columns
	$new_columns = array (
			'jrd_course' => 'Course Scope',
			'jrd_course_number' => 'Course',
			'jrd_course_dates' => 'Course Dates',
			'jrd_enrolment_dates' => 'Enrolment Dates',
			'jrd_enrolments' => 'Enrolments' 
	);
	
	return array_merge ( $columns, $new_columns );
	
	return $columns;
}

add_filter ( 'manage_job_ready_dates_posts_columns', 'job_ready_dates_columns' );

// Populate the columns
function job_ready_dates_custom_columns($column, $post_id) {
	switch ($column) {
		case 'jrd_course' :
			$course_id = esc_html ( get_post_meta ( $post_id, 'jrd_course_id', true ) );
			$course_scope_code = esc_html ( get_post_meta ( $course_id, 'jrc_course_scope_code', true ) );
			$course_scope_name = esc_html ( get_post_meta ( $course_id, 'jrc_course_scope_name', true ) );
			echo $course_scope_code . ':<br/>' . $course_scope_name;
			break;
		case 'jrd_course_number' :
			$course_number = esc_html ( get_post_meta ( $post_id, 'jrd_course_number', true ) );
			$course_name = esc_html ( get_post_meta ( $post_id, 'jrd_course_name', true ) );
			echo $course_number;
			if ($course_name != '') {
				echo "<br/>$course_name";
			}
			break;
		case 'jrd_course_dates' :
			$start_date = esc_html ( get_post_meta ( $post_id, 'jrd_start_date', true ) );
			$end_date = esc_html ( get_post_meta ( $post_id, 'jrd_end_date', true ) );
			echo date ( 'd/m/Y', strtotime ( $start_date ) ) . '<br/>to<br/>' . date ( 'd/m/Y', strtotime ( $end_date ) );
			break;
		case 'jrd_enrolment_dates' :
			$enrolment_start_date = esc_html ( get_post_meta ( $post_id, 'jrd_enrolment_start_date', true ) );
			$enrolment_end_date = esc_html ( get_post_meta ( $post_id, 'jrd_enrolment_end_date', true ) );
			if ($enrolment_start_date != '' && $enrolment_end_date != '') {
				echo date ( 'd/m/Y', strtotime ( $enrolment_start_date ) ) . '<br/>to<br/>' . date ( 'd/m/Y', strtotime ( $enrolment_end_date ) );
			}
			break;
		case 'jrd_enrolments' :
			$max_enrolments = esc_html ( get_post_meta ( $post_id, 'jrd_maximum_enrolments', true ) );
			$min_enrolments = esc_html ( get_post_meta ( $post_id, 'jrd_minimum_enrolments', true ) );
			$enrolment_count = esc_html ( get_post_meta ( $post_id, 'jrd_enrolment_count', true ) );
			echo "Max: $max_enrolments <br/> Min: $min_enrolments <br/> Enrolled: $enrolment_count";
			break;
	}
}

add_action ( 'manage_posts_custom_column', 'job_ready_dates_custom_columns', 10, 2 );

// Make the new columns sortable
function job_ready_dates_sort_me($columns) {
	$columns ['jrd_course_number'] = 'jrd_course_number';
	
	return $columns;
}

add_filter ( 'manage_edit-job_ready_dates_sortable_columns', 'job_ready_dates_sort_me' );

// Applies the "column orderby" logic for the new sortable columns
// TODO: Sorting mechanism doesn't actually work without this code
function job_ready_dates_column_orderby($vars) {
	if (! is_admin ())
		return $vars;
	if (isset ( $vars ['orderby'] ) && 'jrd_course_number' == $vars ['orderby']) {
		$vars = array_merge ( $vars, array (
				'meta_key' => 'jrd_course_number',
				'orderby' => 'meta_value' 
		) );
	}
	return $vars;
}

add_filter ( 'request', 'job_ready_dates_column_orderby' );

// Create a filter for the job_ready_dates_course
// Registers the function to be called when WordPress is preparing to display the filter drop down list
function job_ready_dates_course_filter_list() {
	$screen = get_current_screen ();
	$jrd_course_id = isset ( $_GET ['filter_jrd_course_id'] ) ? ( int ) $_GET ['filter_jrd_course_id'] : 0;
	
	global $wpdb;
	
	if ($screen->post_type == 'job_ready_dates') {
		// Query the database for all "job_ready_courses" and retrieve ID and "jrc_course_scope_code" meta_value
		$querystr = " SELECT DISTINCT $wpdb->posts.ID, $wpdb->postmeta.meta_value
						FROM $wpdb->posts, $wpdb->postmeta
					   WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id
						 AND $wpdb->posts.post_status = 'publish'
						 AND $wpdb->posts.post_type = 'job_ready_courses'
						 AND $wpdb->postmeta.meta_key = 'jrc_course_scope_code'
					ORDER BY $wpdb->postmeta.meta_value ASC ";
		
		$courses = $wpdb->get_results ( $querystr, OBJECT );
		
		// Ensure we have results
		if (empty ( $courses ))
			return;
		
		$selected = $jrd_course_id == 0 ? ' selected' : '';
		
		// Set up the Options
		$options [] = '<option value="0"' . $selected . '>All Courses</option>';
		
		foreach ( $courses as $course ) {
			$selected = $jrd_course_id == $course->ID ? ' selected' : '';
			$options [] = sprintf ( '<option value="%1$s"' . $selected . '>%2$s</option>', ( int ) $course->ID, $course->meta_value );
		}
		
		// Output the dropdown menu
		echo '<select class="" id="filter_jrd_course_id" name="filter_jrd_course_id">';
		echo join ( "\n", $options );
		echo '</select>';
	}
}

add_action ( 'restrict_manage_posts', 'job_ready_dates_course_filter_list' );

// Display Filtered Results
// Register a function that is called when the post display query is prepared
function job_ready_dates_perform_filtering($query) {
	global $pagenow;
	$current_page = isset ( $_GET ['post_type'] ) ? $_GET ['post_type'] : '';
	$jrd_course_id = isset ( $_GET ['filter_jrd_course_id'] ) ? ( int ) $_GET ['filter_jrd_course_id'] : 0;
	
	if (is_admin () && $current_page == 'job_ready_dates' && $pagenow == 'edit.php' && $jrd_course_id > 0) {
		$query->query_vars ['meta_key'] = 'jrd_course_id';
		$query->query_vars ['meta_value'] = $jrd_course_id;
		$query->query_vars ['meta_compare'] = '=';
	}
}

add_filter ( 'parse_query', 'job_ready_dates_perform_filtering' );

/*
 * Course Category Taxonomy
 */

// Init job ready courses category taxonomy
function init_job_ready_courses_category_taxonomy() {
	register_taxonomy ( 'job_ready_courses_category', 'job_ready_courses', array (
			'labels' => array (
					'name' => 'Course Category',
					'add_new_item' => 'Add New Course Category',
					'new_item_name' => 'New Course Category' 
			),
			'show_ui' => true,
			'show_tagcloud' => false,
			'hierarchical' => true 
	) );
}

// Create a filter for the job_ready_courses_category
// Registers the function to be called when WordPress is preparing to display the filter drop down list
function job_ready_courses_category_filter_list() {
	$screen = get_current_screen ();
	
	global $wp_query;
	
	if ($screen->post_type == 'job_ready_courses') {
		// Loads the categories which have been allocated to a Course thus far
		wp_dropdown_categories ( array (
				'show_option_all' => 'Show All Categories',
				'taxonomy' => 'job_ready_courses_category',
				'name' => 'job_ready_courses_category',
				'orderby' => 'name',
				'selected' => (isset ( $wp_query->query ['job_ready_courses_category'] ) ? $wp_query->query ['job_ready_courses_category'] : ''),
				'hierarchical' => false,
				'depth' => 3,
				'show_count' => false,
				'hide_empty' => true 
		) );
	}
}

add_action ( 'restrict_manage_posts', 'job_ready_courses_category_filter_list' );

// Display Filtered Results
// Register a function that is called when the post display query is prepared
function perform_filtering($query) {
	$qv = &$query->query_vars;
	if (isset ( $qv ['job_ready_courses_category'] )) {
		if (($qv ['job_ready_courses_category']) && is_numeric ( $qv ['job_ready_courses_category'] )) {
			$term = get_term_by ( 'id', $qv ['job_ready_courses_category'], 'job_ready_courses_category' );
			$qv ['job_ready_courses_category'] = $term->slug;
		}
	}
}

add_filter ( 'parse_query', 'perform_filtering' );

/*
 * LOGIN / LOGOUT FORMS
 */

// Setup job ready login and handler to process
add_action ( 'init', 'job_ready_login_form_process' );
add_shortcode ( 'job_ready_login_form', 'job_ready_login_form' );
function job_ready_login_form_process() {
	if (! empty ( $_POST ['nonce_login_form'] )) {
		if (! wp_verify_nonce ( $_POST ['nonce_login_form'], 'handle_login_form' )) {
			die ( 'You are not authorized to perform this action.' );
		} else {
			$error = null;
			if (empty ( $_POST ['login'] )) {
				$_REQUEST ['login_error'] = 'Please enter login';
			} elseif (empty ( $_POST ['password'] )) {
				$_REQUEST ['login_error'] = 'Please enter password';
			} else {
				$user = new stdClass ();
				$user->login = $_POST ['login'];
				$user->password = $_POST ['password'];
				$party_xml_object = JRAPartyOperations::loginJRAParty ( $user );
				
				if ($party_xml_object !== false) {
					// Setup the party id and party login (used for pre-populating employer sections on forms)
					$_SESSION ['party_id'] = ( int ) $party_xml_object->{'party'}->{'id'};
					$_SESSION ['party_identifier'] = ( string ) $party_xml_object->{'party'}->{'party-identifier'};
					$_SESSION ['party_type'] = ( string ) $party_xml_object->{'party'}->{'party-type'};
					$_SESSION ['party_login'] = $_POST ['login'];
					
					// Setup the student id and student login (used for pre-populating student information on forms)
					// If the party is a "employer" set student_id to '' + student_login to ''
					$_SESSION ['employee_party_id'] = ($_SESSION ['party_type'] == 'employer') ? '' : $_SESSION ['party_id'];
					$_SESSION ['employee_party_login'] = ($_SESSION ['party_type'] == 'employer') ? '' : $_SESSION ['party_login'];
				} else {
					$_REQUEST ['login_error'] = 'Incorrect login / password combination';
				}
			}
		}
	}
	
	if (! empty ( $_POST ['nonce_logout_form'] )) {
		if (! wp_verify_nonce ( $_POST ['nonce_logout_form'], 'handle_logout_form' )) {
			die ( 'You are not authorized to perform this action.' );
		} else {
			// Unset the Login Details
			unset ( $_SESSION ['party_id'] );
			unset ( $_SESSION ['party_login'] );
			unset ( $_SESSION ['party_type'] );
			
			// Unset the Student Details
			unset ( $_SESSION ['employee_party_id'] );
			unset ( $_SESSION ['employee_party_login'] );
		}
	}
	
	if (! empty ( $_POST ['nonce_prepopulate_form'] )) {
		if (! wp_verify_nonce ( $_POST ['nonce_prepopulate_form'], 'handle_prepopulate_form' )) {
			die ( 'You are not authorized to perform this action.' );
		} else {
			// Employee variable consists of "employee_party_id|employee_party_login"
			// Explode this variable into an array and set the SESSION variables for student
			$employee = explode ( '|', $_POST ['employee'] );
			
			// Set the student details
			$_SESSION ['employee_party_id'] = $employee [0]; // employee_party_id
			$_SESSION ['employee_party_login'] = $employee [1]; // employee_party_login
		}
	}
}
function job_ready_login_form() {
    $content = '';
	if (isset ( $_SESSION ['party_id'] )) {
		$content .= "You are currently logged in as <strong>" . $_SESSION ['party_login'] . "</strong> and we've pre-populated your form with these details. If this is not correct, please logout.<br/>
					<form method='post' action=''>" . wp_nonce_field ( 'handle_logout_form', 'nonce_logout_form' ) . "
						<input type='submit' value='Logout' />
					</form>";
		
		// Display pre-populate drop down for Employers only
		if (isset ( $_SESSION ['party_type'] ) && $_SESSION ['party_type'] == 'Employer') {
			// Setup employees array for drop down list
			$employer_id = $_SESSION ['party_identifier'];
			
			$employees = JRAPartyOperations::loadEmployeesByEmployerID ( $employer_id );
			
			$content .= "As an employer you can pre-populate the form with previously student information related to your organisation.<br/>
						Please choose a student and select pre-populate below:<br/>
						<form method='post' action=''>" . wp_nonce_field ( 'handle_prepopulate_form', 'nonce_prepopulate_form' ) . "
							<select name='employee'>";
			
			foreach ( $employees as $employee ) {
				$selected = (isset ( $_SESSION ['employee_party_id'] ) && $_SESSION ['employee_party_id'] == $employee ['party_id']) ? ' selected' : '';
				$content .= "<option value='" . $employee ['party_id'] . "|" . $employee ['party_login'] . "'$selected>" . $employee ['firstname'] . " " . $employee ['surname'] . "</option>";
			}
			
			$content .= "	</select>
							<input type='submit' value='Pre-populate Form' />
						</form>";
		}
	} else {
		if (isset ( $_REQUEST ['login_error'] )) {
			$content .= "<div class='error'>" . $_REQUEST ['login_error'] . "</div>";
		}
		else
		{
			$content = '<p>If you are an existing or previous student, please log in to book your course.</p>
						<p>If it’s your first time here, please complete and submit the form below and we will be in touch.</p>
						<p>If you have forgotten your login details, please contact Student Services on <a href="tel:0393811922">(03) 9381 1922</a></p>';
			//$content = 'If you are an exisiting or past student or a current NECA member and know your login details, please log in to book your course. If you can’t remember your login details or it’s your first time here please fill in the form below. If you want to reset your login details please contact <a href="mailto:studentservices@necaeducation.com.au?subject=Reset%20My%20Password&body=Hi%20Student%20Services%0ACan%20you%20please%20change%20my%20password.%0AMy%20details%20are%3A%0AName%3A%0ALogin%3A%0AMobile%3A" target="_blank">Student Service</a>. ';
		}
		
		$content .= "<form method='post' action=''>
						<input name='login' type='text' placeholder='Login' /><br/>
						<input name='password' type='password' placeholder='Password' /><br/>" . wp_nonce_field ( 'handle_login_form', 'nonce_login_form' ) . "
	    				<input type='submit' value='Submit'/>
					</form>";
	}
	return $content;
}

// Add action to display job_ready_login_form on the Single Product Template before the content is published
add_action ( 'woocommerce_single_product_summary', 'woocommerce_job_ready_login_injection', 6 );
function woocommerce_job_ready_login_injection() {
    global $product;
    $product_id = $product->get_id();
    
    if($product_id != PRE_APPRENTICE_PRODUCT_ID && $product_id != NASC_PRODUCT_ID) // Exclude Pre-Apprentice Course and Non-Accredited Course Registration
    {
        $job_ready_login_form = job_ready_login_form ();
        echo $job_ready_login_form;
    }
}

// Add Action before cart table
add_action ( 'woocommerce_before_cart_table', 'woocommerce_cart_show_logout' );
function woocommerce_cart_show_logout() {
	if (isset ( $_SESSION ['party_id'] )) {
		$content = "You are currently logged in as <strong>" . $_SESSION ['party_login'] . "</strong> and we've pre-populated your form with these details. If this is not correct, please logout.<br/>
					<form method='post' action=''>" . wp_nonce_field ( 'handle_logout_form', 'nonce_logout_form' ) . "
						<input type='submit' value='Logout' />
					</form>";
		echo $content;
	}
}

add_shortcode ( 'job_ready_logout', 'show_jobready_logout' );
function show_jobready_logout() {
	if (isset ( $_SESSION ['party_id'] )) {
		$content = "Logged in as <strong>" . $_SESSION ['party_login'] . "</strong>
					<a href='" . site_url () . "/jobready-logout.php'>Logout</a>";
		echo $content;
	}
}
function jobready_logout() {
	// Unset the Login Details
	unset ( $_SESSION ['party_id'] );
	unset ( $_SESSION ['party_login'] );
	unset ( $_SESSION ['party_type'] );
	
	// Unset the Student Details
	unset ( $_SESSION ['employee_party_id'] );
	unset ( $_SESSION ['employee_party_login'] );
}


/*
 * SYNC RELATED
 */


// CRON Related
// Disable the WP_CRON from the wp-config.php file

// Setup your event code using wp_schedule_event
add_action( 'neca_daily_event',  'neca_job_ready_daily_sync' );

// Deactivate schedule when plugin is deactivated
register_deactivation_hook( __FILE__, 'jr_sync_deactivate' );

// Activate scheduled sync
if ( ! wp_next_scheduled( 'neca_daily_event' ) )
{
	wp_schedule_event( time(), 'daily', 'neca_daily_event' );
}

// Deactivate scheduled sync
function jr_sync_deactivate()
{
	$timestamp = wp_next_scheduled( 'neca_daily_event' );
	wp_unschedule_event( $timestamp, 'neca_daily_event' );
}

// Perform the actual sync
function neca_job_ready_daily_sync()
{
	// Remove expired job_ready_courses
	JobReadyDateOperations::removeExpiredJobReadyDates();
	
	// Perform the SYNC process to update the job_ready_courses
	job_ready_sync();
	
	// Perform the Employer Sync process to update the job_ready_employers
	JRAEmployerOperations::syncEmployers();
	
	// Send email notification based on success or fail
	$to = 'james@smoothdevelopments.com.au';
	$subject = 'CRON Daily Script successfully completed';
	$message = 'CRON Daily Script successfully completed on NECA Education website';
	wp_mail( $to, $subject, $message);
}


// Add Sync Now menu option under Courses admin menu
function add_job_ready_sync_submenu() {
	$parent_slug = 'edit.php?post_type=job_ready_courses';
	$page_title = __ ( 'Job Ready Sync' );
	$menu_title = __ ( 'Sync Now' );
	$capability = 'publish_job_ready_dates';
	$menu_slug = 'job_ready_sync_submenu';
	$submenu_function = 'job_ready_sync';
	
	add_submenu_page ( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $submenu_function );

    // Add Employer Sync
    $parent_slug = 'edit.php?post_type=job_ready_courses';
    $page_title = __ ( 'Job Ready Employer Sync' );
    $menu_title = __ ( 'Sync Employers Now' );
    $capability = 'publish_job_ready_dates';
    $menu_slug = 'job_ready_employer_sync_submenu';
    $submenu_function = 'job_ready_employers_sync';
    
    add_submenu_page ( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $submenu_function );
}

add_action ( 'admin_menu', 'add_job_ready_sync_submenu' );


function job_ready_employers_sync()
{
    // Perform the Employer Sync process to update the job_ready_employers
    JRAEmployerOperations::syncEmployers();
    
    echo "Employers Sync Completed<br/>";
}

/*
 * GENERIC FUNCTIONS
 */

// Flushing Rewrite on Activation
// To get permalinks to work when you activate the plugin.
function my_rewrite_flush() {
	create_job_ready_courses_cpt ();
	flush_rewrite_rules ();
}

register_activation_hook ( __FILE__, 'my_rewrite_flush' );

// Check if this is a job ready course
function is_job_ready_course() {
	global $post;
	$post_type = get_post_type ( $post );
	
	if ($post_type == 'job_ready_courses')
		return true;
	else
		return false;
}

// Defines some generic callback functions which will be used for retrieving and updating post meta data via the WP REST API
function slug_get_post_meta_cb($object, $field_name, $request) {
	return get_post_meta ( $object ['id'], $field_name );
}
function slug_update_post_meta_cb($value, $object, $field_name) {
	return update_post_meta ( $object->ID, $field_name, $value );
}

// https://github.com/luisfredgs/rest-filter/blob/master/plugin.php
// Plugin Name: WP REST API filter parameter
// Description: This plugin adds a "filter" query parameter to API post collections to filter returned results based on public WP_Query parameters, adding back the "filter" parameter that was removed from the API when it was merged into WordPress core.
// Author: WP REST API Team
// Author URI: http://v2.wp-api.org
// Version 0.1
// License: GPL2+

add_action ( 'rest_api_init', 'rest_api_filter_add_filters' );

/**
 * Add the necessary filter to each post type
 */
function rest_api_filter_add_filters() {
	foreach ( get_post_types ( array (
			'show_in_rest' => true 
	), 'objects' ) as $post_type ) {
		add_filter ( 'rest_' . $post_type->name . '_query', 'rest_api_filter_add_filter_param', 10, 2 );
	}
}

/**
 * Add the filter parameter
 *
 * @param array $args
 *        	The query arguments.
 * @param WP_REST_Request $request
 *        	Full details about the request.
 * @return array $args.
 *        
 */
function rest_api_filter_add_filter_param($args, $request) {
	// Bail out if no filter parameter is set.
	if (empty ( $request ['filter'] ) || ! is_array ( $request ['filter'] )) {
		return $args;
	}
	$filter = $request ['filter'];
	if (isset ( $filter ['posts_per_page'] ) && (( int ) $filter ['posts_per_page'] >= 1 && ( int ) $filter ['posts_per_page'] <= 100)) {
		$args ['post_per_page'] = $filter ['posts_per_page'];
	}
	global $wp;
	$vars = apply_filters ( 'query_vars', $wp->public_query_vars );
	foreach ( $vars as $var ) {
		if (isset ( $filter [$var] )) {
			$args [$var] = $filter [$var];
		}
	}
	return $args;
}

// Setup a sidebar for the custom taxonomy - Job Ready Course
add_filter ( 'generate_sidebar_layout', 'cpt_horses_sidebar_layout' );
function cpt_horses_sidebar_layout($layout) {
	// If we are on the job_ready_courses single page, set the sidebar
	if (is_singular ( 'job_ready_courses' ))
		return 'ups-sidebar-training-with-us';
	
	// Or else, set the regular layout
	return $layout;
}


// Adds additional coupon validation logic to the validation process.
// Checks if the course_scope_code for the given form, is in the invalid list
add_filter( 'woocommerce_coupon_is_valid_for_product', 'neca_coupon_is_valid_for_course_scope_code', 10, 4 );

function neca_coupon_is_valid_for_course_scope_code( $valid, $product, $instance, $values )
{
	if(isset($values['_gravity_form_lead']))
	{
		$gfl = $values['_gravity_form_lead'];
		$form_id = $gfl['form_id'];
		$course_scope_code = '';
		$invalid_course_scope_codes = array('UEE22011', 'UEE30811', 'LET', 'LEP', 'SWP');
		
		if($form_id == SHORT_COURSE_APPLICATION_FORM_NON_ACCREDITED)
		{
			$course_scope_code = $gfl['22'];
		}
		
		if($form_id == SHORT_COURSE_APPLICATION_FORM_ACCREDITED)
		{
			$course_scope_code = $gfl['69'];
		}
		
		if(in_array($course_scope_code, $invalid_course_scope_codes))
		{
			$valid = false;
		}
		else
		{
			$valid = true;
		}
	}
	
	return $valid;
}
