<?php
include("../wp-load.php");

$course_scope_code = 'SWP';
$course_number = 'SWP 05SEP2018';

// Get a list of jr_id's from the JRCourse table
$job_ready_ids = JobReadyDateOperations::getAllJobReadyIDS();
echo "Job Ready IDs (original): " . count($job_ready_ids) . "<br/>";

$job_ready_ids2 = getAllJobReadyIDS2();
echo "Job Ready IDs (new): " . count($job_ready_ids2) . "<br/>";

// Get all the existing 'job_ready_dates' IDs from WordPress
function getAllJobReadyIDS2()
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