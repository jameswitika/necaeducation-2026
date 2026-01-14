<?php
/*
 * JobReadyCourseOperations class
 * Created by: James Witika
 * Company: Smooth Developments
 */

if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class JobReadyCourse {
	var $course_scope_code;
	var $course_scope_name;
	var $force_to_prospect;
	var $duration;
	var $mode_of_study;
	var $description;
	var $course_information;
	var $selection_criteria;
	var $pathways;
	var $rpl;
	var $course_structure;
	var $fees;
	var $how_to_apply;
	var $apply_url;
	var $register;
	var $prerequisites;
	var $licensing_exam;
	var $cost;
	var $footer;
	var $featured;
	
	function __construct()
	{
		$this->course_scope_code = '';
		$this->course_scope_name = '';
		$this->force_to_prospect = 0;
		$this->duration = '';
		$this->mode_of_study = '';
		$this->description = '';
		$this->course_information = '';
		$this->selection_criteria = '';
		$this->pathways = '';
		$this->rpl = '';
		$this->course_structure = '';
		$this->fees = '';
		$this->how_to_apply = '';
		$this->apply_url = home_url( '/product/short-course-application-form-non-accredited/', is_ssl() ? 'https' : 'http' );
		$this->register = '';
		$this->prerequisites = '';
		$this->licensing_exam = '';
		$this->cost = '0';
		$this->footer = '';
		$this->featured = false;
	}
}

class JobReadyCourseOperations {

	// __constructor function
	function __construct()
	{
	}
	
	
	static function getJobReadyCourseName($post)
	{
		$course_scope_name = esc_html( get_post_meta( $post->ID, 'jrc_course_scope_name', true ) );
		return $course_scope_name;
	}

	
	static function getJobReadyCourseFieldByCourseScopeCode ($course_scope_code, $fieldname)
	{
		global $wpdb;
		
		// Query the database for all "job_ready_courses" and retrieve ID and "jrc_course_scope_code" meta_value
		$querystr = " SELECT DISTINCT $wpdb->posts.ID
		FROM $wpdb->posts, $wpdb->postmeta
		WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id
		AND $wpdb->posts.post_status = 'publish'
		AND $wpdb->posts.post_type = 'job_ready_courses'
		AND $wpdb->postmeta.meta_key = 'jrc_course_scope_code'
		AND $wpdb->postmeta.meta_value = %s ";
		
		$course = $wpdb->get_results( $wpdb->prepare($querystr, $course_scope_code), OBJECT);
		$course_id = (int) $course[0]->ID;
		
		$fieldvalue = get_post_meta( $course_id, $fieldname, true );
		
		return $fieldvalue;
		
	}
	
	static function getJobReadyCourseForceToProspectByCourseScopeCode ($course_scope_code)
	{
		global $wpdb;
		
		// Query the database for all "job_ready_courses" and retrieve ID and "jrc_course_scope_code" meta_value
		$querystr = " SELECT DISTINCT $wpdb->posts.ID
		FROM $wpdb->posts, $wpdb->postmeta
		WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id
		AND $wpdb->posts.post_status = 'publish'
		AND $wpdb->posts.post_type = 'job_ready_courses'
		AND $wpdb->postmeta.meta_key = 'jrc_course_scope_code'
		AND $wpdb->postmeta.meta_value = %s ";
		
		$course = $wpdb->get_results( $wpdb->prepare($querystr, $course_scope_code), OBJECT);
		$course_id = (int) $course[0]->ID;
		
		$force_to_prospect = (bool) get_post_meta( $course_id, 'jrc_force_to_prospect', true );
		
		return $force_to_prospect;
		
	}
	
	
	static function getJobReadyCoursePrerequisitesByCourseScopeCode ($course_scope_code)
	{
		global $wpdb;

		// Query the database for all "job_ready_courses" and retrieve ID and "jrc_course_scope_code" meta_value
		$querystr = " SELECT DISTINCT $wpdb->posts.ID
						FROM $wpdb->posts, $wpdb->postmeta
					   WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id
						 AND $wpdb->posts.post_status = 'publish'
						 AND $wpdb->posts.post_type = 'job_ready_courses'
						 AND $wpdb->postmeta.meta_key = 'jrc_course_scope_code'
						 AND $wpdb->postmeta.meta_value = %s ";
		
		$course = $wpdb->get_results( $wpdb->prepare($querystr, $course_scope_code), OBJECT);
		$course_id = (int) $course[0]->ID;
		
		$prerequisites = get_post_meta( $course_id, 'jrc_prerequisites', true );
		
		return $prerequisites;
		
	}
	
	
	// Get all the existing Course Scope Codes from WordPress
	static function getAllCourseScopeCodes()
	{
		global $wpdb;
		
		// Query the database for all "job_ready_courses" and retrieve ID and "jrc_course_scope_code" meta_value
		$querystr = " SELECT DISTINCT $wpdb->posts.ID, $wpdb->postmeta.meta_value as course_scope_code
						FROM $wpdb->posts, $wpdb->postmeta
					   WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id
						 AND $wpdb->posts.post_status = 'publish'
						 AND $wpdb->posts.post_type = 'job_ready_courses'
						 AND $wpdb->postmeta.meta_key = 'jrc_course_scope_code'
					ORDER BY $wpdb->postmeta.meta_value ASC ";
		
		$courses = $wpdb->get_results($querystr, OBJECT);
		
		$jrc_list = array();
		
		foreach($courses as $course)
		{
			$key = $course->course_scope_code;
			$value = $course->ID;
			$jrc_list[$key] = $value;
		}
		
		return $jrc_list;
	}
		
	
	// Load JobReadyCourse by ID
	static function loadJobReadyCourse($post_id)
	{
		$job_ready_course = new JobReadyCourse();
		
		// Retrieve current Course Scope Code, Course Scope Name, Duration and Mode of Stuff based on Course ID
		$job_ready_course->course_scope_code = esc_html( get_post_meta( $post_id, 'jrc_course_scope_code', true ) );
		$job_ready_course->course_scope_name = esc_html( get_post_meta( $post_id, 'jrc_course_scope_name', true ) );
		$job_ready_course->force_to_prospect = (int) get_post_meta( $post_id, 'jrc_force_to_prospect', true ) ;
		$job_ready_course->duration = get_post_meta( $post_id, 'jrc_duration', true );
		$job_ready_course->mode_of_study = get_post_meta( $post_id, 'jrc_mode_of_study', true );
		
		// Retrieves the "textarea" field content (do not strip the HTML)
		$job_ready_course->description = get_post_meta( $post_id, 'jrc_description', true );
		$job_ready_course->course_information = get_post_meta( $post_id, 'jrc_course_information', true );
		$job_ready_course->selection_criteria = get_post_meta( $post_id, 'jrc_selection_criteria', true );
		$job_ready_course->pathways = get_post_meta( $post_id, 'jrc_pathways', true );
		$job_ready_course->rpl = get_post_meta( $post_id, 'jrc_rpl', true );
		$job_ready_course->course_structure = get_post_meta( $post_id, 'jrc_course_structure', true );
		$job_ready_course->fees = get_post_meta( $post_id, 'jrc_fees', true );
		$job_ready_course->how_to_apply = get_post_meta( $post_id, 'jrc_how_to_apply', true );
		$job_ready_course->apply_url = get_post_meta( $post_id, 'jrc_apply_url', true );
		$job_ready_course->register = get_post_meta( $post_id, 'jrc_register', true );
		$job_ready_course->prerequisites = get_post_meta( $post_id, 'jrc_prerequisites', true );
		$job_ready_course->licensing_exam = get_post_meta( $post_id, 'jrc_licensing_exam', true );
		$job_ready_course->footer = get_post_meta( $post_id, 'jrc_footer', true );
		$job_ready_course->featured = get_post_meta( $post_id, 'jrc_featured', true );
		
		return $job_ready_course;
		
	}
	
	
	
	// Create "job_ready_course" in WordPress
	static function createJobReadyCourse( $course )
	{
		$postarr = array(	'post_title'	=> $course->course_scope_code,
							'post_content'	=> ' ',
							'post_status'	=> 'publish',
							'post_type'		=> 'job_ready_courses',
							'post_parent'	=> JOB_READY_COURSE_PAGE_PARENT_ID,
							'meta_input'	=>	array(
								'jrc_course_scope_code'	=>	$course->course_scope_code,
								'jrc_course_scope_name'	=>	$course->course_scope_name
													)
						);
		
		// Fixed bug when new course created
		$new_course = wp_insert_post( $postarr );

		return $new_course;
	}
}