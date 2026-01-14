<?php
include("../wp-load.php");

$course_scope_code = 'SWP';
$course_number = 'SWP 05SEP2018';

check_course_date_and_sync($course_number);

/*
$job_ready_date = JobReadyDateOperations::loadJobReadyDateByCourseNumber($course_number);
var_dump($job_ready_date);

$availability = (int) $job_ready_date->maximum_enrolments - (int) $job_ready_date->enrolment_count;

echo "Availability: " . $availability . "<br/>";
if($availability < 10)
{
	// Job Ready Sync by Course Scope Code
	job_ready_sync_by_course_scope_code($course_scope_code);
}
*/