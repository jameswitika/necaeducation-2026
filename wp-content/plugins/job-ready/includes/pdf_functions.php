<?php

// Create PDF
function neca_create_pdf($filename, $content) 
{
	try {
		// init HTML2PDF
		$html2pdf = new Html2Pdf ( 'P', 'A4', 'fr', true, 'UTF-8', array (
				0,
				0,
				0,
				0
		) );
		
		// display the full page
		$html2pdf->pdf->SetDisplayMode ( 'fullpage' );
		$html2pdf->setDefaultFont('arial');
		$html2pdf->AddFont('dejavusans');
		
		// convert
		$html2pdf->writeHTML ( $content, isset ( $_GET ['vuehtml'] ) );
		
		// send the PDF
		$html2pdf->Output ( JR_ROOT_PATH . '/pdf/' . $filename, 'F' );
	}
	catch ( HTML2PDF_exception $e ) 
	{
		echo $e;
		exit ();
	}
}


function neca_pdf_content_start() 
{
	//<img src="' . get_site_url () . '/wp-content/plugins/job-ready/images/neca-education-and-careers-logo.jpg"><br/>
	$content = '<style>
					.heading1 { color: black; padding: 10px 0px; font-size: 20px; }
					.heading2 { color: black; padding: 0px 0px 10px; font-size: 16px; }
					.heading3 { background: #429cd6; color: #fff; padding: 10px; margin-top: 10px; }
					.tbl { width: 100%;}
					.tbl2 { width: 100%;}
					.tbl td { border-bottom: 1px solid #000; }
					.small { font-size: 9px; }
				</style>
			
				<page backtop="7mm" backbottom="7mm" backleft="10mm" backright="10mm" footer="page">
					<page_header>
						<div style="text-align:right; padding-right: 40px; padding-top: 20px;">
                            <img src="' . $_SERVER['DOCUMENT_ROOT']. '/wp-content/plugins/job-ready/images/neca-education-and-careers-logo.jpg"><br/>
                            
							RTO Code: 21098
						</div>
					</page_header>';
	return $content;
}


function neca_pdf_page_start() 
{
	$content = '<page pageset="old" footer="page">
					<div>&nbsp;<br/>&nbsp;<br/>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br></div>
';
	return $content;
}


function neca_pdf_content_heading($heading, $form) 
{
	// Load the Course Scope Name
	$course_scope_name = JobReadyCourseOperations::getJobReadyCourseFieldByCourseScopeCode($form->course_scope_code, 'jrc_course_scope_name');
	
	$content = '<div class="heading1">' . $heading . '</div>
				<div class="heading2">Qualification Code: ' . $form->course_scope_code . '</div>
				<div class="heading2">Qualification Title: ' . $course_scope_name . '</div>';				
// 	$content .= '<div class="heading2">Course Number: ' . $form->course_number . '</div>';

	return $content;
}


function neca_pdf_page_end() 
{
	$content = '</page>';
	return $content;
}


function neca_pdf_previously_enrolled($form)
{
	$content = '';
	
	$content = '<div class="heading3">PREVIOUS ENROLMENT</div>
					<table class="tbl">
						<tr>
							<td style="width: 40%;">Have you ever enrolled at NECA Education and Careers Before?</td>
							<td style="width: 60%;">' . $form->previously_enrolled_at_neca . '</td>
						</tr>
					</table>';
	
	return $content;
	
}

function neca_pdf_content_personal_details($form) 
{
	$content = '<div class="heading3">PERSONAL DETAILS</div>
					<table class="tbl">
						<tr>
							<td style="width: 40%;">Title:</td>
							<td style="width: 60%;">' . $form->title . '</td>
						</tr>
						<tr>
							<td style="width: 40%;">First Name:</td>
							<td style="width: 60%;">' . $form->first_name . '</td>
						</tr>
						<tr>
							<td style="width: 40%;">Surname:</td>
							<td style="width: 60%;">' . $form->surname . '</td>
						</tr>
						<tr>
							<td style="width: 40%;">Known By:</td>
							<td style="width: 60%;">' . $form->known_by . '</td>
						</tr>
						<tr>
							<td style="width: 40%;">Gender:</td>
							<td style="width: 60%;">' . $form->gender . '</td>
						</tr>
						<tr>
							<td style="width: 40%;">Date of Birth:</td>
							<td style="width: 60%;">' . date ( 'd M Y', strtotime ( $form->birth_date ) ) . '</td>
						</tr>
						<tr>
							<td style="width: 40%;">Address:</td>
							<td style="width: 60%;">' . $form->street_address1 . '</td>
						</tr>
						<tr>
							<td style="width: 40%;">Suburb:</td>
							<td style="width: 60%;">' . $form->suburb . '</td>
						</tr>
						<tr>
							<td style="width: 40%;">State:</td>
							<td style="width: 60%;">' . $form->state . '</td>
						</tr>
						<tr>
							<td style="width: 40%;">Postcode:</td>
							<td style="width: 60%;">' . $form->postcode . '</td>
						</tr>';
	
	// If there a different postal address?
	if (isset ( $form->postal_street_address1 ) && $form->postal_street_address1 != '') {
		
		$content .= '	<tr>
							<td colspan="2" style="width: 100%"><strong>Postal Address</strong></td>
						</tr>
						<tr>
							<td style="width: 40%;">Address:</td>
							<td style="width: 60%;">' . $form->postal_street_address1 . '</td>
						</tr>
						<tr>
							<td style="width: 40%;">Suburb:</td>
							<td style="width: 60%;">' . $form->postal_suburb . '</td>
						</tr>
						<tr>
							<td style="width: 40%;">State:</td>
							<td style="width: 60%;">' . $form->postal_state . '</td>
						</tr>
						<tr>
							<td style="width: 40%;">Postcode:</td>
							<td style="width: 60%;">' . $form->postal_postcode . '</td>
						</tr>';
	} else {
		$content .= '	<tr>
							<td style="width: 40%">Postal Address</td>
							<td style="width: 60%">Same as above</td>
						</tr>';
	}
	
	$content .= '		<tr>
							<td style="width: 40%;">Home Phone: </td>
							<td style="width: 60%;">' . $form->home_phone . '</td>
						</tr>
						<tr>
							<td style="width: 40%;">Mobile: </td>
							<td style="width: 60%;">' . $form->mobile_phone . '</td>
						</tr>
						<tr>
							<td style="width: 40%;">Email: </td>
							<td style="width: 60%;">' . $form->email . '</td>
						</tr>
				 	</table>';
	
	return $content;
}


// Emergency Contact Details
function neca_pdf_content_emergency_content_details($form) {
	$content = '<div class="heading3">EMERGENCY CONTACT</div>
				<table class="tbl">
					<tr>
						<td style="width: 40%;">First Name:</td>
						<td style="width: 60%;">' . $form->emergency_contact_firstname . '</td>
					</tr>
					<tr>
						<td style="width: 40%;">Surname:</td>
						<td style="width: 60%;">' . $form->emergency_contact_surname . '</td>
					</tr>
					<tr>
						<td style="width: 40%;">Number:</td>
						<td style="width: 60%;">' . $form->emergency_contact_number . '</td>
					</tr>';
	
	if ($form->emergency_contact_email != '') {
		$content .= '	<tr>
							<td style="width: 40%;">Email:</td>
							<td style="width: 60%;">' . $form->emergency_contact_email . '</td>
						</tr>';
	}
	
	$content .= '	<tr>
						<td style="width: 40%;">Relationship:</td>
						<td style="width: 60%;">' . $form->emergency_contact_relationship . '</td>
					</tr>
				</table>';
	
	return $content;
}


// Avetmiss Heading
function neca_pdf_content_avetmiss_heading($form) 
{
	$content = '<div class="heading2"><br/>AVETMISS Data</div>';
	return $content;
}


// Labour Force Details
function neca_pdf_content_labour_details($form) 
{
	$content = '<div class="heading3">LABOUR STATUS</div>
				<table class="tbl">
					<tr>
						<td style="width:100%;">The following category best describes your current employment status:</td>
					</tr>
					<tr>
						<td style="width:100%;">' . $form->labour_force_status . '</td>
					</tr>
				</table>';
	
	return $content;
}


// Referred Details
function neca_pdf_content_referred_details($form) 
{
	$content = '<div class="heading3">REFERRED FROM A JOB SEEKER</div>
				<table class="tbl">
					<tr>
						<td style="width:100%;">If unemployed, were you referred from a Job Seeker?</td>
					</tr>
					<tr>
						<td style="width:100%;">' . $form->referred . '</td>
					</tr>';
	if ($form->referred == 'Yes') {
		$content .= '<tr>
						<td style="width:100%;">' . $form->referred_details . '</td>
					</tr>';
	}
	
	$content .= '</table>';
	
	return $content;
}


// Language Details
function neca_pdf_content_language_details($form, $show_australian_citizen = false) 
{
	$content = '<div class="heading3">NATIONALITY AND LANGUAGE DETAILS</div>
					<table class="tbl">
						<tr>
							<td style="width: 40%;">Country of Birth</td>
							<td style="width: 60%;">' . $form->country_of_birth . '</td>
						</tr>';
	
	if ($show_australian_citizen) 
	{
		$content .= ' 	<tr>
							<td style="width: 40%;">Are you an Australian Citizen or Permanent Resident?</td>
							<td style="width: 60%;">' . $form->australian_citizen . '</td>
						</tr>';
		
		if ($form->australian_citizen == 'No') 
		{
			$content .= '	<tr>
									<td style="width: 40%;">If no, please advise where you are a citizen from</td>
									<td style="width: 60%;">' . $form->citizenship_other . '</td>
								</tr>';
		}
	}
	
	$content .= '		<tr>
							<td style="width: 40%;">Indigenous Status?</td>
							<td style="width: 60%;">' . $form->indigenous_status . '</td>
						</tr>
						<tr>
							<td style="width: 40%;">Do you speak a language other than English at home?:</td>
							<td style="width: 60%;">' . $form->main_language . '</td>
						</tr>';
	
	$content .= ' 	</table>';
	
	return $content;
}


// Education Details
function neca_pdf_content_education_details($form) 
{
	$content = '<div class="heading3">EDUCATION AND PRIOR LEARNING DETAILS</div>
					<table class="tbl">
						<tr>
							<td style="width: 40%;">Are you still attending secondary school?</td>
							<td style="width: 60%;">' . $form->at_school_flag . '</td>
						</tr>';
	
	if (isset ( $form->school ) && $form->school != '') 
	{
		$content .= '	<tr>
							<td style="width: 40%;">Where did you complete secondary school?</td>
							<td style="width: 60%;">' . $form->school . '</td>
						</tr>';
	}
	
	$content .= '		<tr>
							<td style="width: 40%;">What was your highest completed school level?</td>
							<td style="width: 60%;">' . $form->highest_school_level . '</td>
						</tr>
						<tr>
							<td style="width: 40%;">Have you successfully completed any of the following qualifications?</td>
							<td style="width: 60%;">' . $form->prior_education_flag . '</td>
						</tr>
						<tr>
							<td style="width: 40%;">&nbsp;</td>
							<td style="width: 60%;">';
	
	foreach ( $form->prior_educations as $prior_education ) 
	{
		$content .= $prior_education . '<br/>';
	}
	
	$content .= '			</td>
						</tr>';
	
	if ($form->prior_education_qualification != '') 
	{
		
		$content .= '		<tr>
								<td style="width: 40%;">Is your qualification Australian?</td>
								<td style="width: 60%;">' . $form->prior_education_qualification . '</td>
							</tr>';
	}
	
	$content .= '	</table>';
	
	return $content;
}


function neca_pdf_content_usi($form) 
{
	$content = '<div class="heading3">UNIQUE STUDENT IDENTIFIER</div>
				<table class="tbl">
					<tr>
						<td style="width: 40%;">Unique Student Identifier (USI)?</td>
						<td style="width: 60%;">' . $form->usi_number . '</td>
					</tr>
				</table>';
	return $content;
}


function neca_pdf_content_vsn($form) 
{
	$content = '<div class="heading3">VICTORIAN STUDENT NUMBER</div>
					<table class="tbl">
						<tr>
							<td style="width: 40%;">Victorian Student Number (VSN):</td>
							<td style="width: 60%;">' . $form->vsn . '</td>
						</tr>
						<tr>
							<td style="width: 40%;">Previous Victorian Education: </td>
							<td style="width: 60%;">' . $form->previous_victorian_education . '</td>
						</tr>
						<tr>
							<td style="width: 40%;">Most recent Victorian School attended: </td>
							<td style="width: 60%;">' . $form->previous_victorian_school . '</td>
						</tr>';
	
	$content .= ' 	</table>';
	
	return $content;
}


function neca_pdf_content_credit_transfer($form) 
{
	$content = '<div class="heading3">CREDIT TRANSFER</div>
					<table class="tbl">';
	
	$content .= '		<tr>
							<td style="width: 40%;">I wish to apply for Credit Transfer:</td>
							<td style="width: 60%;">' . $form->credit_transfer . '</td>
						</tr>';
	
	$content .= ' 	</table>';
	
	return $content;
}


function neca_pdf_content_credit_transfer_rpl($form) 
{
	$content = '<div class="heading3">CREDIT TRANSFER/RPL</div>
					<table class="tbl">';
	
	$content .= '		<tr>
							<td style="width: 40%;">I wish to apply for Credit Transfer/RPL:</td>
							<td style="width: 60%;">' . $form->credit_transfer . '</td>
						</tr>';
	
	$content .= ' 	</table>';
	
	return $content;
}


function neca_pdf_content_recognition_of_prior_learning($form) 
{
	$content = '<div class="heading3">RECOGNITION OF PRIOR LEARNING/RPL</div>
					<table class="tbl">';
	
	$content .= '		<tr>
							<td style="width: 40%;">I wish to apply for Recognition of Prior Learning (RPL)?</td>
							<td style="width: 60%;">' . $form->credit_transfer . '</td>
						</tr>';
	
	$content .= ' 	</table>';
	
	return $content;
}


// Language, Literacy or Numeracy
function neca_pdf_content_language_literacy_numeracy($form) 
{
	$content = '<div class="heading3">LANGUAGE, LITERACY OR NUMERACY</div>
					<table class="tbl">';
	$content .= '		<tr>
							<td style="width: 40%;">I need assistance with Language, Literacy or Numeracy or other support to enable me to complete this training:</td>
							<td style="width: 60%;">' . $form->language_literacy_numeracy . '</td>
						</tr>';
	
	$content .= ' 	</table>';
	
	return $content;
}


// Disability Details
function neca_pdf_content_disability_details($form) 
{
	$content = '<div class="heading3">DISABILITY DETAILS</div>
					<table class="tbl">
						<tr>
							<td style="width: 40%;">Do you consider yourself to have a disability, impairment or long term condition?</td>
							<td style="width: 60%;">' . $form->disability_flag . '</td>
						</tr>
						<tr>
							<td style="width: 40%;">&nbsp;</td>
							<td style="width: 60%;">';
	foreach ( $form->disability_types as $disability_type ) {
		$content .= $disability_type . '<br/>';
	}
	if (isset ( $form->disability_other ) && $form->disability_other != '') {
		echo 'Other: ' . $form->disability_other;
	}
	$content .= '			</td>
						</tr>';
	$content .= ' 	</table>';
	
	return $content;
}


function neca_pdf_content_employer_details($form) 
{
	$content = '<div class="heading3">EMPLOYER DETAILS</div>
				<table class="tbl">';
	
	if ($form->employer_party_new) 
	{
		$content .= '	<tr>
						<td style="width: 25%;">New Employer (in JR): </td>
						<td style="width: 75%;">Yes</td>
					</tr>';
	}
	$content .= '	<tr>
						<td style="width: 25%;">Company: </td>
						<td style="width: 75%;">' . $form->employer_company . '</td>
					</tr>';
	
	/*
					<tr>
						<td style="width: 25%;">Address: </td>
						<td style="width: 75%;">' . $form->employer_address . '</td>
					</tr>
					<tr>
						<td style="width: 25%;">Suburb: </td>
						<td style="width: 75%;">' . $form->employer_suburb . '</td>
					</tr>
					<tr>
						<td style="width: 25%;">State: </td>
						<td style="width: 75%;">' . $form->employer_state . '</td>
					</tr>
					<tr>
						<td style="width: 25%;">Postcode: </td>
						<td style="width: 75%;">' . $form->employer_postcode . '</td>
					</tr>
					<tr>
						<td style="width: 25%;">Office Phone: </td>
						<td style="width: 75%;">' . $form->employer_office_phone . '</td>
					</tr>';
	
	if ($form->employer_supervisor_firstname != '') 
	{
		$content .= '	<tr>
							<td style="width: 25%;">Supervisor Name: </td>
							<td style="width: 75%;">' . $form->employer_supervisor_firstname . ' ' . $form->employer_supervisor_surname . '</td>
						</tr>';
	}
	
	if ($form->employer_supervisor_phone != '') 
	{
		$content .= '	<tr>
							<td style="width: 25%;">Supervisor Phone: </td>
							<td style="width: 75%;">' . $form->employer_supervisor_phone . '</td>
						</tr>';
	}
	
	if ($form->employer_supervisor_email != '') 
	{
		$content .= '	<tr>
							<td style="width: 25%;">Supervisor Email: </td>
							<td style="width: 75%;">' . $form->employer_supervisor_email . '</td>
						</tr>';
	}
	*/
	
	$content .= '</table>
				<br/>
				<table class="tbl">
					<tr>
						<td style="width: 40%;">Will your employer be paying your invoice directly?</td>
						<td style="width: 60%;">' . $form->employer_paying_invoice . '</td>
					</tr>
				</table>';
	
	return $content;
}


// Enrolment Avetmiss Details
function neca_pdf_content_enrolment_avetmiss_details($form) 
{
	$content = '<div class="heading3">ENROLMENT AVETMISS DETAILS</div>
					<table class="tbl">
						<tr>
							<td style="width: 40%;">Which best describes your main reason for undertaking the program / apprenticeship?</td>
							<td style="width: 60%;">' . $form->study_reason . '</td>
						</tr>';
	
	if ($form->industry_employment != '') {
		$content .= '		<tr>
								<td style="width: 40%;">Which classification best describes the industry of current or previous employer?</td>
								<td style="width: 60%;">' . $form->industry_employment . '</td>
							</tr>';
	}
	
	if ($form->occupation != '') {
		$content .= '		<tr>
								<td style="width: 40%;">Which role best describes your current or recent occupation?</td>
								<td style="width: 60%;">' . $form->occupation . '</td>
							</tr>';
	}
	
	$content .= ' 	</table>';
	
	return $content;
}


// Concession Details
function neca_pdf_content_concession_details($form) 
{
	$content = '<div class="heading3">CONCESSION CARD DETAILS</div>
					<table class="tbl">
						<tr>
							<td style="width: 40%;">Do you have a valid concession card?</td>
							<td style="width: 60%;">' . $form->concession_flag . '</td>
						</tr>';
	
	$content .= ' 	</table>';
	
	return $content;
}


// Would you describe yourself as belonging to any of the following cohorts?
function neca_pdf_content_cohort($form)
{
	$content = '<div class="heading3">WOULD YOU DESCRIBE YOURSELF AS BELONGING TO ANY OF THE FOLLOWING COHORTS</div>
					<table class="tbl">
						<tr>
							<td style="width: 40%;">Would you describe yourself as belonging to any of the following cohorts?</td>
							<td style="width: 60%;">';

							foreach ( $form->cohorts as $cohort ) {
								$content .= $cohort . '<br/>';
							}
	
	$content .= 			'</td>
						</tr>';
	
	$content .= ' 	</table>';
	
	return $content;
	
}


// How did you hear about us
function neca_pdf_content_how_did_you_hear($form) 
{
	$content = '<div class="heading3">HOW DID YOU HEAR ABOUT US?</div>
					<table class="tbl">
						<tr>
							<td style="width: 40%;">How did you hear about us?</td>
							<td style="width: 60%;">' . $form->how_did_you_hear . '</td>
						</tr>';
	
	$content .= ' 	</table>';
	
	return $content;
}


// Student Declaration
function neca_pdf_content_student_declaration($form) 
{
	$content = '<div class="heading3">STUDENT DECLARATION</div>';
	$content .= '<table class="tbl">';
	
	$content .= '	<tr>
						<td colspan="2">
							I (name) <br/><br/>
							in seeking to enrol in (course) <br/><br/>
							declare the following to be true and accurate statements:
						</td>
					</tr>
					<tr>
						<td colspan="2">a. I AM / I AM NOT enrolled in a school, including government, non-government, independent, Catholic or home school.</td>
					</tr>
					<tr>
						<td colspan="2">b. I AM / I AM NOT enrolled in the Commonwealth Governments Skills for Education and Employment program.</td>
					</tr>
					<tr>
						<td colspan="2">c. [&nbsp;&nbsp;&nbsp;] I understand that my enrolment in the above qualification may be subsidised by the Victorian and Commonwealth Governments under the Skills First Program. I understand how enrolling in the above qualification will affect my future training options and eligibility for further government subsidised training under the Skills First Program.</td>
					</tr>
					<tr>
						<td colspan="2">d. [&nbsp;&nbsp;&nbsp;] I acknowledge and understand that I may be contacted by the Department or an agent to participate in a student survey, interview or other questionnaire.</td>
					</tr>
					<tr>
						<td style="width: 25%;">Signed: </td>
						<td style="width: 75%;">&nbsp;<br/></td>
					</tr>
					<tr>
						<td style="width: 25%;">Date: </td>
						<td style="width: 75%;">&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
					</tr>
				</table>';
	return $content;
}


function neca_pdf_content_prerequisite_declaration($form) 
{
	$content = '<div class="heading3">PRE-REQUISITE DECLARATION</div>
				<table class="tbl">
					<tr>
						<td style="width: 5%">[' . $form->prerequisite_declaration . ']</td>
						<td style="width: 95%"><p class="small">
							I confirm I have the pre-requisites and will bring the originals or certified copies on the first day of my course</p>
						</td>
					</tr>
				</table>';
	return $content;
}


// All Students Must Read, Sign and Date
function neca_pdf_content_all_students_must_read_sign_date($form) 
{
	$content = '<div class="heading3">ALL STUDENTS MUST READ, SIGN AND DATE</div>';
	$content .= '<table class="tbl">
					<tr>
						<td style="width:5%">[' . $form->privacy_declaration . ']</td>
						<td style="width:95%">
							<p class="small">
								<strong>PRIVACY DECLARATION - </strong><br/>
								I acknowledge that I have read the National Privacy Statement & Student Enrolment Privacy Notice and NECA Education & Careers Privacy Notice and Victorian Government’s VET Student Enrolment Notice
							</p>
						</td>
					</tr>
				</table>';
	
	return $content;
}


// Last Update: 23.01.2023
function neca_pdf_content_privacy_policy_feb2021() 
{
	$content = '<div class="heading3">VET Data Privacy Notice</div>';
	$content .= '<p class="small"><span style="text-decoration: underline;"><strong>How we collect your personal information</strong></span></p><br/>
<p class="small">When you enrol as a student in a vocational education and training (VET) course, your registered training organisation (RTO) collects personal information so they can process and manage your enrolment.</p>
<p class="small">Your RTO may be required by law (under the <em>National Vocational Education and Training Regulator Act 2011</em> (Cth) (the NVETR Act)) to disclose the personal information collected about you to the National Centre for Vocational Education Research Ltd (NCVER). NCVER is the National VET Data Custodian and is responsible for collecting, managing, analysing and communicating research and statistics about the Australian VET sector.</p>
<p class="small">The NCVER is authorised by law (under the NVETR Act) to disclose your personal information to us, the Department of Employment and Workplace Relations (the department).</p>
<p class="small"><span style="text-decoration: underline;"><strong>How we handle and use your personal information</strong></span></p>
<p class="small">The department is authorised by law, including the <em>Privacy Act 1988</em> (Cth) (the Privacy Act) and the NVETR Act to collect, use and disclose your personal information to fulfil specified functions and activities, including:</p>
<ul class="small">
<li>administering VET, such as program administration, regulation, monitoring and evaluation</li>
<li>facilitating statistics and research relating to education, including surveys and data linkage</li>
<li>understanding how the VET market operates, for policy, workforce planning and consumer information</li>
</ul>
<p class="small">The department is also authorised by law (under the NVETR Act) to disclose your personal information to:</p>
<ul class="small">
<li>another Commonwealth authority</li>
<li>a person engaged by the Secretary of the department to carry out an activity on behalf of the department</li>
</ul>
<p class="small">if that authority or person satisfies any prescribed information safeguard rules for such a disclosure.</p>
<p class="small">The department may be authorised by law to share ‘Public Sector Data’ with third parties, including under the <em>Data Availability and Transparency Act 2022</em> (Cth) (DAT Act). ‘Public Sector Data’ is defined in the DAT Act to mean ‘data lawfully collected, created or held by or on behalf of a Commonwealth body…’ and therefore includes any data collected by the department as part of performing VET related functions.</p>
<p class="small">For information about the department’s broader approach to handling personal information across all the areas it administers, please see the <a href="https://www.dewr.gov.au/using-site/privacy" data-saferedirecturl="https://www.google.com/url?q=https://www.dewr.gov.au/using-site/privacy&amp;source=gmail&amp;ust=1674550469446000&amp;usg=AOvVaw0xF3o2U9TNZTsBC1a8ii90">department’s privacy policy</a>.</p>
<p class="small"><span style="text-decoration: underline;"><strong>To correct your information</strong></span><br />
If you would like to seek access to or correct your personal information, in the first instance, please contact NECA Education and Careers.</p>
<p class="small"><span style="text-decoration: underline;"><strong>To make a complaint or ask a question</strong></span></p>
<p class="small">If you think we may have breached your privacy you may make a complaint at <a href="mailto:privacy@dewr.gov.au">privacy@dewr.gov.au</a>. To ensure that we fully understand the nature of your complaint and the outcome you are seeking, we prefer that you make your complaint in writing.</p>
<p class="small">For further information about our complaint handling processes please see our <a href="https://www.dewr.gov.au/about-department/resources/dewr-privacy-complaints-handling-procedures" data-saferedirecturl="https://www.google.com/url?q=https://www.dewr.gov.au/about-department/resources/dewr-privacy-complaints-handling-procedures">Privacy Complaint Handling Procedures</a>.</p>
<p class="small">If you wish to ask a question about this VET Data Privacy Notice please email <a href="mailto:VET-DataPolicy@dewr.gov.au">VET-DataPolicy@dewr.gov.au</a>.</p>';
	
	return $content;
}


// Last updated: 13.02.2023
function neca_pdf_content_privacy_policy_feb2023()
{
	$content = '<div class="heading3">Privacy Notice</div>';
	$content .= '
        <p class="small"><span style="text-decoration: underline;"><strong>Why we collect your personal information</strong></span><br/>
        As a registered training organisation (RTO), we collect your personal information so we can process and manage your enrolment in a vocational education and training (VET) course with us.</p>
        <p class="small"><span style="text-decoration: underline;"><strong>How we use your personal information</strong></span><br/>
        We use your personal information to enable us to deliver VET courses to you, and otherwise, as needed, to comply with our obligations as an RTO.</p>
        <p class="small"><span style="text-decoration: underline;"><strong>How we disclose your personal information</strong></span><br/>
        We are required by law (under the National Vocational Education and Training Regulator Act 2011 (Cth) (NVETR Act)) to disclose the personal information we collect about you to the National VET Data Collection kept by the National Centre for Vocational Education Research Ltd (NCVER). The NCVER is responsible for collecting, managing, analysing and communicating research and statistics about the Australian VET sector.<br/>
        We are also authorised by law (under the NVETR Act) to disclose your personal information to the relevant state or territory training authority.</p>
        <p class="small"><span style="text-decoration: underline;"><strong>How NCVER and other bodies handle your personal information</strong></span><br/>
        NCVER will collect, hold, use and disclose your personal information in accordance with the law, including the Privacy Act 1988 (Cth) (Privacy Act) and the NVETR Act. Your personal information may be used and disclosed by NCVER for purposes that include populating authenticated VET transcripts; administration of VET; facilitation of statistics and research relating to education, including surveys and data linkage; and understanding the VET market.<br/>
        NCVER is authorised to disclose information to the Australian Government Department of Employment and Workplace Relations (DEWR), Commonwealth authorities, state and territory authorities (other than registered training organisations) that deal with matters relating to VET and VET regulators for the purposes of those bodies, including to enable:</p>
        <ul class="small">
            <li>administration of VET, including program administration, regulation, monitoring and evaluation</li>
            <li>facilitation of statistics and research relating to education, including surveys and data linkage</li>
            <li>understanding how the VET market operates, for policy, workforce planning and consumer information.</li>
        </ul>
        <p class="small">NCVER may also disclose personal information to persons engaged by NCVER to conduct research on NCVER\'s behalf.<br/>
        NCVER does not intend to disclose your personal information to any overseas recipients.</p>
        <p class="small">For more information about how NCVER will handle your personal information please refer to the NCVER\'s Privacy Policy at <a href="http://www.ncver.edu.au/privacy" target="_blank">www.ncver.edu.au/privacy.</a></p>
        <p class="small">If you would like to seek access to or correct your information, in the first instance, please contact your RTO using the contact details listed below.</p>
        <p class="small">DEWR is authorised by law, including the Privacy Act and the NVETR Act, to collect, use and disclose your personal information to fulfil specified functions and activities. For more information about how DEWR will handle your personal information, please refer to the DEWR VET Privacy Notice at <a href="https://www.dewr.gov.au/national-vet-data/vet-privacy-notice" target="_blank">https://www.dewr.gov.au/national-vet-data/vet-privacy-notice</a>.</p>
        <p class="small"><span style="text-decoration: underline;"><strong>Surveys</strong></span><br/>
        You may receive a student survey which may be run by a government department or an NCVER employee, agent, third-party contractor or another authorised agency. Please note you may opt out of the survey at the time of being contacted.</p>
        <p class="small"><span style="text-decoration: underline;"><strong>Contact information</strong></span><br/>
        At any time, you may contact NECA Education and Careers to:</p>
        <ul class="small">
            <li>request access to your personal information</li>
            <li>correct your personal information</li>
            <li>make a complaint about how your personal information has been handled</li>
            <li>ask a question about this Privacy Notice</li>
        </ul>
        <p class="small"><a href="https://necaeducation.com.au/footer-menu/privacy-policy/" target="_blank">https://necaeducation.com.au/footer-menu/privacy-policy/</a></p>
        <p class="small">If you have any questions, concerns or complaints about the Privacy Policy or our handling of your personal information, please contact:</p>
        <p class="small">Natalie Green<br/>
            Ph: 03 9381 1922<br/>
            Email: <a href="mailto:privacy@necaeducation.com.au" target="_blank">privacy@necaeducation.com.au</a><br/>
            PO Box 187 North Carlton VIC 3054
        </p>
        <p class="small">If we receive a privacy complaint it will be treated seriously and dealt with promptly, in a confidential manner, and in accordance with NECA Education & Careers internal complaints handling procedures.</p>';

	return $content;
}


// All Students Must Read, Sign and Date
function neca_pdf_content_all_students_must_read_sign_date_2019($form) 
{
	$content = '<div class="heading3">ALL STUDENTS MUST READ, SIGN AND DATE</div>';
	$content .= '<table class="tbl">
					<tr>
						<td style="width:5%">[' . $form->privacy_declaration . ']</td>
						<td style="width:95%">
							<p class="small">
								<strong>PRIVACY DECLARATION - </strong>The information being sought in this form is collected for the purposes of processing your enrolment application. The information will be held by <strong>NECA Education & Careers</strong> and may be accessed and used by people employed or engaged by <strong>NECA Education & Careers</strong> in the delivery of services to you. The information may be used or disclosed to organizations outside <strong>NECA Education & Careers</strong> where permitted by relevant Privacy Legislation. The provision of the information is voluntary, however if this information is not provided <strong>NECA Education & Careers</strong> may be unable to process your enrolment application. You have a right of access to, and correction of, your personal information in accordance with the Privacy Legislation and <strong>NECA Education & Careers\'</strong> Privacy Policy. Please direct any enquiries you may have in relation to this matter to <strong>NECA Education & Careers</strong> Privacy Office.
							</p>
						</td>
					</tr>
				</table>';
	
	return $content;
}


function neca_pdf_content_privacy_notice_and_student_declaration() 
{
	$content = '<table class="tbl">
					<tr>
						<td style="width: 5%;">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width: 95%;">
							<p class="small">I have read and understand the Privacy Notice and Student Declaration</p>
						</td>
					</tr>
				</table>';
	
	return $content;
}


function neca_pdf_content_enrolment_declaration() 
{
	$content = '<table class="tbl">
					<tr>
						<td style="width: 5%;">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width: 95%;">
							<p class="small"><strong>ENROLMENT DECLARATION - </strong>I declare that the information I have provided on this enrolment form is true and complete and I can produce documents to verify this if required. I hereby agree to abide by the Code of Conduct and the regulations of NECA Educations and Careers. I understand that if any of this information is found to be incorrect or untrue it may result in the terms and conditions of my enrolment being null and void.</p>
						</td>
					</tr>
				</table>';
	
	return $content;
}


function neca_pdf_content_tickboxes($tick_all = false) 
{
	$content = '<table class="tbl">
					<tr>
						<td style="width: 5%;">';
	$content .= $tick_all ? '[YES]' : '[&nbsp;&nbsp;&nbsp;]';
	
	$content .= '		</td>
						<td style="width: 95%;">
							<p class="small"><strong>DOCUMENTS RECEIVED - </strong>I declare that I have received and been briefed on the Student
							Manual and understand my requirements outlined within the document</p>
						</td>
					</tr>
					<tr>
						<td style="width: 5%;">';
	
	$content .= $tick_all ? '[YES]' : '[&nbsp;&nbsp;&nbsp;]';
	
	$content .= '		</td>
						<td style="width: 95%;">
							<p class="small">The Fees & Payment Policy Guide was explained and provided to me prior to the confirmation of my enrolment</p>
						</td>
					</tr>
					<tr>
						<td style="width: 5%;">';
	
	$content .= $tick_all ? '[YES]' : '[&nbsp;&nbsp;&nbsp;]';
	
	$content .= '		</td>
						<td style="width: 95%;">
							<p class="small">Do you agree to receive communications from NECA Education & Careers throughout the year?</p>
						</td>
					</tr>
					<tr>
						<td style="width: 5%;">';
	
	$content .= $tick_all ? '[YES]' : '[&nbsp;&nbsp;&nbsp;]';
	
	$content .= '		</td>
						<td style="width: 95%;"><p class="small">I have read and understand the USI Privacy Statement & NECA Education & Careers Privacy Statement in the Student Manual & Policy Guide</p></td>
					</tr>
			</table>';
	
	return $content;
}


function neca_pdf_content_tickboxes_2019($tick_all = false, $show_pre_training = false) 
{
	$content = '<table class="tbl">
					<tr>
						<td style="width: 5%;">';
	$content .= $tick_all ? '[YES]' : '[&nbsp;&nbsp;&nbsp;]';
	
	$content .= '		</td>
						<td style="width: 95%;">
							<p class="small">I have read and understand the Privacy Policy and Student Declaration</p>
						</td>
					</tr>
					<tr>
						<td style="width: 5%;">';
	
	$content .= $tick_all ? '[YES]' : '[&nbsp;&nbsp;&nbsp;]';
	
	$content .= '		</td>
						<td style="width: 95%;">
							<p class="small">I have read and understand the Victorian Enrolment Privacy Notice</p>
						</td>
					</tr>';
	
	
	if($show_pre_training)
	{
		$content .= '	<tr>
							<td style="width: 5%;">';
		
		$content .= $tick_all ? '[YES]' : '[&nbsp;&nbsp;&nbsp;]';
		
		$content .= '		</td>
							<td style="width: 95%;">
								<p class="small">I have attended a Pre-Training Review session</p>
							</td>
						</tr>';
	}
	
	$content .= '	<tr>
						<td style="width: 5%;">';
	
	$content .= $tick_all ? '[YES]' : '[&nbsp;&nbsp;&nbsp;]';
	
	$content .= '		</td>
						<td style="width: 95%;">
							<p class="small"><strong>ENROLMENT DECLARATION - </strong>I declare that the information I have provided on this enrolment form is true and complete and I can produce documents to verify this if required. I hereby agree to abide by the Code of Conduct and the regulations of <strong>NECA Education and Careers</strong>. I understand that if any of this information is found to be incorrect or untrue it may result in the terms and conditions of my enrolment being null and void.</p>
						</td>
					</tr>
					<tr>
						<td style="width: 5%;">';
	
	$content .= $tick_all ? '[YES]' : '[&nbsp;&nbsp;&nbsp;]';
	
	$content .= '		</td>
						<td style="width: 95%;">
							<p class="small"><strong>DOCUMENTS RECEIVED - </strong>I declare that I have received and been briefed on the Student handbook and understand my requirements outlined within the document</p>
						</td>
					</tr>
					<tr>
						<td style="width: 5%;">';
	
	$content .= $tick_all ? '[YES]' : '[&nbsp;&nbsp;&nbsp;]';
	
	$content .= '		</td>
						<td style="width: 95%;">
							<p class="small">The <strong>Statement of Fees</strong> was explained and provided to me prior to the confirmation of my enrolment</p>
						</td>
					</tr>
					<tr>
						<td style="width: 5%;">';
	
	$content .= $tick_all ? '[YES]' : '[&nbsp;&nbsp;&nbsp;]';
	
	$content .= '		</td>
						<td style="width: 95%;">
							<p class="small">Do you agree to receive communications from <strong>NECA Education & Careers</strong> throughout the year?</p>
						</td>
					</tr>
					<tr>
						<td style="width: 5%;">';
	
	$content .= $tick_all ? '[YES]' : '[&nbsp;&nbsp;&nbsp;]';
	
	$content .= '		</td>
						<td style="width: 95%;"><p class="small">I have read and understand the USI Privacy Statement & NECA Education & Careers Privacy Statement in the Student Manual & Policy Guide</p></td>
					</tr>
			</table>';
	
	return $content;
}


function neca_pdf_content_signatures($form, $tick_all = false) 
{
	$content = '<div class="heading3">STUDENT DECLARATION AND CONSENT</div>';
	$content .= '<table class="tbl">
					<tr>
						<td style="width: 5%;">';
	
	$content .= $tick_all ? '[YES]' : '[&nbsp;&nbsp;&nbsp;]';
	
	$content .= '		</td>
						<td style="width: 95%;">
							<p class="small"><strong>ENROLMENT DECLARATION - </strong>I declare that the information I have provided on this enrolment form is true and
							complete and I can produce documents to verify this if required. I hereby agree to abide by the Code of Conduct and the
							regulations of NECA Educations and Careers. I understand that if any of this information is found to be incorrect or untrue
							it may result in the terms and conditions of my enrolment being null and void.</p>
						</td>
					</tr>
				</table>';
	
	$content .= '<table class="tbl">
					<tr>
						<td style="width: 25%;">Student Name: </td>
						<td style="width: 75%;">' . $form->first_name . ' ' . $form->surname . '<br/></td>
					</tr>
					<tr>
						<td style="width: 25%;">Signature: </td>
						<td style="width: 75%;"><strong><em>' . $form->signature . '</em></strong><br/></td>
					</tr>
					<tr>
						<td style="width: 25%;">Date: </td>
						<td style="width: 75%;">' . current_time ( "d/m/Y" ) . '</td>
					</tr>
					<tr>
						<td style="width: 25%;">Parent / Guardian Name: </td>
						<td style="width: 75%;">&nbsp;<br/></td>
					</tr>
					<tr>
						<td style="width: 25%;">Signature: </td>
						<td style="width: 75%;">&nbsp;<br/></td>
					</tr>
					<tr>
						<td style="width: 25%;">Date: </td>
						<td style="width: 75%;">&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2"><p class="small">Parental/guardian consent is required for all students under the age of 18</p></td>
					</tr>
				</table>';
	
	return $content;
}


function neca_pdf_content_signatures_2019($form) 
{
	$content = '<table class="tbl">
					<tr>
						<td style="width: 25%;">Name: </td>
						<td style="width: 75%;">' . $form->first_name . ' ' . $form->surname . '<br/></td>
					</tr>
					<tr>
						<td style="width: 25%;">Signature: </td>
						<td style="width: 75%;"><strong><em>' . $form->signature . '</em></strong><br/></td>
					</tr>
					<tr>
						<td style="width: 25%;">Date: </td>
						<td style="width: 75%;">' . current_time ( "d/m/Y" ) . '</td>
					</tr>
					<tr>
						<td style="width: 25%;">Parental Signature: </td>
						<td style="width: 75%;">&nbsp;<br/></td>
					</tr>
					<tr>
						<td colspan="2"><p class="small">Parental/guardian consent is required for all students under the age of 18</p></td>
					</tr>
					<tr>
						<td style="width: 25%;">Date: </td>
						<td style="width: 75%;">&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
					</tr>
				</table>';
	
	return $content;
}


function neca_pdf_content_neca_privacy_notice_2022()
{
	$content = '<div class="heading3">PRIVACY STATEMENT</div>';
	$content .= '<p class="small"><span style="text-decoration: underline;"><strong>Why we collect your personal information.<br />
	</strong></span>As a registered training organisation (RTO), we collect your personal information so we can process and manage your enrolment in a vocational education and training (VET) course with us.</p>
	<p class="small"><span style="text-decoration: underline;"><strong>How we use your personal information</strong></span><br />
	We use your personal information to enable us to deliver VET courses to you, and otherwise, as needed, to comply with our obligations as an RTO.</p>
	<p class="small"><span style="text-decoration: underline;"><strong>How we disclose your personal information</strong></span><br />
	We are required by law (under the National Vocational Education and Training Regulator Act 2011 (Cth) (NVETR Act)) to disclose the personal information we collect about you to the National VET Data Collection kept by the National Centre for Vocational Education Research Ltd (NCVER). The NCVER is responsible for collecting, managing, analysing and communicating research and statistics about the Australian VET sector.</p>
	<p class="small">We are also authorised by law (under the NVETR Act) to disclose your personal information to the relevant state or territory training authority.</p>
	<p class="small"><span style="text-decoration: underline;"><strong>How the NCVER and other bodies handle your personal information.</strong></span><br />
	The NCVER will collect, hold, use and disclose your personal information in accordance with the law, including the Privacy Act 1988 (Cth) (Privacy Act) and the NVETR Act. Your personal information may be used and disclosed by NCVER for purposes that include populating authenticated VET transcripts; administration of VET; facilitation of statistics and research relating to education, including surveys and data linkage; and understanding the VET market.</p>
	<p class="small">The NCVER is authorised to disclose information to the Australian Government Department of Education, Skills and Employment (DESE), Commonwealth authorities, State and Territory authorities (other than registered training organisations) that deal with matters relating to VET and VET regulators for the purposes of those bodies, including to enable:<br/>
	<ul>
		<li>administration of VET, including program administration, regulation, monitoring and evaluation</li>
		<li>facilitation of statistics and research relating to education, including surveys and data linkage</li>
		<li>understanding how the VET market operates, for policy, workforce planning and consumer information.</li>
	</ul>
	</p>
	<p class="small">The NCVER may also disclose personal information to persons engaged by NCVER to conduct research on NCVER’s behalf. The NCVER does not intend to disclose your personal information to any overseas recipients.</p>
	<p class="small">For more information about how the NCVER will handle your personal information please refer to the NCVER’s Privacy Policy at <a href="https://www.ncver.edu.au/privacy" target="_blank" rel="noopener">www.ncver.edu.au/privacy</a>.</p>
	<p class="small">If you would like to seek access to or correct your information, in the first instance, please contact your RTO using the contact details listed below.</p>
	<p class="small">DESE is authorised by law, including the Privacy Act and the NVETR Act, to collect, use and disclose your personal information to fulfil specified functions and activities. For more information about how the DESE will handle your personal information, please refer to the DESE VET Privacy Notice at <a href="https://www.dese.gov.au/national-vet-data/vet-privacy-notice" target="_blank" rel="noopener">https://www.dese.gov.au/national-vet-data/vet-privacy-notice</a>.</p>
	<p class="small"><span style="text-decoration: underline;"><strong>Surveys</strong></span><br />
	You may receive a student survey which may be run by a government department or an NCVER employee, agent, third-party contractor or another authorised agency. Please note you may opt out of the survey at the time of being contacted.</p>
	<p class="small"><span style="text-decoration: underline;"><strong>Contact information</strong></span><br />
	At any time, you may contact NECA Education &amp; Careers to:<br/>
	<ul>
		<li>request access to your personal information</li>
		<li>correct your personal information</li>
		<li>make a complaint about how your personal information has been handled</li>
		<li>ask a question about this Privacy Notice</li>
	</ul>
	</p>';
	
	return $content;
}


function neca_pdf_content_neca_privacy_notice_2019() 
{
	$content = '<div class="heading3">NECA EDUCATION & CAREERS PRIVACY STATEMENT</div>';
	$content .= '<p class="small">
					<strong>Privacy Notice</strong><br/><br/>
					Under the Data Provision Requirements 2012, NECA Education and Careers is required to collect personal information about you and to disclose that personal information to the National Centre for Vocational Education Research Ltd (NCVER).<br/><br/>
					Your personal information (including the personal information contained on this enrolment form and your training activity data) may be used or disclosed by NECA Education and Careers for statistical, regulatory and research purposes. NECA Education and Careers may disclose your personal information for these purposes to:<br/><br/>
					<ul>
						<li>Commonwealth and State or Territory government departments and authorised agencies; and</li>
						<li>NCVER;</li>
					</ul>
					Personal information that has been disclosed to NCVER may be used or disclosed by NCVER for the following purposes:<br/>
					<ul>
						<li>populating authenticated VET transcripts;</li>
						<li>facilitating statistics and research relating to education, including surveys and data linkage;</li>
						<li>pre-populating RTO student enrolment forms;</li>
						<li>understanding how the VET market operates, for policy, workforce planning and consumer information; and</li>
						<li>administering VET, including program administration, regulation, monitoring and evaluation.</li>
					</ul>
					You may receive a student survey which may be administered by a government department or NCVER employee, agent or third party contractor or other authorised agencies. Please note you may opt out of the survey at the time of being contacted.<br/><br/>
					NCVER will collect, hold, use and disclose your personal information in accordance with the Privacy Act 1988 (Cth), the National VET Data Policy and all NCVER policies and protocols (including those published on NCVER\'s website at <a href="http://www.ncver.edu.au" target="_blank">www.ncver.edu.au</a>).<br/><br/>
					For more information about NCVER\'s Privacy Policy go to <a href="https://www.ncver.edu.au/privacy" target="_blank">https://www.ncver.edu.au/privacy</a>.<br/><br/>
					<strong>Student Declaration and Consent</strong><br/><br/>
					I declare that the information I have provided to the est of my knowledge is true and correct.
					I consent to the collection, use and disclosure of my personal information in accordance with the Privacy Notice above.
				</p>';
	
	return $content;
}


function neca_pdf_content_neca_privacy_notice() 
{
	$content = '<div class="heading3">PRIVACY NOTICE AND STUDENT DECLARATION</div>';
	$content .= '<p class="small">
					The information being sought in this form is collected for the purposes of processing your enrolment application. The information will be held by NECA Education &Careers and may be accessed and used by people employed or engaged by NECA Education & Careers in the delivery of services to you. The information may be used
					or disclosed to organisations outside NECA Education & Careers where permitted by relevant Privacy Legislation. The provision of the information is voluntary, however if
					this information is not provided NECA Education & Careers may be unable to process your enrolment application. You have a right of access to, and correction of, your
					personal information in accordance with the Privacy Legislation and NECA Education & Careers\' Privacy Policy. Please direct any enquiries you may have in relation to
					this matter to NECA Education & Careers Privacy Officer.
				</p> ';
	
	return $content;
}


function neca_pdf_content_student_enrolment_privacy_notice() 
{
	$content = '<div class="heading3">NATIONAL ENROLMENT PRIVACY NOTICE</div>';
	$content .= '<p class="small">
					Under the Data Provision Requirements 2012, NECA Education & Careers is required to collect personal information about you and to disclose that personal information to the National Centre for Vocational Education Research Ltd (NCVER).<br/><br/>
					Your personal information (including the personal information contained on this enrolment form), may be used or disclosed by NECA Education & Careers for statistical, administrative, regulatory and research purposes. NECA Education & Careers may disclose your personal information for these purposes to:<br/><br/>
					<ul>
						<li>Commonwealth and State or Territory government departments and authorised agencies; and</li>
						<li>NCVER;</li>
					</ul>
			
					Personal information that has been disclosed to NCVER may be used or disclosed by NCVER for the following purposes:<br/>
					<ul>
						<li>populating authenticated VET transcripts;</li>
						<li>facilitating statistics and research relating to education, including surveys and data linkage;</li>
						<li>pre-populating RTO student enrolment forms;</li>
						<li>understanding how the VET market operates, for policy, workforce planning and consumer information; and</li>
						<li>administering VET, including program administration, regulation, monitoring and evaluation.</li>
					</ul>
					You may receive a student survey which may be administered by a government department or NCVER employee, agent or third party contractor or other authorised agencies. Please note you may opt out of the survey at the time of being contacted.<br/><br/>
					NCVER will collect, hold, use and disclose your personal information in accordance with the Privacy Act 1988 (Cth), the National VET Data Policy and all NCVER policies and protocols (including those published on NCVER\'s website at <a href="http://www.ncver.edu.au" target="_blank">www.ncver.edu.au</a>).<br/><br/>
					For more information about NCVER\'s Privacy Policy go to <a href="https://www.ncver.edu.au/privacy" target="_blank">https://www.ncver.edu.au/privacy</a>.
				</p> ';
	
	return $content;
}


// Last Update: 13.02.2023
function neca_pdf_content_victorian_enrolment_privacy_notice() 
{
	$content = '<div class="heading3">Victorian Government VET Student Enrolment Privacy Notice</div>';
	$content .= '
	<p class="small">The Victorian Government, through the Department of Education and Training (the Department), develops, monitors and funds vocational education and training (VET) in Victoria. The Victorian Government is committed to ensuring that Victorians have access to appropriate and relevant VET services. Any personal information collected by the Department for VET purposes is protected in accordance with the <i>Privacy and Data Protection Act 2014 (Vic)</i> and the <i>Health Records Act 2001</i> (Vic).</p>
	<p class="small"><strong>Collection of your data</strong><br/>
	NECA Education and Careers is required to provide the Department with student and training activity data. This includes personal information collected in the NECA Education and Careers enrolment form and unique identifiers such as the Victorian Student Number (VSN) and the Commonwealth\'s Unique Student Identifier (USI). NECA Education and Careers provides data to the Department in accordance with the Victorian VET Student Statistical Collection Guidelines, available at: <a href="http://www.education.vic.gov.au/training/providers/rto/Pages/datacollection.aspx" target="_blank" rel="noopener">DET website</a></p>
	<p class="small"><strong>Use of your data</strong><br/>
	The Department uses student and training data, including personal information, for a range of VET purposes including administration, monitoring and planning, including interaction between the Department and Student where appropriate.<br/>
	The data may also be subjected to data analytics, which seek to determine the likelihood of certain events occurring (such as program or subject completion), which may be relevant to the services provided to the student.</p>
	<p class="small"><strong>Disclosure of your data</strong><br/>
	As necessary and where lawful, the Department may disclose VET data, including personal information, to its contractors, other government agencies, professional bodies and/or other organisations for VET-related purposes. In particular, this includes disclosure of VET student and training data to the Commonwealth and the National Centre for Vocational Education Research (NCVER).</p>
	<p class="small"><strong>Legal and Regulatory</strong><br/>
	The Department\'s collection and handling of enrolment data and VSNs is authorised under the Education and Training Reform Act 2006 (Vic). The Department is also authorised to collect and handle USIs in accordance with the Student Identifiers Act 2014 (Cth) and the Student Identifiers Regulation 2014 (Cth).</p>
	<p class="small"><strong>Survey participation</strong><br/>
	You may be contacted to participate in a survey conducted by NCVER or a Department- endorsed project, audit or review relating to your training. This provides valuable feedback on the delivery of VET programs in Victoria.</p>
	<p class="small"><strong>Consequences of not providing your information</strong><br/>
	Failure to provide your personal information may mean that it is not possible for you to enrol in VET and/or to obtain a Victorian Government VET subsidy</p>
	<p class="small"><strong>Access, correction and complaints</strong><br/>
	You have the right to seek access to or correction of your own personal information. You may also complain if you believe your privacy has been breached. For further information please contact NECA Education and Careers in the first instance by phone 03 9381 1922 or email: info@necaeducation.com.au</p>
	<p class="small"><strong>Further information</strong><br/>
	For further information about the way the Department collects and handles personal information, including access, correction and complaints, go to <a href="http://www.education.vic.gov.au/Pages/privacypolicy.aspx" target="_blank" rel="noopener">Victorian State Government Education and Training</a> website.<br/>
	For further information about Unique Student Identifiers, including access, correction and complaints, go to <a href="https://www.usi.gov.au/documents/privacy-notice" target="_blank" rel="noopener">Australian Government USI</a> website.</p>
	<p class="small"><strong>Other</strong></p>
	<p class="small"><em>Legal and Regulatory</em><br/>
	As a Registered Training Organisation NECA Education and Careers is governed by ASQA (Australian Skills Quality Authority) and is required to provide the VET (Vocational Education and Training) regulator and other Commonwealth and or state and territory regulatory departments with regards to students and training activity data. This includes personal information collected in the NECA Education and Careers on the enrolment form for AVETMISS Data reporting to the National Centre for Vocational Education Research (NCVER) and Unique Student Identifier (USI) to be collected and handled in accordance with the Student Identifiers Act 2014 (Cth) and the Student Identifiers Regulation 2014 (Cth) as per the Commonwealth and Federal legislation.</p>
	<p class="small"><em>Collection, Use and disclosure of your data</em><br/>
	The use of your data will be in accordance with the Privacy Act 1988 which includes thirteen Australian Privacy Principles (APPs) as applicable, The data collected, stored, used and disclosed will be for a range of VET related purposes which include but are not limited to administration, monitoring, audit, education related, research purpose and to meet our reporting and legislative requirements to all the above mentioned regulatory bodies (ASQA, NCVER, USI).</p>
	<p class="small"><em>Consequences of not providing your information</em><br/>
	Failure to provide USI and related information for USI verification purposes can prevent us from issuing you with a nationally recognised VET qualification or statement of attainment when you complete your course. Failure to provide us with USI search permission will prevent us from locating your USI which needs to be verified.</p>';

	return $content;
}


function neca_pdf_content_sign_off($accredited_short_course = false) 
{
	$content = '<div class="heading3">SIGN OFF</div>';
	
	$content .= '<table class="tbl">
					<tr>
						<td colspan="2">
							<p class="small">Sign and date to confirm the following:</p>
						</td>
					</tr>';
	
	// Only show if apprentice application
	if (! $accredited_short_course) {
		$content .= '	<tr>
							<td style="width: 5%;" class="small">[&nbsp;&nbsp;&nbsp;]</td>
							<td style="width: 95%;" class="small">Explained access to government subsidy</td>
						</tr>';
	}
	
	$content .= '	<tr>
						<td style="width: 5%;" class="small">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width: 95%;" class="small">Provided the student with a copy of the student manual and policy guide</td>
					</tr>
					<tr>
						<td style="width: 5%;" class="small">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width: 95%;" class="small">Explained the privacy policy and reporting</td>
					</tr>';
	
	// Only show if apprentice application
	if (! $accredited_short_course) {
		$content .= '	<tr>
						<td style="width: 5%;" class="small">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width: 95%;" class="small">Completed and signed the current evidence of student eligibility and student declaration form in line with the current guidelines about determining student eligibility and supporting evidence</td>
					</tr>
					<tr>
						<td style="width: 5%;" class="small">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width: 95%;" class="small">The Guide to Fees was explained and provided to prior to the confirmation of my enrolment</td>
					</tr>
					<tr>
						<td style="width: 5%;" class="small">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width: 95%;" class="small">If applicable, has received a letter from the school releasing any student under the age of 17 and hasn\'t completed year 10</td>
					</tr>';
	}
	
	// Only show if accredited
	if ($accredited_short_course) {
		$content .= '	<tr>
						<td style="width: 5%;" class="small">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width: 95%;" class="small">A copy of prerequisites (if applicable) has been retained</td>
					</tr>';
	}
	
	$content .= '	<tr>
						<td style="width: 5%;" class="small">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width: 95%;" class="small"><strong>VET DATA USE STATEMENT AND RTO DECLARATION AND UNDERSTANDING</strong></td>
					</tr>
				</table>';
	
	$content .= '<p class="small">
					Under the National Vocational Education and Training Regulator (Data Provision Requirements) Instrument 2020 and National VET Data Policy (which includes the National VET Provider Collection Data Requirements Policy at Part B), Registered Training Organisations are required to collect and submit data compliant with AVETMISS for the National VET Provider Collection for all Nationally Recognised Training. This data is held by the National Centre for Vocational Education Research Ltd (NCVER), and may be used and disclosed for purposes that include:<br/>
					<ul>
						<li>populating authenticated VET transcripts</li>
						<li>administering VET, including program administration, regulation, monitoring and evaluation</li>
						<li>facilitating statistics and research relating to education, including surveys and data linkage</li>
						<li>understanding how the VET market operates, for policy, workforce planning and consumer information.</li>
					</ul><br/><br/>
					NCVER is authorised by the National Vocational Education and Training Regulator Act 2011 (NVETR Act) to disclose to the following bodies, personal information collected in accordance with the Data Provision Requirements or any equivalent requirements in a non-referring State (Victoria or Western Australia), for the purposes of that body:<br/>
					<ul>
						<li>a VET regulator (the Australian Skills, Quality Authority, the Victorian Registration and Qualifications Authority or the Training Accreditation Council Western Australia)</li>
						<li>the Australian Government Department of Education, Skills and Employment</li>
						<li>another Commonwealth authority</li>
						<li>a state or territory authority (other than a registered training organisation) that deals with or has responsibility for matters relating to VET.</li>
					</ul><br/><br/>
					NCVER may also disclose personal information to persons engaged by NCVER to conduct research on NCVER’s behalf.<br/><br/>
					<strong>RTO Declaration and Understanding</strong><br/>
					I declare that the information provided in this data submission is accurate and complete.<br/>
					I understand that information provided in this data submission about client training and outcomes may appear on authenticated VET transcripts.<br/>
					I understand that:<br/>
					<ul>
						<li>information provided in this data submission will only be used, accessed, published and disseminated according to the <a href="https://docs.education.gov.au/node/46116" target="_blank">National VET Data Policy</a></li>
						<li>if that information also includes personal information, the <a href="https://www.oaic.gov.au/privacy-law/privacy-act" target="_blank">Privacy Act 1988</a>, the Australian Privacy Principles and the National Vocational Education and Training Regulator Act 2011 regulate the collection, use or disclosure of personal information.</li>
					</ul><br/>
					I understand that:<br/>
					<ul>
						<li>information provided in this data submission may be used for the purposes outlined above, and</li>
						<li>identified RTO level information that supports consumer information (on My Skills for example), transparency and understanding of the national VET market may be published in reports, tables and a range of other data products, including data cubes and websites.</li>
					</ul>
				</p>';
	
	$content .= '<table class="tbl">
					<tr>
						<td style="width: 25%;">Name: </td>
						<td style="width: 75%;">&nbsp;<br/></td>
					</tr>
					<tr>
						<td style="width: 25%;">Position: </td>
						<td style="width: 75%;">&nbsp;<br/></td>
					</tr>
					<tr>
						<td style="width: 25%;">Signature: </td>
						<td style="width: 75%;">&nbsp;<br/></td>
					</tr>
					<tr>
						<td style="width: 25%;">Date: </td>
						<td style="width: 75%;">&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
					</tr>
				</table>';
	
	return $content;
}


// Skills First Application Form PDF
function skills_first_pdf($form, $course_name = '', $show_job_trainer = false)
{
	// Start
	$content = neca_pdf_content_start ();
	
	// Add Heading
	$content .= neca_pdf_content_skills_first_evidence_of_student_eligibility ( $form, $course_name );
	
	// Page End
	$content .= neca_pdf_page_end ();
	
	// Page Start
	$content .= neca_pdf_page_start ();
	
	// Personal Details Content
	$content .= neca_pdf_content_skills_first_education_history ( $form );
	
	// Job Trainer Enrolment Only
	if($show_job_trainer)
	{
		$content .= neca_pdf_content_skills_first_job_trainer($form);
	}
	
	// Personal Details Content
	$content .= neca_pdf_content_skills_first_student_declaration ( $form );

	// End
	$content .= neca_pdf_page_end ();
	
	// Page Start
	$content .= neca_pdf_page_start ();
	
	
	// Personal Details Content
	$content .= neca_pdf_content_skills_first_authorised_delegate ( $form );
	
	// End
	$content .= neca_pdf_page_end ();
	
	// Setup Filename
	$filename = urlencode ( 'Skills_First_Program_' . $form->first_name . '_' . $form->surname . '_' . current_time ( 'Ymd_hms' ) ) . '.pdf';
	
	// Create PDF
	neca_create_pdf ( $filename, $content );
	
	return $filename;
}


function neca_pdf_content_skills_first_evidence_of_student_eligibility($form, $course_name = 'Apprentice Application Form')
{
	$student_fullname = $form->first_name;
	if (trim ( $form->middle_name ) != '') {
		$student_fullname .= ' ' . $form->middle_name;
	}
	$student_fullname .= ' ' . $form->surname;
	
	$content = '<h4>' . $course_name . '</h4>
				<h4>SKILLS FIRST PROGRAM</h4>
				<h4>EVIDENCE OF STUDENT ELIGIBILITY AND STUDENT DECLARATION</h4>
				<p><i>Section A - To be completed by an authorised delegate of the Training Provider</i></p>
				<p><strong>Evidence of citizenship/residency and age</strong></p>
				<p>I confirm that for ' . $student_fullname . '</p>
				<table class="tbl">
					<tr>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:45%">An Australian Birth Certificate (not Birth Extract)</td>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:45%">A current Australian Passport</td>
					</tr>
					<tr>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:45%">Australian Citizenship certificate</td>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:45%">Australian Certificate of Registration by Descent</td>
					</tr>
					<tr>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:45%">Current <u><em>green</em></u> Medicare Card</td>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:45%">Current New Zealand Passport</td>
					</tr>
					<tr>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:45%">New Zealand Birth Certificate</td>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:45%">New Zealand Citizenship Certificate</td>
					</tr>
					<tr>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:45%">A proxy declaration for individuals in exceptional circumstances as per Clauses 2.11 – 2.15 of the Guidelines About Eligibility (the Eligibility Guidelines)</td>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:45%">Formal confirmation of permanent residence granted by the Department of Home Affairs (or its successor) AND the student\'s foreign passport or ImmiCard</td>
					</tr>
					<tr>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:45%">A Referral to Government Subsidised Training - Asylum Seeker\'s form from Asylum Seeker Resource Centre or the Australian Red Cross</td>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:45%">Confirmation obtained from the Visa Entitlement Verification Online System (VEVO) that the student holds a valid Bridging Visa Class E, Safe Haven Enterprise Visa, Temporary Protection Visa, Bridging Visa Class F, or Humanitarian Stay (Temporary) (subclass 449) visa or Temporary (Humanitarian Concern) (subclass 786) visa.</td>
					</tr>
				 </table>
				 <p>By <strong>EITHER:</strong></p>
				 <table class="tbl">
					<tr>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:95%">viewing an original; <em>or</em></td>
					</tr>
					<tr>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:95%">viewing a certified copy; <em>or</em></td>
					</tr>
					<tr>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:95%">verifying through the Document Verification Service (DVS) [where it is possible to do so, and in accordance with Clause 2.5(c) of the Eligibility Guidelines]; <em>or</em></td>
					</tr>
					<tr>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:95%">viewing a digital green Medicare card on a Digital Wallet app on the card holder\'s mobile device [in accordance with Clause 2.5(d) of the Eligibility Guidelines]; <em>or</em></td>
					</tr>
					<tr>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:95%">relying on evidence sighted and retained as part of a previous enrolment [in accordance with Clause 2.8 of the Eligibility Guidelines]</td>
					</tr>
					<tr>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:95%">viewing a printed or electronic record from VEVO that confirms a student holds valid Bridging Visa Class E, Safe Haven Enterprise Visa, Temporary Protection Visa, Bridging Visa Class F, or Humanitarian Stay (Temporary) (subclass 449) visa or Temporary (Humanitarian Concern) (subclass 786) visa.</td>
					</tr>
				 </table>
				 <p><strong>AND</strong> I have <strong>retained</strong> ONE of the following:</p>
				 <table class="tbl">
					<tr>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:95%">a copy of the original or certified copy, <em><u>OR</u></em></td>
					</tr>
					<tr>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:95%">the certified copy, <em><u>OR</u></em></td>
					</tr>
					<tr>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:95%">evidence as set out in Clause 2.5(c) of the Eligibility Guidelines [where verified through the DVS]; <em>OR</em></td>
					</tr>
					<tr>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:95%">declaration of sighting a digital green Medicare card [as set out in Clause 2.5(d) of the Eligibility Guidelines]; <em>OR</em></td>
					</tr>
					<tr>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:95%">a printed or electronic copy of a record from VEVO that confirms the student holds a valid Bridging Visa Class E, Safe Haven Enterprise Visa, Temporary Protection Visa, Bridging Visa Class F, or Humanitarian Stay (Temporary) (subclass 449) visa or Temporary (Humanitarian Concern) (subclass 786) visa.</td>
					</tr>
				 </table>
				 <hr/>
				 <p><strong>AND</strong> if the student\'s age is relevant to their eligibility, and ONLY IF the evidence of citizenship/residency does not show a date of birth, I have also sighted and retained a copy of one of the following:</p>
				<table class="tbl">
					<tr>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:27%">a current drivers licence</td>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:27%">a \'Keypass\' card</td>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:27%">current foreign passport</td>
					</tr>
					<tr>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:27%">a current learner permit</td>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:27%">a Proof of Age card</td>
						<td style="width:5%">[&nbsp;&nbsp;&nbsp;]</td>
						<td style="width:27%">Not applicable</td>
					</tr>
				 </table> ';
	
	return $content;
}


function neca_pdf_content_skills_first_education_history($form)
{
	$content = '<p><em><strong>Section B - To be completed by the student</strong></em></p>
				<p> - A <strong>\'skill set\'</strong> means a course with the title \'Course in...\' or a single subject, or a small group of subjects (for example \'Course in Family Violence\', \'Infection control Skill Set (Retail)\'
				<br/>- A <strong>\'qualification\'</strong> means a course that has \'Certificate\' or \'Diploma\' in the title (for example, \'Certificate III in Business\', \'Diploma of Nursing\').</p>
				<p><strong>Education history</strong></p>
				<p>Q1. What is the highest qualification that you have <strong>now</strong>, or <strong>expect to complete</strong> at the time the training you are applying for is scheduled to start? <strong>Don\'t include</strong> secondary or high school qualifications.<br/>
				<i>(Include code and full title of qualification, if possible, for example, Certificate III in Aged Care. If you have not completed <strong>any</strong> qualification, write \'none\')</i>
				<br/> - ' . $form->highest_qualification_completed . '</p>
				<p>Q2. How many other <strong>Skills First funded</strong> qualifications have you enrolled in that have started, or will start in the <strong>same calendar year</strong> as the qualification/s you are applying for now? <strong>Don\'t</strong> include the qualification/s you are applying for now. <strong>Do</strong> include other qualification/s you\'ve enrolled in at this or another training provider but haven\'t started yet..
				<br/> - ' . $form->government_funded_enrolments_this_year . '</p>
				<p>Q3. Not including the qualifications/s you are applying for now, how many other <strong>Skills First funded</strong> courses are you doing at the moment?
				<br/> - ' . $form->government_funded_undertakings_at_present . '</p>
				<p>Q4. In your lifetime, how many <strong>government funded</strong> qualifications have you started (commenced) that are at the same level as the one you are applying for now?
				<br/> - ' . $form->government_funded_in_lifetime . '</p>';
	return $content;
}


function neca_pdf_content_skills_first_job_trainer($form)
{
	$content = '<p><em><strong>FOR JOBTRAINER ENROLMENT ONLY</strong></em></p>
				<p>Q5. Are you seeking to enrol in a qualification under the JobTrainer Initiative? <strong>Note:</strong> You can only enrol in <strong>one</strong> qualification under the JobTrainer initiative.
				<br/> - ' . $form->jobtrainer . '</p>';
	
	$content .= '<p>Q6. If you answered YES to Q5, have you previously started a qualification under the JobTrainer initiative?
				<br/> - ' . $form->jobtrainer_previously_started . '</p>';
	
	$content .= '<p>Q7. If you answered YES to Q6, are you applying to recommence in the same qualification that you already started under the JobTrainer initiative?
				<br/> - ' . $form->jobtrainer_recommence . '</p>';
	
	$content .= '<p>Q8. Are you 17 to 24 years old?
				<br/> - ' . $form->jobtrainer_17_to_24 . '</p>';
	
	$content .= '<p>Q9. Are you a job seeker?
				<br/> - ' . $form->jobtrainer_job_seeker . '</p>';
	
	$content .= '<p>Q10. If you answered YES to Q9, list the items which apply to?';
	if(isset($form->jobtrainer_applicable) && is_array($form->jobtrainer_applicable) && count($form->jobtrainer_applicable) > 0)
	{
		foreach ( $form->jobtrainer_applicable as $applicable )
		{
			$content .= '<br/> - ' . $applicable;
		}
	}
	$content .= '</p>';
	
	$content .= '<p>Q11. If you did not tick any of the boxes in Q10, you can make a declaration that you are a job seeker by ticking this box and signing this form?<br/>' . $form->jobtrainer_declaration . '</p>';
	
	return $content;
}


function neca_pdf_content_skills_first_student_declaration($form) 
{
	$now = DateTime::createFromFormat ( 'U', current_time ( 'timestamp' ) );
	$enrolled_in_a_school = strstr ( $form->enrolled_in_a_school, 'I AM NOT' ) ? "I AM NOT" : "I AM";
	$enrolled_in_skills_for_education = strstr ( $form->enrolled_in_skills_for_education, 'I AM NOT' ) ? "I AM NOT" : "I AM";
	
	// Get the Course Name by Course Scope Code
	$course_scope_name = JobReadyCourseOperations::getJobReadyCourseFieldByCourseScopeCode($form->course_scope_code, 'jrc_course_scope_name');
	
	if (trim ( $course_scope_name ) != '')
	{
		$course_name = $form->course_scope_code . ' - ' . $course_scope_name;
	}
	else
	{
		$course_name = $form->course_scope_code;
	}
	
	$content = '<p><strong>Student declaration</strong></p>
				<p>I <strong>' . $form->first_name . ' ' . $form->middle_name . ' ' . $form->surname . '</strong>, in seeking to enrol in <strong>' . $course_name . '</strong> declare the following to be true and accurate statements:</p>
				<p>- a. ' . $enrolled_in_a_school . ' enrolled in a school, including government, non-government, independent, Catholic or home school.
				<br/>- b. ' . $enrolled_in_skills_for_education . ' enrolled in the Commonwealth Government’s <em>Skills for Education and Employment</em> program.';
	
	if($form->course_scope_code != 'BSB41515')
	{
		$content.= '<br/>- c. I understand that my enrolment in the above qualification/s and/or skill set/s may be subsidised by the Victorian and Commonwealth Governments under the <em>Skills First</em> Program. I understand how enrolling in the above Qualification/s will affect my future training options and eligibility for further government subsidised training under the <em>Skills First</em> Program.';
	}
	
	$content.= '<br/>- d. I acknowledge and understand that I may be contacted by the Department of Education and Training or an agent to participate in a student survey, interview or other questionnaire.</p>
				<p>Signed: ' . $form->signature . '__________________________________________ &nbsp;&nbsp;&nbsp;&nbsp;Date: ' . $now->format ( "d/m/Y" ) . '</p>';
	return $content;
}


function neca_pdf_content_skills_first_authorised_delegate($form)
{
	// Get the Course Name by Course Scope Code
	$course_scope_name = JobReadyCourseOperations::getJobReadyCourseFieldByCourseScopeCode($form->course_scope_code, 'jrc_course_scope_name');
	
	if (trim ( $course_scope_name) != '')
	{
		$course_name = $form->course_scope_code . ' - ' . $course_scope_name;
	}
	else
	{
		$course_name = $form->course_scope_code;
	}

	$content = '<p><em>Section C - To be completed by an authorised delegate of the Training Provider</em></p>';
	
	/* 2022.05.03 - Removed as requested by Ashima via email 03/05/2022
	 <table class="tbl2">
		 <tr>
			 <td style="width: 80%" valign="top"><strong>Number of qualifications student is currently eligible for:</strong></td>
			 <td style="width: 5%;" valign="top">[&nbsp;&nbsp;&nbsp;] 0</td>
			 <td style="width: 5%;" valign="top">[&nbsp;&nbsp;&nbsp;] 1</td>
			 <td style="width: 10%;" valign="top">[&nbsp;&nbsp;&nbsp;] 2</td>
		 </tr>
	 </table>
	 */
	
	$content .='<table class="tbl2">
					<tr>
						<td style="width: 80%" valign="top"><strong>Eligibility exemption granted:</strong></td>
						<td style="width: 10%;" valign="top">[&nbsp;&nbsp;&nbsp;] Yes</td>
						<td style="width: 10%;" valign="top">[&nbsp;&nbsp;&nbsp;] No</td>
					</tr>
				</table>
			
				<p>Based on:</p>
				<ul>
					<li>my discussion with the student</li>
					<li>the evidence I have sighted and retained in <strong>Section A</strong>; and</li>
					<li>the information provided to me by the student in <strong>Section B</strong></li>
				</ul>

				<p>I confirm that the student is <br/>';
	
	if($form->course_scope_code == 'UEE30820')
	{
		$content .= '<span style="font-family: dejavusans;">[&#10003;]</span> ';
	}
	else
	{
		$content .= '[&nbsp;&nbsp;&nbsp;] ';
	}
	
	$content .= 'eligible for Skills First funding<br/> 
				[&nbsp;&nbsp;&nbsp;] not eligible for Skills First funding<br/>
				[&nbsp;&nbsp;&nbsp;] not eligible for Skills First funding, but I have granted an eligibility exemption<br/>
				for the course/s listed below:<br/><br/>
				<strong>' . $course_name . '</strong></p>
				<p>I acknowledge that as the Training Provider\'s authorised delegate, I am responsible for ensuring that all parts of this form are complete. By signing this declaration, I acknowledge that I have reviewed Sections A and B and have confirmed they have been completed in full.</p>
				<h3>Authorised Training Provider delegate:</h3>
				<p>Name: ____________________________________________<br/></p>
				<p>Position: __________________________________________<br/></p>
				<p>Signed: __________________________________________ Date: _________________<br/><br/></p>
				<p>Notes: Use this section to record additional detail, relevant eligibility information, including information you used to verify the student\'s eligibility that is not captured in Sections A or B.<br/><strong>If there are no notes, write N/A</strong><br/><br/><br/></p>';
	
	return $content;
}


// Pre Training Review PDF
function pre_training_review_pdf($form, $course_name) 
{
	// Start
	$content = neca_pdf_content_start ();
	
	// Add Intro
	$content .= neca_pdf_content_pre_training_review_intro ( $form, $course_name );
	
	// Add Your Expectations
	$content .= neca_pdf_content_pre_training_review_your_expectations ( $form );
	
	// Add Previous Learning Experience
	$content .= neca_pdf_content_pre_training_review_previous_learning_experience ( $form );
	
	// Page End
	$content .= neca_pdf_page_end ();
	
	// Page Start
	$content .= neca_pdf_page_start ();
	
	// Add Recognition of Prior Learning (RPL)
	$content .= neca_pdf_content_pre_training_review_rpl ( $form );
	
	// Delivery Mode
	$content .= neca_pdf_content_pre_training_review_delivery_mode ( $form );
	
	// Page End
	$content .= neca_pdf_page_end ();
	
	// Page Start
	$content .= neca_pdf_page_start ();
	
	// Student Declaration
	$content .= neca_pdf_content_pre_training_review_student_declaration ( $form );
	
	// Office Use Only
	$content .= neca_pdf_content_pre_training_review_office_use_only ( $form );
	
	// End
	$content .= neca_pdf_page_end ();
	
	// Setup Filename
	$filename = urlencode ( 'Pre_Training_Review_' . $form->first_name . '_' . $form->surname . '_' . current_time ( 'Ymd_hms' ) ) . '.pdf';
	
	// Create PDF
	neca_create_pdf ( $filename, $content );
	
	return $filename;
}


function neca_pdf_content_pre_training_review_intro($form, $course_name) 
{
	$content = '<div style="padding-top: 50px; padding-bottom: 10px;">
					<h4>Pre Training Review Form – Apprentice</h4>
				</div>
				<h4>Student Name: ' . $form->first_name . ' ' . $form->middle_name . ' ' . $form->surname . '</h4>
				<p>The purpose of this Pre Training Review is to identify whether or not the qualification ' . $course_name . ' is suitable and most suitable to your needs.</p>
				<p>This Pre-Training Review will also assist NECA Education & Careers to determine and understand your objectives for undertaking the course, explore your current competencies and provide opportunities for these to be assessed through Recognition of Prior Learning (RPL) or Credit Transfer (CT)</p>';
	
	return $content;
}


function neca_pdf_content_pre_training_review_your_expectations($form) 
{
	$content = '<h4>Your Expectations</h4>
				<p>a) Have you reviewed the course outline provided in your enrolment pack and/or on our Website?
				<br/> - ' . $form->expectation_outline_reviewed . '</p>
						
				<p>b) Based on question (a) is this the typical work environment you would like to work in after the successful completion of the qualification?
				<br/>' . $form->expectation_working_environment . '</p>
						
				<p>c) Why have you chosen this Program/course? What do you hope to achieve once you complete the program/Course?
				<br/> - ' . $form->expectation_reason_and_outcome . '</p>
						
				<p>d) What do you know about the Electrical industry and what appeals to you about the industry?
				<br/> - ' . $form->expectation_knowledge_and_appeal . '</p>
						
				<p>e) Why have you chosen NECA Education & Careers to complete your program/Course?
				<br/> - ' . $form->expectation_why_neca . '</p>
';
	
	return $content;
}


function neca_pdf_content_pre_training_review_previous_learning_experience($form) 
{
	$ple_difficulties_flag = ($form->ple_difficulties_flag == "No") ? 'No' : 'Yes';
	
	$content = '<h4>Previous Learning Experience</h4>
				<p>f) In your past learning experiences, have you encountered any difficulties to learning?
				<br/> - ' . $ple_difficulties_flag . '</p>
				<p>g) Based on your above answer, Give a brief description of your past learning experiences
				<br/> - ' . $form->ple_difficulties . '</p>
				<p>h) From the information that you currently have about the course, do you have any concerns that might prevent you from progressing through this program? If yes, please give a brief description.
				<br/> - ' . $form->ple_concerns . '</p>
				';
	
	return $content;
}


function neca_pdf_content_pre_training_review_rpl($form) 
{
	$credit_transfer = trim ( $form->credit_transfer ) != '' ? $form->credit_transfer : 'No';
	$rpl = trim ( $form->rpl ) != '' ? $form->rpl : 'No';
	
	$content = '<h4>Recognition of prior Learning (RPL) / Credit transfer (CT)</h4>
				<p><strong>Credit Transfer</strong><br/>
				Credit transfer is where we look at your previous formal study and credit any units that have been successfully completed. In order for any credits to be granted, you will need to provide your statement of attainment / transcript of results.<br/>
				Do you wish to apply for Credit Transfer (CT)?&nbsp;&nbsp;&nbsp;&nbsp;' . $credit_transfer . '</p>
				<p><strong>Recognition of Prior Learning (RPL)</strong><br/>
				RPL is where we formally assess the skills and knowledge you have achieved through previous studies, work and life experiences. You will need to provide evidence showing where the prior learning and/or experience may be relevant and undertake an assessment to demonstrate your skills at a cost of $750pr unit.<br/>
				Do you wish to apply for Recognition of Prior Learning (RPL)?&nbsp;&nbsp;&nbsp;&nbsp;' . $rpl . '</p>';
	
	return $content;
}


function neca_pdf_content_pre_training_review_delivery_mode($form) 
{
	$count = 1;
	$learning_style_results = array ();
	foreach ( $form->learning_style as $k => $v ) 
	{
		$learning_style_results [$v] = $count;
		$count ++;
	}
	
	$count = 1;
	$learning_preference_results = array ();
	foreach ( $form->learning_preference as $k => $v ) 
	{
		$learning_preference_results [$v] = $count;
		$count ++;
	}
	
	$content = '<h4>Delivery Mode / Learning Styles</h4>
				<p>a) What is your preferred learning style? (Put numbers in order of preference)</p>
				<table class="tbl2">
					<tr>
						<td style="width: 5%;" valign="top">[&nbsp;' . $learning_style_results [1] . '&nbsp;]</td>
						<td style="width: 95%" valign="top">Being shown how to do something, and then trying it yourself with some supervision</td>
					</tr>
					<tr>
						<td style="width: 5%;" valign="top">[&nbsp;' . $learning_style_results [2] . '&nbsp;]</td>
						<td style="width: 95%" valign="top">Researching and reading, theorising and discussing</td>
					</tr>
					<tr>
						<td style="width: 5%;" valign="top">[&nbsp;' . $learning_style_results [3] . '&nbsp;]</td>
						<td style="width: 95%" valign="top">A mix of being shown how to do something, trying it out, and talking to someone who has done it</td>
					</tr>
					<tr>
						<td style="width: 5%;" valign="top">[&nbsp;' . $learning_style_results [4] . '&nbsp;]</td>
						<td style="width: 95%" valign="top">Working with others on the same Problem-I do not like doing it alone</td>
					</tr>
				</table>
				<p>b) How do you prefer to learn? (put numbers in order of preference)</p>
				<table class="tbl2">
					<tr>
						<td style="width: 5%;" valign="top">[&nbsp;' . $learning_preference_results [1] . '&nbsp;]</td>
						<td style="width: 95%" valign="top">Doing practical things with an end result that I can see</td>
					</tr>
					<tr>
						<td style="width: 5%;" valign="top">[&nbsp;' . $learning_preference_results [2] . '&nbsp;]</td>
						<td style="width: 95%" valign="top">In lecture theatres and places where I can discuss, read and research ideas</td>
					</tr>
					<tr>
						<td style="width: 5%;" valign="top">[&nbsp;' . $learning_preference_results [3] . '&nbsp;]</td>
						<td style="width: 95%" valign="top">In a relaxed environment with lots of discussions, where I can ask the teacher when I need help or guidance</td>
					</tr>
					<tr>
						<td style="width: 5%;" valign="top">[&nbsp;' . $learning_preference_results [4] . '&nbsp;]</td>
						<td style="width: 95%" valign="top">In a friendly environment where I learn from others knowledge & experience</td>
					</tr>
				</table>
								
				<h4>Assessing Digital Literacy</h4>
				<p>
					As the course you are enrolling in includes an online or digital component<br/>
					a) Do you have regular access to a computer and internet&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $form->computer_access_internet . '<br/><br/>
					<strong>NOTE: </strong>Students who answered NO-should have a further discussion with Student Services
				</p>
				<p>
					b) Approximately, how often do you use a computer and/or the internet?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $form->computer_usage . '<br/><br/>
					<strong>NOTE: </strong>Students who answer A couple of times a month or never/rarely -should have a further discussion with Student Services
				</p>
				<p>c) Please select the relevant option based on your ability.</p>
				<table>
					<tr>
						<td style="width: 20%;" valign="top">I can turn on and login to a computer</td>
						<td style="width: 80%;" valign="top">' . $form->computer_turn_on. '</td>
					</tr>
					<tr>
						<td style="width: 20%;" valign="top">I can send an email</td>
						<td style="width: 80%;" valign="top">' . $form->computer_email. '</td>
					</tr>
					<tr>
						<td style="width: 20%;" valign="top">I can navigate to a website to locate required information</td>
						<td style="width: 80%;" valign="top">' . $form->computer_website. '</td>
					</tr>
					<tr>
						<td style="width: 20%;" valign="top">I can find information using an Internet search engine</td>
						<td style="width: 80%;" valign="top">' . $form->computer_search. '</td>
					</tr>
					<tr>
						<td style="width: 20%;" valign="top">I can attach documents to an email</td>
						<td style="width: 80%;" valign="top">' . $form->computer_attach_email. '</td>
					</tr>
					<tr>
						<td style="width: 20%;" valign="top">I can login to an online system and follow prompts</td>
						<td style="width: 80%;" valign="top">' . $form->computer_online_system. '</td>
					</tr>
				</table>
				';
	return $content;
}


function neca_pdf_content_pre_training_review_student_declaration($form) 
{
	$now = DateTime::createFromFormat ( 'U', current_time ( 'timestamp' ) );
	$content = '<p><strong>Student Declaration: </strong><br/>
				I, ' . $form->first_name . ' ' . $form->middle_name . ' ' . $form->surname . ' declare that the information entered on this form is, to the best of my knowledge, true, correct and complete.<br/><br/></p>
				<table class="tbl2">
					<tr>
						<td style="width:20%">Signature: </td>
						<td style="width:30%">' . $form->signature . '</td>
						<td style="width:20%">Date: </td>
						<td style="width:30%">' . $now->format ( "d/m/Y" ) . '</td>
					</tr>
				</table>';
	
	return $content;
}


function neca_pdf_content_pre_training_review_office_use_only($form) 
{
	$content = '<h3>OFFICE USE ONLY</h3>
				<strong>Digital Literacy Level is assessed and incorporated as the individual completes the following online.</strong>
				<ul>
					<li>LLN Robot Quiz</li>
					<li>Online Application Form (includes Enrolment form, Pretraining review form, Skills First Declaration)</li>
				</ul>
				<p>
					<strong>Appropriate and Suitable: </strong> I confirm that according to my assessment this course is the most suitable and appropriate for this individual student due to the below listed reason(s)<br/>
					<table class="tbl2">
						<tr>
							<td style="width: 5%;" valign="top">[&nbsp;&nbsp;&nbsp;]</td>
							<td style="width: 95%" valign="top">Meets their career pathway</td>
						</tr>
						<tr>
							<td style="width: 5%;" valign="top">[&nbsp;&nbsp;&nbsp;]</td>
							<td style="width: 95%" valign="top">Already working in the industry</td>
						</tr>
						<tr>
							<td style="width: 5%;" valign="top">[&nbsp;&nbsp;&nbsp;]</td>
							<td style="width: 95%" valign="top">Up skilling to further career</td>
						</tr>
						<tr>
							<td style="width: 5%;" valign="top">[&nbsp;&nbsp;&nbsp;]</td>
							<td style="width: 95%" valign="top">Passion for working in Electrical Industry</td>
						</tr>
						<tr>
							<td style="width: 5%;" valign="top">[&nbsp;&nbsp;&nbsp;]</td>
							<td style="width: 95%" valign="top">Other-Please outline and explain why?</td>
						</tr>
					</table><br/>
					________________________________________________________________________________<br/><br/>
					________________________________________________________________________________
				</p>
				<p>
					<strong>The proposed training aligns with the stated objective in the VET funding contract:</strong><br/>
					<table class="tbl2">
						<tr>
							<td style="width: 5%;" valign="top">[&nbsp;&nbsp;&nbsp;]</td>
							<td style="width: 95%" valign="top">Enable eligible individuals to obtain the required skills to make them job ready</td>
						</tr>
						<tr>
							<td style="width: 5%;" valign="top">[&nbsp;&nbsp;&nbsp;]</td>
							<td style="width: 95%" valign="top">Assist eligible Individuals to undertake further education; and/or</td>
						</tr>
						<tr>
							<td style="width: 5%;" valign="top">[&nbsp;&nbsp;&nbsp;]</td>
							<td style="width: 95%" valign="top">Promote/enable access to training for disadvantaged learners</td>
						</tr>
					</table><br/>
					<strong>Comments</strong><br/><br/>
					________________________________________________________________________________<br/><br/>
					________________________________________________________________________________
				</p>
				<p>
					<strong>NOT appropriate and Suitable:</strong> I confirm according to my assessment that this course is NOT suitable and appropriate for this student due to the below listed reason(s)<br/><br/>
					<strong>Comments</strong><br/><br/>
					________________________________________________________________________________<br/><br/>
					________________________________________________________________________________
				</p>
				<p>
					My recommendations are: <br/>
					<table class="tbl2">
						<tr>
							<td style="width: 5%;" valign="top">[&nbsp;&nbsp;&nbsp;]</td>
							<td style="width: 95%" valign="top">Student to attend English classes to improve on language</td>
						</tr>
						<tr>
							<td style="width: 5%;" valign="top">[&nbsp;&nbsp;&nbsp;]</td>
							<td style="width: 95%" valign="top">Student to undertake a lower level course</td>
						</tr>
						<tr>
							<td style="width: 5%;" valign="top">[&nbsp;&nbsp;&nbsp;]</td>
							<td style="width: 95%" valign="top">Student to undertake a course in a different sector</td>
						</tr>
					</table><br/><br/>
					Name: ____________________________________________<br/><br/>
					Signature: __________________________________________ Date: _________________
				</p>
				';
	
	return $content;
}