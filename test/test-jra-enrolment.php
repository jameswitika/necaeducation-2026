<?php
include("../wp-load.php");

// Test JRA Enrolment Retrieval by Enrolment ID
$enrolment_id = 'ENNEC59082';
$enrolment = JRAEnrolmentOperations::getJRAEnrolmentByEnrolmentID($enrolment_id);
if ($enrolment) {
    echo "Enrolment retrieved successfully:<br/>";
    print_r($enrolment);
    echo "<br/><br/>";
} 
else 
{
    echo "Failed to retrieve enrolment with ID: $enrolment_id<br/>";
}

// Validate the Party ID is associated with the Enrolment
$party_id = 'PA25335';

if ($enrolment && isset($enrolment->{'party-identifier'})) {
    $associated_party_id = (string)$enrolment->{'party-identifier'};
    if ($associated_party_id === $party_id) {
        echo "Party ID $party_id is correctly associated with the enrolment.<br/>";
    } else {
        echo "Party ID $party_id is NOT associated with the enrolment. Found: $associated_party_id<br/>";
    }
} else {
    echo "Enrolment data does not contain a party identifier.<br/>";
}

// Get the Enrolment Status
if ($enrolment && isset($enrolment->{'enrolment-status'})) {
    $enrolment_status = (string)$enrolment->{'enrolment-status'};
    echo "Enrolment Status: $enrolment_status<br/>";
} else {
    echo "Enrolment data does not contain a status.<br/>";
}

// Get the Course Number
if ($enrolment && isset($enrolment->{'course-number'})) {
    $course_number = (string)$enrolment->{'course-number'};
    echo "Course Number: $course_number<br/>";

    // Get the Course Scope Code by using the Course Number to lookup the Course Details
    $jrd = JobReadyDateOperations::loadJobReadyDateByCourseNumber($course_number);

    if ($jrd && isset($jrd->course_scope_code)) {
        $course_scope_code = (string)$jrd->course_scope_code;
        echo "Course Scope Code: $course_scope_code<br/>";
    } else {
        echo "Failed to retrieve Course Scope Code for Course Number: $course_number<br/>";
    }

} else {
    echo "Enrolment data does not contain a course number.<br/>";
}
