<?php
include("../wp-load.php");

$entry_id = $_GET['entry_id'];
$entry = GFAPI::get_entry( $entry_id );
$form_data = array();

echo "Entry Data: <br/><pre>";
var_dump($entry);
echo "</pre>";

// Calls the Short Course Application NECGDC Form Accredited Submission Process
project_management_diploma_form_submission_process($entry, $form_data);

// Calls the NECGDC002_pdf_only function

echo "Reprocess complete";

?>