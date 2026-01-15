<?php
include("../wp-load.php");

// Test JRA Party Retrieval by Party ID
$party_id = 'PA25335';

$jra_party = JRAPartyOperations::loadJRAPartyByID( $party_id );
if ( $jra_party ) {
    echo "Successfully retrieved JRA Party data for Party ID: $party_id<br/>";
    // echo "<pre>";
    // print_r( $jra_party );
    // echo "</pre><br/>";
} else {
    echo "Failed to retrieve JRA Party data for Party ID: $party_id<br/>";
}

$form_fields = JobReadyFormOperations::convertPartyXMLToJobReadyForm($jra_party);
$prefill_fields = array();
$prefill_fields['2'] = $form_fields->title;
$prefill_fields['9'] = $form_fields->first_name;
$prefill_fields['28'] = $form_fields->middle_name;
$prefill_fields['8'] = $form_fields->surname;
$prefill_fields['10'] = $form_fields->known_by;
$prefill_fields['11'] = $form_fields->birth_date;
$prefill_fields['27'] = $form_fields->gender;
$prefill_fields['30'] = $form_fields->street_address1;
$prefill_fields['31'] = $form_fields->suburb;
$prefill_fields['32'] = $form_fields->state;
$prefill_fields['33'] = $form_fields->postcode;

//$prefill_fields['43'] = $form_fields->postal_address_same;
$prefill_fields['44'] = $form_fields->postal_street_address1;
$prefill_fields['47'] = $form_fields->postal_suburb;
$prefill_fields['48'] = $form_fields->postal_state;
$prefill_fields['46'] = $form_fields->postal_postcode;

$prefill_fields['20'] = $form_fields->home_phone;
$prefill_fields['19'] = $form_fields->mobile_phone;

echo "Prefill Fields:<br/>";
var_dump($prefill_fields);
?>