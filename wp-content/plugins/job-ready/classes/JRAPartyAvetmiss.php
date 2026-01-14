<?php
/*
 * JRAPartyAvetmiss class + JRAPartyAvetmissOperations class
 * Created by: James Witika
 * Company: Smooth Developments
 */

if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class JRAPartyAvetmiss
{
	var $highest_school_level; 			// reference
	var $year_highest_school_level; 	// integer
	var $indigenous_status; 			// reference
	var $main_language; 				// reference
	var $labour_force_status; 			// reference
	var $disability_flag; 				// reference If	“Yes”,	party_disabilities must	have elements
	var $disability_types;				// array of disability-type
	var $prior_education_flag; 			// reference While adding or updating a party’s “Prior education” from API, the "prior_educationflag" must be set to "Yes" in order for it to appear in the UI (however, it will be recorded in the database and will show up in the API.) If “Yes”, party_prior_educations must have elements
	var $prior_educations;				// array of prior_education_type
	var $prior_education_qualification; // reference 
	var $at_school_flag; 				// reference
	var $spoken_english_proficiency; 	// reference
	var $learner_unique_identifier; 	// string
	var $school; 						// reference
	var $client_identifier; 			// string
	var $town_of_birth; 				// string
	var $country_of_birth; 				// reference A	country
	var $nationality; 					// reference A country
	var $citizenship_status; 			// reference Available from Feb 17.
	
	function __construct()
	{
		$this->highest_school_level = ''; 			// reference
		$this->year_highest_school_level = ''; 		// integer
		$this->indigenous_status = ''; 				// reference
		$this->main_language = ''; 					// reference
		$this->labour_force_status = ''; 			// reference
		$this->disability_flag = ''; 				// reference If	“Yes”, party_disabilities must have elements
		$this->disability_types = array();				// array of disability types (string)
		$this->prior_education_flag = ''; 			// reference While adding or updating a party’s “Prior education” from API, the "prior_educationflag" must be set to "Yes" in order for it to appear in the UI (however, it will be recorded in the database and will show up in the API.) If “Yes”, party_prior_educations must	have elements
		$this->prior_educations = array();			// array of prior education types (string)
		$this->prior_education_qualification = '';	// reference
		$this->at_school_flag = ''; 				// reference
		$this->spoken_english_proficiency = ''; 	// reference
		$this->learner_unique_identifier = ''; 		// string
		$this->school = ''; 						// reference
		$this->client_identifier = ''; 				// string
		$this->town_of_birth = ''; 					// string
		$this->country_of_birth = ''; 				// reference A country
		$this->nationality = ''; 					// reference A country
		$this->citizenship_status = ''; 			// reference Available from Feb 17.
	}
}


class JRAPartyAvetmissOperations
{
	function __construct()
	{

	}
	

	// Create XML layout for all Party Avetmiss
	static function createJRAPartyAvetmissXML( $avetmiss )
	{
		$xml = '<avetmiss>';
		
		if($avetmiss->highest_school_level != '')
				$xml .= '	<highest-school-level>'.$avetmiss->highest_school_level.'</highest-school-level>';
		
			if($avetmiss->year_highest_school_level != '')
				$xml .= '	<year-highest-school-level>'.$avetmiss->year_highest_school_level.'</year-highest-school-level>';
		
			if($avetmiss->indigenous_status != '')
				$xml .= '	<indigenous-status>'.$avetmiss->indigenous_status.'</indigenous-status>';
		
			if($avetmiss->main_language != '')
				$xml .= '	<main-language>'.$avetmiss->main_language.'</main-language>';
		
			if($avetmiss->labour_force_status != '')
				$xml .= '	<labour-force-status>'.$avetmiss->labour_force_status.'</labour-force-status>';
		
			if($avetmiss->disability_flag != '')
				$xml .= '	<disability-flag>'.$avetmiss->disability_flag.'</disability-flag>';
			
			if($avetmiss->disability_flag == 'Yes' && count($avetmiss->disabilities > 0))
			{
				$xml .= '	<disabilities>';
				foreach($avetmiss->disability_types as $disability_type)
				{
					$xml .= '	<disability>
									<disability-type>'.$disability_type.'</disability-type>
								</disability>';
				}
				$xml .= ' 	</disabilities>';
			}
				

			if($avetmiss->prior_education_flag != '')
				$xml .= '	<prior-education-flag>'.$avetmiss->prior_education_flag.'</prior-education-flag>';

			if($avetmiss->prior_education_flag == 'Yes' && count($avetmiss->prior_educations) > 0)
			{
				$xml .= '	<prior-educations>';
				foreach($avetmiss->prior_educations as $prior_education)
				{
					$xml .= '	<prior-education>
									<prior-education-type>'.$prior_education.'</prior-education-type>
									<prior-education-achievement-identifier>'.$avetmiss->prior_education_qualification.'</prior-education-achievement-identifier>
								</prior-education>';
				}
				$xml .= ' 	</prior-educations>';
			}
				
			if($avetmiss->at_school_flag!= '')
				$xml .= '	<at-school-flag>'.$avetmiss->at_school_flag.'</at-school-flag>';
		
			if($avetmiss->spoken_english_proficiency != '')
				$xml .= '	<spoken-english-proficiency>'.$avetmiss->spoken_english_proficiency.'</spoken-english-proficiency>';
		
			if($avetmiss->learner_unique_identifier != '')
				$xml .= '	<learner-unique-identifier>'.$avetmiss->learner_unique_identifier.'</learner-unique-identifier>';
		
// 			if($avetmiss->school!= '')
// 				$xml .= '	<school>'.$avetmiss->school.'</school>';
		
			if($avetmiss->client_identifier != '')
				$xml .= '	<client-identifier>'.$avetmiss->client_identifier.'</client-identifier>';

			if($avetmiss->town_of_birth != '')
				$xml .= '	<town-of-birth>'.$avetmiss->town_of_birth.'</town-of-birth>';
		
			if($avetmiss->country_of_birth != '')
				$xml .= '	<country-of-birth>'.$avetmiss->country_of_birth.'</country-of-birth>';
		
			if($avetmiss->nationality != '')
				$xml .= '	<nationality>'.$avetmiss->nationality.'</nationality>';
		
			if($avetmiss->citizenship_status != '')
				$xml .= '	<citizenship-status>'.$avetmiss->citizenship_status.'</citizenship-status>';
		
		$xml .= '</avetmiss>';
		
		return $xml;
	}
}