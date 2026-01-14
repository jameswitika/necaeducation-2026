<?php
include("../wp-load.php");

$entry_id = $_GET['entry_id'];
$entry = GFAPI::get_entry( $entry_id );
$form_data = array();

echo "Entry Data: <br/><pre>";
var_dump($entry);
echo "</pre>";

// Calls the Electrical Apprenticeship Submission Process
asc_holding_bay_form_accredited_submission_process($entry, $form_data);

echo "ASC Holding Bay Reprocess complete";
?>