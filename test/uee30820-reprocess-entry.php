<?php
include("../wp-load.php");

$entry_id = $_GET['entry_id'];
$entry = GFAPI::get_entry( $entry_id );
$form_data = array();

echo "Entry Data: <br/><pre>";
var_dump($entry);
echo "</pre>";

// Calls the UEE30820 Submission Process
uee30820_form_submission_process($entry, $form_data);

echo "Reprocess complete";
?>