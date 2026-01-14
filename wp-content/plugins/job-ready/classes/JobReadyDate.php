<?php
/*
 * JobReadyDateOperations class
 * Created by: James Witika
 * Company: Smooth Developments
 */

if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}


class JobReadyDateOperations {
	
	// __constructor function
	function __construct()
	{
	}

	
	static function loadJobReadyDate($post_id)
	{
		$job_ready_date = new stdClass();

		// Single call to the DB
		$meta = get_metadata('post', $post_id);
		
		// Retrieve current Course Scope Code, Course Scope Name, Duration and Mode of Stuff based on Course ID
		$job_ready_date->course_id = (int) $meta['jrd_course_id'][0];
		$job_ready_date->jr_id = (int) esc_html( $meta['jrd_jr_id'][0]);
		$job_ready_date->course_number = esc_html( $meta['jrd_course_number'][0]);
		$job_ready_date->course_name = esc_html( $meta['jrd_course_name'][0]);
		$job_ready_date->course_scope_code = esc_html( $meta['jrd_course_scope_code'][0]);
		$job_ready_date->course_scope_name = esc_html( $meta['jrd_course_scope_name'][0]);
		
		// Retrieves the "textarea" field content (do not strip the HTML)
		$start_date = $meta['jrd_start_date'][0];
		$job_ready_date->start_date_clean = ($start_date != '') ? date("d/m/Y", strtotime($start_date)) : '';
		$end_date = $meta['jrd_end_date'][0];
		$job_ready_date->end_date_clean = ($end_date != '') ? date("d/m/Y", strtotime($end_date)): '';
		$job_ready_date->enrolment_start_date = $meta['jrd_enrolment_start_date'][0];
		$job_ready_date->enrolment_start_date_clean = ($job_ready_date->enrolment_start_date != '') ? date("d/m/Y", strtotime($job_ready_date->enrolment_start_date)) : '';
		$job_ready_date->enrolment_end_date = $meta['jrd_enrolment_end_date'][0];
		$job_ready_date->enrolment_end_date_clean = ($job_ready_date->enrolment_end_date != '') ? date("d/m/Y", strtotime($job_ready_date->enrolment_end_date)) : '';
		$job_ready_date->maximum_enrolments = (int) $meta['jrd_maximum_enrolments'][0];
		$job_ready_date->minimum_enrolments = (int) $meta['jrd_minimum_enrolments'][0];
		$job_ready_date->enrolment_count = $meta['jrd_enrolment_count'][0];
		
		return $job_ready_date;
	}
	
	
	
	static function loadJobReadyDateByCourseNumber($jrd_course_number)
	{
		global $wpdb;
		
		// Query the database for all "job_ready_courses" and retrieve ID and "jrc_course_scope_code" meta_value
		$querystr = $wpdb->prepare( " SELECT DISTINCT $wpdb->posts.*
										FROM $wpdb->posts, $wpdb->postmeta
									   WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id
										 AND $wpdb->posts.post_type = 'job_ready_dates'
										 AND $wpdb->posts.post_status IN ('publish', 'private')
										 AND $wpdb->postmeta.meta_key = 'jrd_course_number'
										 AND $wpdb->postmeta.meta_value = %s
									ORDER BY $wpdb->postmeta.meta_value ASC ", $jrd_course_number);

		$post = $wpdb->get_results($querystr, OBJECT);
		
		if(count($post) == 1)
		{
			$post_id = $post[0]->ID;
			$job_ready_date = new stdClass();
			
			// Retrieve current Course Scope Code, Course Scope Name, Duration and Mode of Stuff based on Course ID
			$job_ready_date->ID = $post_id;
			$job_ready_date->course_id = (int) get_post_meta( $post_id, 'jrd_course_id', true );
			$job_ready_date->jr_id = (int) esc_html( get_post_meta( $post_id, 'jrd_jr_id', true ) );
			$job_ready_date->course_number = esc_html( get_post_meta( $post_id, 'jrd_course_number', true ) );
			$job_ready_date->course_name = esc_html( get_post_meta( $post_id, 'jrd_course_name', true ) );
			$job_ready_date->course_scope_code = esc_html( get_post_meta( $post_id, 'jrd_course_scope_code', true ) );
			$job_ready_date->course_scope_name = esc_html( get_post_meta( $post_id, 'jrd_course_scope_name', true ) );
			
			// Retrieves the "textarea" field content (do not strip the HTML)
			$start_date = get_post_meta( $post_id, 'jrd_start_date', true );
			$job_ready_date->start_date_clean = ($start_date != '') ? date("d/m/Y", strtotime($start_date)) : '';
			$end_date = get_post_meta( $post_id, 'jrd_end_date', true );
			$job_ready_date->end_date_clean = ($end_date != '') ? date("d/m/Y", strtotime($end_date)): '';
			$enrolment_start_date = get_post_meta( $post_id, 'jrd_enrolment_start_date', true );
			//$job_ready_date->enrolment_start_date_clean = ($enrolment_start_date != '') ? date("d/m/Y", strtotime($enrolment_start_date)) : '';
			//$enrolment_end_date = get_post_meta( $post_id, 'jrd_enrolment_end_date', true );
			//$job_ready_date->enrolment_end_date_clean = ($enrolment_end_date != '') ? date("d/m/Y", strtotime($enrolment_end_date)) : '';
			$job_ready_date->maximum_enrolments = (int) get_post_meta( $post_id, 'jrd_maximum_enrolments', true );
			$job_ready_date->minimum_enrolments = (int) get_post_meta( $post_id, 'jrd_minimum_enrolments', true );
			$job_ready_date->enrolment_count = (int) get_post_meta( $post_id, 'jrd_enrolment_count', true );
			
			$enrolments_remaining = (int)$job_ready_date->maximum_enrolments - (int)$job_ready_date->enrolment_count;
			$job_ready_date->enrolments_remaining = $enrolments_remaining;
			
			return $job_ready_date;
		}
		
		return false;
		
	}
	
	
	static function loadJobReadyDatesByCourseID($jrd_course_id)
	{
		global $wpdb;

		// Replace the custom query with the WP_QUERY using params to do the exact same thing except sort using the meta_key "jrd_start_dtte"
		$jr_args = array(	'post_type' => 'job_ready_dates',
							'post_status' => 'publish',
							'posts_per_page'   => -1,
							'orderby' => 'meta_value',
							'order'	=> 'ASC',
							'meta_key' => 'jrd_start_date',
							'meta_query' => array(
									array(	'key' => 'jrd_course_id',
											'value' => array( $jrd_course_id ),
											'compare' => 'IN',
									)
							)
					);
		
		$query = new WP_Query( $jr_args );
		$job_ready_dates = $query->posts;
		
		/* Replace our manual query with WP_Query and ARGS to include better sorting options 
		$querystr = $wpdb->prepare( " SELECT DISTINCT $wpdb->posts.*
										FROM $wpdb->posts, $wpdb->postmeta
									   WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id
										 AND $wpdb->posts.post_type = 'job_ready_dates'
										 AND $wpdb->posts.post_status = 'publish'
										 AND $wpdb->postmeta.meta_key = 'jrd_course_id'
										 AND $wpdb->postmeta.meta_value = %d
									ORDER BY $wpdb->postmeta.meta_value ASC ", $jrd_course_id);
		
		$job_ready_dates = $wpdb->get_results($querystr, OBJECT);
		*/
		
		$final_dates = array();
		
		foreach($job_ready_dates as $job_ready_date)
		{
			$meta = JobReadyDateOperations::loadJobReadyDate((int)$job_ready_date->ID);
			$enrolments_remaining = (int)$meta->maximum_enrolments - (int)$meta->enrolment_count;
			
			// TODO: Check last update and perform an update if enrolments < 5
			
			if($enrolments_remaining > 0 && $enrolments_remaining <= 5)
			{
				$meta->enrolments_remaining_formatted = $enrolments_remaining == 1 ? $enrolments_remaining . " spot left" : $enrolments_remaining . " spots left";
			}
			
			
			// Check if enrolment_start_date
			$enrolment_start_date = date_create($meta->enrolment_start_date);
			$enrolment_end_date = date_create($meta->enrolment_end_date);
			
			date_time_set($enrolment_start_date,0,0,0);
			date_time_set($enrolment_end_date,23,59,59);
			$now = DateTime::createFromFormat('U', current_time('timestamp'));
			
			if( $enrolment_start_date < $now && $enrolment_end_date > $now ) // Valid Date
			{
				if( $meta->maximum_enrolments > $meta->enrolment_count)
				{
					// Enrolment Link
					//$meta->link_enrol = "https://necaskillscentre.jobreadyrto.com.au/terminal/online_book_course/" . $meta->jr_id;
					$meta->show_enrol = true;
					$meta->full = false;
				}
				else
				{
					$meta->show_enrol = false;
					$meta->full = true;
				}
				
			}
			
			$job_ready_date->meta = $meta;
			
			if( $enrolment_start_date < $now && $enrolment_end_date > $now ) // Valid Date
			{
				array_push($final_dates, $job_ready_date);
			}
		}
		
		return $final_dates;
		
	}
	
	
	
	static function removeExpiredJobReadyDates()
	{
		global $wpdb;
		
		// Query the database for all "job_ready_courses" and retrieve ID and "jrc_course_scope_code" meta_value
		$querystr = "UPDATE neca_posts, neca_postmeta 
						SET neca_posts.post_status = 'trash'
					  WHERE neca_posts.ID = neca_postmeta.post_id 
						AND neca_posts.post_type = 'job_ready_dates' 
						AND neca_posts.post_status = 'publish' 
						AND neca_postmeta.meta_key = 'jrd_end_date' 
						AND neca_postmeta.meta_value < '" . current_time('mysql', 1) . "'";
		
		$posts = $wpdb->get_results($querystr, OBJECT);
	}
	
	

	// Get all the existing 'job_ready_dates' IDs from WordPress
	static function getAllJobReadyIDS()
	{
		global $wpdb;
		
		// Query the database for all "job_ready_courses" and retrieve ID and "jrc_course_scope_code" meta_value
		$querystr = " SELECT DISTINCT $wpdb->posts.ID, $wpdb->postmeta.meta_value as jr_id
						FROM $wpdb->posts, $wpdb->postmeta
					   WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id
						 AND $wpdb->posts.post_type = 'job_ready_dates'
						 AND $wpdb->posts.post_status IN ('publish', 'draft', 'private')
						 AND $wpdb->postmeta.meta_key = 'jrd_jr_id'
					ORDER BY $wpdb->postmeta.meta_value ASC ";
		
		$job_ready_dates = $wpdb->get_results($querystr, OBJECT);
		
		$jrd_list = array();
		
		foreach($job_ready_dates as $job_ready_date)
		{
			$key = $job_ready_date->jr_id;
			$value = $job_ready_date->ID;
			$jrd_list[$key] = $value;
		}
		
		return $jrd_list;
	}
	
	
	static function updateJobReadyDate( $post_id, $job_ready_date )
	{
		if ( ! get_post( $post_id ) || ! is_array( $job_ready_date) || empty( $job_ready_date) ) {
			echo "Error with variables";
			return false;
		}
		
		foreach ( $job_ready_date as $meta_key => $meta_value )
		{
			//echo "$meta_key = $meta_value <br/>";
			update_post_meta( $post_id, $meta_key, $meta_value );
		}
		
		return true;
	}
	
	
	static function createJobReadyDates( $job_ready_date )
	{
		$postarr = array(	'post_title'	=> $job_ready_date['title'],
							'post_content'	=> ' ',
							'post_status'	=> 'publish',
							'post_type'		=> 'job_ready_dates',
							'meta_input'	=>	array(	'jrd_jr_id'					=> $job_ready_date['jrd_jr_id'],
														'jrd_course_number'			=> $job_ready_date['jrd_course_number'],
														'jrd_course_id'				=> $job_ready_date['jrd_course_id'],
														'jrd_course_name'			=> $job_ready_date['jrd_course_name'],
														'jrd_course_scope_code'		=> $job_ready_date['jrd_course_scope_code'],
														'jrd_course_scope_name'		=> $job_ready_date['jrd_course_scope_name'],
														'jrd_start_date'			=> $job_ready_date['jrd_start_date'],
														'jrd_end_date'				=> $job_ready_date['jrd_end_date'],
														'jrd_enrolment_start_date'	=> $job_ready_date['jrd_enrolment_start_date'],
														'jrd_enrolment_end_date'	=> $job_ready_date['jrd_enrolment_end_date'],
														'jrd_maximum_enrolments'	=> $job_ready_date['jrd_maximum_enrolments'],
														'jrd_minimum_enrolments'	=> $job_ready_date['jrd_minimum_enrolments'],
														'jrd_enrolment_count'		=> $job_ready_date['jrd_enrolment_count'])
						);
		
		$job_ready_date_id = wp_insert_post( $postarr );
		
		return $job_ready_date_id;
	}
}