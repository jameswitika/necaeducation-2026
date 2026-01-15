<?php
// Required for all the sessions used to reduce "Job Ready API" calls
session_start();

/* ****************************** *
 * JOB-READY PRE-POPULATED FILEDS *
 * ****************************** */
// Pre-Populate References from Job Ready (used across multiple forms)

// Invoice Options
function jrar_invoice_options($course_number, $neca_member)
{
	
	$choices = array();
	
	$course = JRACourseOperations::loadJRACourseByCourseNumber($course_number);

	foreach($course->invoice_options as $invoice_option)
	{
		if($invoice_option->neca_member == $neca_member && $invoice_option->internal == false)
		{
			$choice = array(	'text'			=> $invoice_option->name,
								'value'			=> $invoice_option->name,
								'price'			=> $invoice_option->total,
								'isSelected'	=> (count($choices) == 0) ? true : false );
			array_push($choices, $choice);
		}
	}
	
	if(count($choices) == 0)
	{
		foreach($course->invoice_options as $invoice_option)
		{
			if($invoice_option->neca_member == false && $invoice_option->internal == false)
			{
				$choice = array(	'text'			=> $invoice_option->name,
									'value'			=> $invoice_option->name,
									'price'			=> $invoice_option->total,
									'isSelected'	=> (count($choices) == 0) ? true : false );
				array_push($choices, $choice);
			}
		}
	}
	
	return $choices;
}

// Title
function jrar_title()
{
	if(!isset($_SESSION['title_choices']))
	{
		$choices = JRAReferenceOperations::getReference('title', 'title');
		$_SESSION['title_choices'] = $choices;
		
	}
	return $_SESSION['title_choices'];
}

// Gender
function jrar_gender()
{
	if(!isset($_SESSION['gender_choices']))
	{
		$choices = JRAReferenceOperations::getReference('gender', 'gender');
		$choices_fixed = array();
		
		// Replaces the "X" text with Indeterminate
		foreach($choices as $choice)
		{
			if($choice['text'] == 'X')
			{
				$choice['text'] = 'Not Specified/Other';
			}
			array_push($choices_fixed, $choice);
		}
		$_SESSION['gender_choices'] = $choices_fixed;
	}
	
	return $_SESSION['gender_choices'];
}

// State (Address) + State (Postal Address) + States
function jrar_state()
{
	if(!isset($_SESSION['state_choices']))
	{
		$choices = JRAReferenceOperations::getReference('state', 'state');
		$_SESSION['state_choices'] = $choices;
	}
	return $_SESSION['state_choices'];
}

// Employment Category
function jrar_employment_category()
{
	if(!isset($_SESSION['employment_category_choices']))
	{
		$choices = JRAReferenceOperations::getReference('employment_category', 'employment-category');
		
		// Removes the "Not stated" option
		foreach($choices as $key => $choice)
		{
			if($choice['text'] == 'Not stated')
			{
				unset($choices[$key]);
			}
		}
		$_SESSION['employment_category_choices'] = $choices;
	}
	
	return $_SESSION['employment_category_choices'];
}

// Country
function jrar_country()
{
	if(!isset($_SESSION['country_choices']))
	{
		$choices = JRAReferenceOperations::getReference('country', 'country');
		asort($choices);
		$_SESSION['country_choices'] = $choices;
	}
	return $_SESSION['country_choices'];
}

// Citizenship Status
function jrar_citizenship_status()
{
	if(!isset($_SESSION['citizenship_status_choices']))
	{
		$choices = JRAReferenceOperations::getReference('citizenship_status', 'citizenship-status');
		$_SESSION['citizenship_status_choices'] = $choices;
	}
	return $_SESSION['citizenship_status_choices'];
}

// Indigenous Status
function jrar_indigenous_status()
{
	if(!isset($_SESSION['indigenous_status_choices']))
	{
		$choices = JRAReferenceOperations::getReference('indigenous_status', 'indigenous-status');
		
		// Removes the "Not stated" option
		foreach($choices as $key => $choice)
		{
			if($choice['text'] == 'Not stated')
			{
				unset($choices[$key]);
			}
		}
		
		$_SESSION['indigenous_status_choices'] = $choices;
	}
	
	return $_SESSION['indigenous_status_choices'];
}

// Language
function jrar_language()
{
	if(!isset($_SESSION['language_choices']))
	{
		$choices = JRAReferenceOperations::getReference('language', 'language');
		asort($choices);
		$_SESSION['language_choices'] = $choices;
	}
	return $_SESSION['language_choices'];
}

// Disability Type
function jrar_disability_type()
{
	if(!isset($_SESSION['disability_type_choices']))
	{
		$choices = JRAReferenceOperations::getReference('disability_type', 'disability-type');
		$_SESSION['disability_type_choices'] = $choices;
	}
	return $_SESSION['disability_type_choices'];
}

// Highest School Level
function jrar_highest_school_level()
{
	if(!isset($_SESSION['highest_school_level_choices']))
	{
		$choices = JRAReferenceOperations::getReference('highest_school_level', 'highest-school-level');
		// Removes the "Not stated" option
		foreach($choices as $key => $choice)
		{
			if($choice['text'] == 'Not stated')
			{
				unset($choices[$key]);
			}
		}
		$_SESSION['highest_school_level_choices'] = $choices;
	}
	return $_SESSION['highest_school_level_choices'];
}

// Prior Education Type
function jrar_prior_education_type()
{
	if(!isset($_SESSION['prior_education_type_choices']))
	{
		$choices = JRAReferenceOperations::getReference('prior_education_type', 'prior-education-type');
		$_SESSION['prior_education_type_choices'] = $choices;
	}
	return $_SESSION['prior_education_type_choices'];
}


// Client Industry Employer
function jrar_client_industry_employer()
{
	if(!isset($_SESSION['client_industry_employer']))
	{
		$choices[] = array (	'text' => 'Agriculture, Forestry and Fishing',
								'value' => 'Agriculture, Forestry and Fishing' );
		$choices[] = array (	'text' => 'Mining',
								'value' => 'Mining' );
		$choices[] = array (	'text' => 'Manufacturing',
								'value' => 'Manufacturing' );
		$choices[] = array (	'text' => 'Electricity, Gas, Water and Waste Services',
								'value' => 'Electricity, Gas, Water and Waste Services' );
		$choices[] = array (	'text' => 'Construction',
								'value' => 'Construction' );
		$choices[] = array (	'text' => 'Wholesale Trade',
								'value' => 'Wholesale Trade' );
		$choices[] = array (	'text' => 'Retail Trade',
								'value' => 'Retail Trade' );
		$choices[] = array (	'text' => 'Accommodation and Food Services',
								'value' => 'Accommodation and Food Services' );
		$choices[] = array (	'text' => 'Transport, Postal and Warehousing',
								'value' => 'Transport, Postal and Warehousing' );
		$choices[] = array (	'text' => 'Information Media and Telecommunications',
								'value' => 'Information Media and Telecommunications' );
		$choices[] = array (	'text' => 'Financial and Insurance Services',
								'value' => 'Financial and Insurance Services' );
		$choices[] = array (	'text' => 'Rental, Hiring and Real Estate Services',
								'value' => 'Rental, Hiring and Real Estate Services' );
		$choices[] = array (	'text' => 'Professional, Scientific and Technical Services',
								'value' => 'Professional, Scientific and Technical Services' );
		$choices[] = array (	'text' => 'Administrative and Support Services',
								'value' => 'Administrative and Support Services' );
		$choices[] = array (	'text' => 'Public Administration and Safety',
								'value' => 'Public Administration and Safety' );
		$choices[] = array (	'text' => 'Education and Training',
								'value' => 'Education and Training' );
		$choices[] = array (	'text' => 'Health Care and Social Assistance',
								'value' => 'Health Care and Social Assistance' );
		$choices[] = array (	'text' => 'Arts and Recreation Services',
								'value' => 'Arts and Recreation Services' );
		$choices[] = array (	'text' => 'Other Services',
								'value' => 'Other Services' );
		
		$_SESSION['client_industry_employer'] = $choices;
	}
	return $_SESSION['client_industry_employer'];
}


// Client Occupation Identifier
function jrar_client_occupation_identifer()
{
	if(!isset($_SESSION['client_occupation_identifier']))
	{
		$choices[] = array (	'text' => 'Managers',
								'value' => 'Managers' );
		$choices[] = array (	'text' => 'Professionals',
								'value' => 'Professionals' );
		$choices[] = array (	'text' => 'Technicians and Trades Workers',
								'value' => 'Technicians and Trades Workers' );
		$choices[] = array (	'text' => 'Community and Personal Service Workers',
								'value' => 'Community and Personal Service Workers' );
		$choices[] = array (	'text' => 'Clerical and Administrative Workers',
								'value' => 'Clerical and Administrative Workers' );
		$choices[] = array (	'text' => 'Sales Workers',
								'value' => 'Sales Workers' );
		$choices[] = array (	'text' => 'Machinery Operators and Drivers',
								'value' => 'Machinery Operators and Drivers' );
		$choices[] = array (	'text' => 'Labourers',
								'value' => 'Labourers' );
		$choices[] = array (	'text' => 'Other',
								'value' => 'Other' );
		
		$_SESSION['client_occupation_identifier'] = $choices;
	}
	
	return $_SESSION['client_occupation_identifier'];
}


// Years for Select
function get_years_for_select()
{
	$year = date("Y");
	$choices[] = array ('text' => '- Please Select -', 'value' => '');
	
	for($i = $year; $i>=1970; $i-- )
	{
		$choices[] = array ( 'text' => $i, 'value' => $i );
	}
	return $choices;
}


// Study Reason
function jrar_study_reason()
{
	if(!isset($_SESSION['study_reason_choices']))
	{
		$choices = JRAReferenceOperations::getReference('study_reason', 'study-reason');
		// Removes the "Not specified" option
		foreach($choices as $key => $choice)
		{
			if($choice['text'] == 'Not Specified')
			{
				unset($choices[$key]);
			}
		}
		$_SESSION['study_reason_choices'] = $choices;
	}
	return $_SESSION['study_reason_choices'];
}