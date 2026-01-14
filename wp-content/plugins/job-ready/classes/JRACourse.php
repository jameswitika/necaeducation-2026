<?php
/*
 * JRACourse class + JRACourseOperations class
 * Created by: James Witika
 * Company: Smooth Developments
 */

if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class JRACourse
{
	var $course_number;			// string Automatically generated if blank
	var $course_name;			// string
	var $course_scope_code;		// reference Mandatory	if	course_scope_name	is	blank
	var $course_scope_name;		// reference Mandatory	if	course_scope_code	is	blank
	var $start_date;			// date Mandatory
	var $end_date;				// date Mandatory
	var $enrolment_start_date;	// date
	var $enrolment_end_date;	// date
	var $maximum_enrolments;		// integer Mandatory
	var $minimum_enrolments;		// integer Mandatory
	var $total_enrolments;		// integer Read Only
	var $invoice_options;		// array of invoice options
	
	function __construct()
	{
		$this->course_number = '';
		$this->course_name = '';
		$this->course_status = '';
		$this->course_scope_code = '';
		$this->course_scope_name = '';
		$this->start_date = '';
		$this->end_date = '';
		$this->enrolment_start_date = '';
		$this->enrolment_end_date = '';
		$this->maximum_enrolments = 0;
		$this->minimum_enrolments = 0;
		$this->total_enrolments = 0;
		$this->invoice_options = array();
	}
}

class JRACourseOperations
{
	function __construct()
	{
		
	}
	
	
	static function loadJRACourseByCourseNumber( $course_number )
	{
		global $jr_api_headers;
		
		$method = "GET";
		$webservice = '/webservice/courses/' . $course_number;
		$url = JR_API_SERVER . $webservice;
		
		// Call the Job Ready API
		try {
			//make POST request
			$response = wp_remote_request( $url, array(	'method' 	=> $method,
														'headers' 	=> $jr_api_headers,
														'timeout' 	=> 500 )
										);
			
			// Get the response
			$result = wp_remote_retrieve_body( $response );
			
			// Convert the XML to an Object
			// The new endpoint returns a single object (not an array any more)
			$jra_course = xmlToObject($result);
			
			// No longer required as an array is not returned
			//$jra_course = $jra_courses[0]->course;
			
			$course = new JRACourse();
			$course->course_number = (string) $jra_course->{'course-number'};
			$course->course_name = (string) $jra_course->{'course-name'};
			$course->course_scope_code = (string) $jra_course->{'course-scope-code'};
			$course->course_scope_name = (string) $jra_course->{'course-scope-name'};
			$course->start_date = (string) $jra_course->{'start-date'};
			$course->end_date = (string) $jra_course->{'end-date'};
			$course->enrolment_start_date = (string) $jra_course->{'enrolment-start-date'};
			$course->enrolment_end_date = (string) $jra_course->{'enrolment-end-date'};
			$course->maximum_enrolments = (int) $jra_course->{'maximum-enrolments'};
			$course->minimum_enrolments = (int) $jra_course->{'minimum-enrolments'};
			$course->total_enrolments = (int) $jra_course->{'total-enrolments'};
			
			// If the course date has "invoice-options" then there are pricing options available.
			if(isset($jra_course->{'invoice-options'}))
			{
				$jra_invoice_options = $jra_course->{'invoice-options'}->{'invoice-option'};
				
				$date_now = date_create_from_format("Y-m-d", current_time('Y-m-d'), timezone_open("Australia/Melbourne"));
				
				// Loop through all invoice options
				foreach($jra_invoice_options as $jra_invoice_option)
				{
					$enabled = (string) $jra_invoice_option->enabled;
					//$online_enrolment_enabled = (string) $jra_invoice_option->{'online-enrolment-enabled'};
					$date_from_string = (string) $jra_invoice_option->{'date-from'};
					$date_to_string = (string) $jra_invoice_option->{'date-to'};
					
					$date_from = date_create_from_format("Y-m-d", $date_from_string, timezone_open("Australia/Melbourne"));
					if(trim($date_to_string) != '')
					{
						$date_to = date_create_from_format("Y-m-d", $date_to_string, timezone_open("Australia/Melbourne"));
					}
					else
					{
						$date_to = date_create_from_format("Y-m-d", current_time('Y-m-d'), timezone_open("Australia/Melbourne"));
						$date_to->add(new DateInterval('P1M'));
					}
					
					// TO BE CONFIRMED
					// && $online_enrolment_enabled == 'true'
					if($enabled == 'true' && $date_now > $date_from && $date_now < $date_to)
					{
						// Creates a generic class for invoice option					
						$invoice_option = new stdClass();
						$invoice_option->name = (string) $jra_invoice_option->name;
						$invoice_option->total = (float) $jra_invoice_option->total;
						
						if( strpos($invoice_option->name, 'NECA Member') !== false)
						{
							$invoice_option->neca_member = true;
						}
						else
						{
							$invoice_option->neca_member = false;
						}
						
						
						if( strpos($invoice_option->name, '(Internal)') !== false)
						{
							$invoice_option->internal = true;
						}
						else
						{
							$invoice_option->internal = false;
						}
						
						
						// Add invoice option to the invoice options array
						array_push($course->invoice_options, $invoice_option);
					}
				}
			}
			
			return $course;
		}
		catch (Exception $e)
		{
			$error = 'JRACourse > loadJRACourseByCourseNumber() exception error: ' . $e->getMessage();
			
			// Send an email to the administrator
			$subject = 'NECA Education + Careers - loadJRACourseByCourseNumber error';
			$body_content = "The following error occurs on " . date('d-m-Y') . " while trying to sync the Course data with JobReady:<br/><br/>" . $error . "<br/><br/>Please contact the website administrator.";
			
			echo $body_content;
			wp_mail('james@smoothdevelopments.com.au', $subject, $body_content, $headers = '');
			
			return false;
		}
	}
}