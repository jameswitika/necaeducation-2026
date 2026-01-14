<?php

/*
 * Job Ready Sync
 * Created by: James Witika
 * Company: Smooth Developments
 */


if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

// Sync with Job Ready
function job_ready_sync()
{
	global $jr_api_headers;
		
	$method = "GET";
	
	// Call the Job Ready API
	try {
		
		$all_records_complete = false;
		$offset = 0;
		$limit = 100;
		$total_count = 0;
		
		// Counters
		$skipped = 0;
		$new_jrc_count = 0;
		$jrc_exists_count = 0;
		$new_jrd_count = 0;
		$updated_jrd_count = 0;
		
		// Get a list of course_scope_codes from the Course Table
		$course_scope_codes = JobReadyCourseOperations::getAllCourseScopeCodes();
		
		// Get a list of jr_id's from the JRCourse table
		$job_ready_ids = JobReadyDateOperations::getAllJobReadyIDS();
	
		while(!$all_records_complete)
		{
			$query_string = 'end_date_from=' . date('Y-m-d') .
							'&offset=' . $offset . 
							'&limit=' . 100;
			$webservice = '/webservice/courses?' . htmlspecialchars($query_string);
			$url = JR_API_SERVER . $webservice;
			
			//make GET request
			$response = wp_remote_request( $url, array(	'method' 	=> $method,
														'headers' 	=> $jr_api_headers,
														'timeout' 	=> 500 )
														);
			
			// Get the response
			$result = wp_remote_retrieve_body( $response );
			
			// Convert the XML to an Object
			$jra_courses= xmlToObject($result);
					
			foreach($jra_courses as $jra_course)
			{
				$jr_id = (int) $jra_course->id;
				$course_number = (string) $jra_course->{'course-number'};
				$course_name = (string) $jra_course->{'course-information'};
 				//echo "<strong>Course: $jr_id - $course_number: $course_name </strong><br/>";
					
				// Setup the course object (regardless if its NEW or UPDATE
				$jrd = array();
				
				// Need to 'cast' all SimpleXMLObjects to appropriate field type
				$jrd['title'] = (string) $jra_course->{'course-number'};
				$jrd['jrd_jr_id'] = (int) $jra_course->id;
				$jrd['jrd_course_number'] = (string) $jra_course->{'course-number'};
				$jrd['jrd_course_name'] = (string) $jra_course->{'course-information'}; // Represents the course time
				$jrd_course_scope_code = (string) $jra_course->{'course-scope-code'};
				$jrd['jrd_course_scope_code'] = trim($jrd_course_scope_code);
				$jrd['jrd_course_scope_name'] = (string) $jra_course->{'course-scope-name'};
				$jrd['jrd_start_date'] = (string) $jra_course->{'start-date'};
				$jrd['jrd_end_date'] = (string) $jra_course->{'end-date'};
				$jrd['jrd_enrolment_start_date'] = (string) $jra_course->{'enrolment-start-date'};
				$jrd['jrd_enrolment_end_date'] = (string) $jra_course->{'enrolment-end-date'};
				$jrd['jrd_maximum_enrolments'] = (int) $jra_course->{'maximum-enrolments'};
				$jrd['jrd_minimum_enrolments'] = (int) $jra_course->{'minimum-enrolments'};
				$jrd['jrd_enrolment_count'] = (int) $jra_course->{'total-enrolments'};
				$jrd['status'] = 'publish';

				// Gather the "WordPress" Date/Time right now (adjusting for the WordPress GMT setting in ADMIN)
				$now = DateTime::createFromFormat('U', current_time('timestamp'));
				
				if(trim($jrd['jrd_enrolment_end_date']) != '')
				{
					// Form the Enrolnment Date for comparison and set the time to 23:59:59 for comparison
					$enrolment_end_date = DateTime::createFromFormat('Y-m-d', $jrd['jrd_enrolment_end_date']);
					$enrolment_end_date->setTime(23,59,59);
				}
				
				// Do not sync the course if the enrolment_start_date or enrolment_end_date are blank
				// and only sync if the enrolment_end_date is greater than today
				if(trim($jrd['jrd_enrolment_start_date']) != '' && trim($jrd['jrd_enrolment_end_date']) != '' && $enrolment_end_date > $now)
				{
					echo "JRD COURSE SCOPE CODE: " . $jrd['jrd_course_scope_code'];
					
					if( array_key_exists ($jrd['jrd_course_scope_code'], $course_scope_codes))
					{
						$jrd['jrd_course_id'] = $course_scope_codes[$jrd['jrd_course_scope_code']];
						$jrc_exists_count++;
						echo " - Found it (Course ID: ".$jrd['jrd_course_id'].")<br/>";
					}
					else
					{
						$course = new stdClass();
						$course->course_scope_code = $jrd['jrd_course_scope_code'];
						$course->course_scope_name = $jrd['jrd_course_scope_name'];
						
						echo " - Creating it<br/>";
						
						if($course->course_scope_code != '')
						{
							// Create the new job_ready_course in WordPress
							$new_course_id = JobReadyCourseOperations::createJobReadyCourse($course);
						
							// Increments the New Course Counter
							$new_jrc_count++;
						
							$jrd['jrd_course_id'] = $new_course_id;
						
							// Add new Course to Course Scope Codes (so we don't create it every loop)
							$course_scope_codes[$course->course_scope_code] = $new_course_id;
						}
						else
						{
							$jrd['jrd_course_id'] = 0;
						}
					}
					
					// Only process the "job ready date" if a "job_ready_course" is allocated
					if($jrd['jrd_course_id'] > 0)
					{
					
						// Checks to see if the job_ready_date already exists by searching for the unique jr_id (job ready id) field
						if( array_key_exists ($jrd['jrd_jr_id'], $job_ready_ids))
						{
							echo "Course Date already exists<br/>";
							// Set the post id
							$jrd_post_id = $job_ready_ids[$jrd['jrd_jr_id']];
							
							// Update 'job_ready_dates'
							JobReadyDateOperations::updateJobReadyDate($jrd_post_id, $jrd);
							
							// Updates the UPDATED Job Ready Dates Counter
							$updated_jrd_count++;
						}
						else
						{
							echo "Creating NEW Course Date<br/>";
							// Create a new 'job_ready_date'
							$jrd_id = JobReadyDateOperations::createJobReadyDates($jrd);
			
							// Updates the NEW Job Ready Dates Counter
							$new_jrd_count++;
						}
					}
				}
				else 
				{
					$skipped++;
				}
			}
			
			// Checks if there are more records available (if count matches the limit, increment off and re-process next 100 records)
			if(count($jra_courses) == $limit)
			{
				$offset += $limit;
			}
			else
			{
				$all_records_complete = true;
			}
			
			$total_count += count($jra_courses);
		}
		
		// Send an email to the administrator
		$subject = 'NECA Education + Careers - JobReady Course Sync successful';
		$body_content = "	<h1>JobReady Course Sync</h1>
							Total Records Processed: $total_count <br/>
							Skipped (invalid Enrolment Date): $skipped <br/>
							Course Exists: $jrc_exists_count <br/>
							New Course Created: $new_jrc_count <br/>
							JobReady Course Updated: $updated_jrd_count <br/>
							JobReady Course Created: $new_jrd_count <br/>
							Completed on: " . date('d-m-Y h:i:s');
		
		//echo $body_content;
		
		wp_mail('james@smoothdevelopments.com.au', $subject, $body_content);
		
	}
	catch (Exception $e)
	{
		$error = 'CRON > job_ready_course_sync exception error: ' . $e->getMessage();
		
		// Send an email to the administrator
		$subject = 'NECA Education + Careers - JobReady Course Sync error';
		$body_content = "The following error occurs on " . date('d-m-Y') . " while trying to sync the Course data with JobReady:<br/><br/>" . $error . "<br/><br/>Please contact the website administrator.";
		
		echo $body_content;
		wp_mail('james@smoothdevelopments.com.au', $subject, $body_content, $headers = '');
	}
}


// Sync with Job Ready
function job_ready_sync_by_course_scope_code($course_scope_code)
{
	global $jr_api_headers;

	$method = "GET";
	
	// Call the Job Ready API
	try {
		
		$all_records_complete = false;
		$offset = 0;
		$limit = 100;
		$total_count = 0;
		
		// Counters
		$skipped = 0;
		$new_jrc_count = 0;
		$jrc_exists_count = 0;
		$new_jrd_count = 0;
		$updated_jrd_count = 0;
		
		// Get a list of course_scope_codes from the Course Table
		$course_scope_codes = JobReadyCourseOperations::getAllCourseScopeCodes();
				
		// Get a list of jr_id's from the JRCourse table
		$job_ready_ids = JobReadyDateOperations::getAllJobReadyIDS();
		
		while(!$all_records_complete)
		{
			$webservice = '/webservice/courses?course_scope_code=' . $course_scope_code . '&end_date_from=' . date('Y-m-d') . '&offset=' . $offset . '&limit=100';
			$url = JR_API_SERVER . $webservice;
			
			//make GET request
			$response = wp_remote_request( $url, array(	'method' 	=> $method,
														'headers' 	=> $jr_api_headers,
														'timeout' 	=> 500 )
														);
			
			// Get the response
			$result = wp_remote_retrieve_body( $response );
			
			// Convert the XML to an Object
			$jra_courses = xmlToObject($result);
			
			foreach($jra_courses as $jra_course)
			{
				$jr_id = (int) $jra_course->id;
				$course_number = (string) $jra_course->{'course-number'};
				$course_name = (string) $jra_course->{'course-information'};
				// 				echo "<strong>Course: $jr_id - $course_number: $course_name </strong><br/>";
				
				// Setup the course object (regardless if its NEW or UPDATE
				$jrd = array();
				
				// Need to 'cast' all SimpleXMLObjects to appropriate field type
				$jrd['title'] = (string) $jra_course->{'course-number'};
				$jrd['jrd_jr_id'] = (int) $jra_course->id;
				$jrd['jrd_course_number'] = (string) $jra_course->{'course-number'};
				$jrd['jrd_course_name'] = (string) $jra_course->{'course-information'}; // Represents the course time
				$jrd['jrd_course_scope_code'] = (string) $jra_course->{'course-scope-code'};
				$jrd['jrd_course_scope_name'] = (string) $jra_course->{'course-scope-name'};
				$jrd['jrd_start_date'] = (string) $jra_course->{'start-date'};
				$jrd['jrd_end_date'] = (string) $jra_course->{'end-date'};
				$jrd['jrd_enrolment_start_date'] = (string) $jra_course->{'enrolment-start-date'};
				$jrd['jrd_enrolment_end_date'] = (string) $jra_course->{'enrolment-end-date'};
				$jrd['jrd_maximum_enrolments'] = (int) $jra_course->{'maximum-enrolments'};
				$jrd['jrd_minimum_enrolments'] = (int) $jra_course->{'minimum-enrolments'};
				$jrd['jrd_enrolment_count'] = (int) $jra_course->{'total-enrolments'};
				$jrd['status'] = 'publish';
				
				
				// Gather the "WordPress" Date/Time right now (adjusting for the WordPress GMT setting in ADMIN)
				$now = DateTime::createFromFormat('U', current_time('timestamp'));
				
				if(trim($jrd['jrd_enrolment_end_date']) != '')
				{
					// Form the Enrolnment Date for comparison and set the time to 23:59:59 for comparison
					$enrolment_end_date = DateTime::createFromFormat('Y-m-d', $jrd['jrd_enrolment_end_date']);
					$enrolment_end_date->setTime(23,59,59);
				}
				
				// Do not sync the course if the enrolment_start_date or enrolment_end_date are blank
				// and only sync if the enrolment_end_date is greater than today
				
				// 11.11.2021 - Changes conditional to ignore the enrolment_end_date condition and just allow it
				// if(trim($jrd['jrd_enrolment_start_date']) != '' && trim($jrd['jrd_enrolment_end_date']) != '' && $enrolment_end_date > $now)
				
				if(trim($jrd['jrd_enrolment_start_date']) != '' && trim($jrd['jrd_enrolment_end_date']) != '')
				{
					// Check to see if the "course_scope_code" already exists in WordPress
					if( array_key_exists ($jrd['jrd_course_scope_code'], $course_scope_codes))
					{
						$jrd['jrd_course_id'] = $course_scope_codes[$jrd['jrd_course_scope_code']];
						$jrc_exists_count++;
					}
					
					// Only process the "job ready date" if a "job_ready_course" is allocated
					if($jrd['jrd_course_id'] > 0)
					{
						
						// Checks to see if the job_ready_date already exists by searching for the unique jr_id (job ready id) field
						if( array_key_exists ($jrd['jrd_jr_id'], $job_ready_ids))
						{
							// Set the post id
							$jrd_post_id = $job_ready_ids[$jrd['jrd_jr_id']];
							
							// Update 'job_ready_dates'
							JobReadyDateOperations::updateJobReadyDate($jrd_post_id, $jrd);
							
							// Updates the UPDATED Job Ready Dates Counter
							$updated_jrd_count++;
						}
						else
						{
							// Create a new 'job_ready_date'
							$jrd_id = JobReadyDateOperations::createJobReadyDates($jrd);
							
							// Updates the NEW Job Ready Dates Counter
							$new_jrd_count++;
						}
					}
					// 				echo "<br/><br/>";
				}
				else
				{
					$skipped++;
				}
			}
			
			// Checks if there are more records available (if count matches the limit, increment off and re-process next 100 records)
			if(count($jra_courses) == $limit)
			{
				$offset += $limit;
			}
			else
			{
				$all_records_complete = true;
			}
			
			$total_count += count($jra_courses);
		}
		
		// Send an email to the administrator
		$subject = 'NECA Education + Careers - JobReady Course Single Sync for ' . $course_scope_code . ' successful';
		$body_content = "	<h1>JobReady Course Sync</h1>
		Total Records Processed: $total_count <br/>
		Skipped (invalid Enrolment Date): $skipped <br/>
		Course Exists: $jrc_exists_count <br/>
		New Course Created: $new_jrc_count <br/>
		JobReady Course Updated: $updated_jrd_count <br/>
		JobReady Course Created: $new_jrd_count <br/>
		Completed on: " . date('d-m-Y h:i:s');
		
		echo $body_content;
		
		wp_mail('james@smoothdevelopments.com.au', $subject, $body_content);
		
	}
	catch (Exception $e)
	{
		$error = 'CRON > job_ready_course_sync (single: '.$course_scope_code.') exception error: ' . $e->getMessage();
		
		// Send an email to the administrator
		$subject = 'NECA Education + Careers - JobReady Course Sync error';
		$body_content = "The following error occurs on " . date('d-m-Y') . " while trying to sync the Course data with JobReady:<br/><br/>" . $error . "<br/><br/>Please contact the website administrator.";
		
		echo $body_content;
		wp_mail('james@smoothdevelopments.com.au', $subject, $body_content, $headers = '');
	}
}



function check_course_date_and_sync($course_number)
{
	$job_ready_date = JobReadyDateOperations::loadJobReadyDateByCourseNumber($course_number);
	if($job_ready_date)
	{
		$availability = $job_ready_date->maximum_enrolments - $job_ready_date->enrolment_count;
		$course_scope_code = $job_ready_date->course_scope_code;
		
		// Check if availability less than 3
		/*if($availability < 3)
		{
			// Job Ready Sync by Course Scope Code
			job_ready_sync_by_course_scope_code($course_scope_code);
		}*/

		// 2022.07.06 - Sync everytime
		// Job Ready Sync by Course Scope Code
		job_ready_sync_by_course_scope_code($course_scope_code);
	}
}