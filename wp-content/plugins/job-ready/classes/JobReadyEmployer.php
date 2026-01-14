<?php
class JobReadyEmployer
{
	var $id;
	var $job_ready_id;
	var $party_id;
	var $legal_name;
	var $trading_name;
	var $status_id;

	function __construct()
	{
		$this->setInitialValues();
	}

	function setInitialValues()
	{
		$this->id = -1;
		$this->legal_name = '';
		$this->trading_name = '';
		$this->party_id = '';
		$this->status_id = 'AC';
	}

	function save()
	{
		$result = false;

		if($this->id > 0)
		{
			$result = JobReadyEmployerOperations::updateJobReadyEmployer($this);
		}
		else
		{
			$result = JobReadyEmployerOperations::insertJobReadyEmployer($this);
		}
		return $result;
	}

	function delete()
	{
		$result = false;
		if($this->id > -1)
		{
			$result = JobReadyEmployerOperations::deleteJobReadyEmployer($this);
		}
		return $result;
	}
}


class JobReadyEmployerOperations
{

	// __constructor function
	function __construct()
	{
		
	}


	// Loads a single employer
	// Returns: JobReadyEmployer Object
	public static function loadJobReadyEmployer( $employer_id )
	{
		$result = new JobReadyEmployer();
		$qParams = array();

		// Create a connection to the database
		global $wpdb;

		// Setup Query + qParams
		$query = 'SELECT * 
					FROM ' . $wpdb->prefix . 'employer
				   WHERE id = %d
				   LIMIT 1';
		
		array_push($qParams, $employer_id);
		
		// Prepare and get a single row
		$row = $wpdb->get_row( $wpdb->prepare( $query, $qParams ), ARRAY_A);
		
		// Populate result object
		JobReadyEmployerOperations::populateObject($row, $result);
		
		// Return result
		return $result;
	}

	
	// Loads a single employer
	// Returns: JobReadyEmployer Object
	public static function loadJobReadyEmployerByPartyID( $employer_party_id )
	{
		$result = new JobReadyEmployer();
		$qParams = array();
		
		// Create a connection to the database
		global $wpdb;
		
		// Setup Query + qParams
		$query = 'SELECT *
					FROM ' . $wpdb->prefix . 'employer
				   WHERE party_id = %d
				   LIMIT 1';
		
		array_push($qParams, $employer_party_id);
		
		// Prepare and get a single row
		$row = $wpdb->get_row( $wpdb->prepare( $query, $qParams ), ARRAY_A);
		
		// Populate result object
		JobReadyEmployerOperations::populateObject($row, $result);
		
		// Return result
		return $result;
	}
	

	// Loads all JobReadyEmployers
	// Returns: Array of JobReadyEmployer Objects
	public static function loadJobReadyEmployers( $status_id = 'AC' )
	{
		$result = array();
		$qParams = array();

		// Create a connection to the database
		global $wpdb;
		
		// Setup Query + qParams
		$query = 'SELECT * 
					FROM ' . $wpdb->prefix . 'employer
				   WHERE id > 0';
		
		if($status_id != 'ALL')
		{
			$query .= ' AND status_id = %s';
			array_push($qParams, $status_id);
		}

		$query .= " ORDER BY legal_name ASC";

		$rows = $wpdb->get_results( $wpdb->prepare( $query, $qParams ), ARRAY_A);

		// Loop through rows
		foreach($rows as $row)
		{
			$employer = new JobReadyEmployer();
			JobReadyEmployerOperations::populateObject($row, $employer);
			array_push($result, $employer);
		}
		return $result;
	}

	
	// Search JobReadyEmployers
	// Returns: Array of JobReadyEmployer Objects
	public static function searchJobReadyEmployers( $keyword )
	{
		$result = array();
		$qParams = array();

		// Create a connection to the database
		global $wpdb;
				
		$keyword_clean = sanitize_text_field($keyword);
		
		// Setup Query + qParams
		$query = 'SELECT *
					FROM ' . $wpdb->prefix . 'employer
				   WHERE (trading_name LIKE "%' . $keyword_clean . '%" OR legal_name LIKE "%' . $keyword_clean . '%")
					 AND status_id = "AC"
				ORDER BY trading_name ASC';
		
		//echo "Query: " . $query . "<br/>";
		
		$rows = $wpdb->get_results( $query, ARRAY_A);
		
		// Loop through rows
		foreach($rows as $row)
		{
			$employer = new JobReadyEmployer();
			JobReadyEmployerOperations::populateObject($row, $employer);
			array_push($result, $employer);
		}
		return $result;
	}
	

	// Insert a new employer
	public static function insertJobReadyEmployer($employer)
	{
		// Create a connection to the database
		global $wpdb;
		$table_name= $wpdb->prefix . "employer";

		$fields = array('job_ready_id' => $employer->job_ready_id,
						'trading_name' => $employer->trading_name,
						'legal_name' => $employer->legal_name,
						'party_id' => $employer->party_id,
						'status_id' => $employer->status_id );
		
		// Insert employer
		if( $wpdb->insert( $table_name, $fields ))
		{
			return $wpdb->insert_id;
		}
			
		return false;
	}


	// Updates a employer
	public static function updateJobReadyEmployer($employer)
	{
		// Create a connection to the database
		global $wpdb;
		$table_name= $wpdb->prefix . "employer";
		
		$fields = array('job_ready_id' => $employer->job_ready_id,
						'trading_name' => $employer->trading_name,
						'legal_name' => $employer->legal_name,
						'party_id' => $employer->party_id );
		
		// Insert employer
		if( false === $wpdb->update( $table_name, $fields, array( 'id' => $employer->id ) ))
		{
			$wpdb->print_error();
		}
		else
		{
			return $employer->id;
		}
		
		return false;
	}


	// Updates a employer field
	public static function updateJobReadyEmployerField($field, $value, $employer_id)
	{
		// Create a connection to the database
		global $wpdb;
		$table_name= $wpdb->prefix . "employer";
		
		$field = array( $field => $value );
		
		// Insert employer
		if( $wpdb->update( $table_name, $field, array( 'id' => $employer_id ) ))
		{
			return $employer_id;
		}
		
		return false;
	}


	// Deletes a JobReadyEmployer
	public static function deleteJobReadyEmployer($employer_id)
	{
		$result = JobReadyEmployerOperations::updateJobReadyEmployerField('status_id', 'DL', $employer_id);
	}


	// Get All JobReadyEmployers for Select
	public static function getAllJobReadyEmployersForWordPress()
	{
		// Create a connection to the database
		global $wpdb;
		
		// Setup Query + qParams
		$query = 'SELECT *
					FROM ' . $wpdb->prefix . 'employer
				   WHERE status_id = "AC"
				ORDER BY legal_name';

		$employers = $wpdb->get_results( $query );
		
		// Return array
		return $employers;
	}


	// Gathers and return an array of employer ( potentially for a select list )
	public static function getAllJobReadyEmployersForSelect()
	{
		$results = array();
		
		// Create a connection to the database
		global $wpdb;
		
		// Setup Query + qParams
		$query = 'SELECT *
					FROM ' . $wpdb->prefix . 'employer
				   WHERE id > 0
				ORDER BY legal_name';
		
		$employers = $wpdb->get_results( $query );
		
		// Loop through all employers
		foreach($employers as $employer)
		{
			$results[$employer->party_id] = $employer->id;
		}
		
		// Return array
		return $results;
	}


	// Populates the targetObj with the sourceRow's values
	public static function populateObject($sourceRow, &$targetObj)
	{
		$field_array = array(	'id',
								'job_ready_id',
								'party_id',
								'legal_name',
								'trading_name',
								'status_id');

		foreach($field_array as $field)
		{
			if(isset($sourceRow[$field]))
			{
				$targetObj->$field = $sourceRow[$field];
			}
		}
		return $targetObj;

	}
}
?>