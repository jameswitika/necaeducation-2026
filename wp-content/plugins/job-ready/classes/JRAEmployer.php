<?php
/*
 * JRAEmployer class + JRAEmployerOperations class
 * Created by: James Witika
 * Company: Smooth Developments
 */

if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class JRAEmployer
{
	var $id;
	var $party_identifies;
	var $party_type;
	var $legal_name;
	var $trading_name;
	var $created_on;
	var $updated_on;
	
	function __construct()
	{
		$this->table_name = ''; // string		The unique ID (see above)
	}
}

class JRAEmployerOperations
{
	function __construct()
	{
		
	}
	
	
	static function getJRAEmployer( $party_id )
	{
		global $jr_api_headers;
		
		$webservice = '/webservice/party/' . $party_id;
		$url = JR_API_SERVER . $webservice;
		$method = 'GET';
		
		// Call the Job Ready API
		try {
			
			//make POST request
			$response = wp_remote_request(	$url,
					array(	'method' 	=> $method,
							'headers' 	=> $jr_api_headers,
							'timeout' 	=> 500 )
					);
			
			// Get the response
			$result = wp_remote_retrieve_body( $response );
			return $result;
		}
		catch (Exception $e)
		{
			echo "Error: " . print_r($e, true) . "<br/>";
			return false;
		}
	}
	
	
	
	static function getJRAEmployers( $args = array() )
	{
		global $jr_api_headers;
		
		$webservice = '/webservice/party/?party_type=Employer&updated_since=' . $args['last_update'] . '&offset=' . $args['offset'] . '&limit=' . $args['limit'];
		$url = JR_API_SERVER . $webservice;
		$method = 'GET';
		
		// Call the Job Ready API
		try {
			
			//make POST request
			$response = wp_remote_request(	$url,
					array(	'method' 	=> $method,
							'headers' 	=> $jr_api_headers,
							'timeout' 	=> 500 )
					);
			
			// Get the response
			$result = wp_remote_retrieve_body( $response );
			return $result;
		}
		catch (Exception $e)
		{
			echo "Error: " . print_r($e, true) . "<br/>";
			return false;
		}
	}
	

	static function getJRAEmployersCount( $last_update )
	{
		global $jr_api_headers;
		
		$webservice = '/webservice/party/?party_type=Employer&updated_since=' . $last_update;
		$url = JR_API_SERVER . $webservice;
		$method = 'GET';
		
		// Call the Job Ready API
		try {
			
			//make POST request
			$response = wp_remote_request(	$url,
					array(	'method' 	=> $method,
							'headers' 	=> $jr_api_headers,
							'timeout' 	=> 500 )
					);
			
			// Get the response
			$result = wp_remote_retrieve_body( $response );

			$xml = simplexml_load_string($result);
			foreach($xml->attributes() as $k => $v);
			{
				if($k == 'total')
				{
					$total = $v;
				}
			}
			
			//echo "Total Records: " . $total . "<br/>";
			
			return $total;
		}
		catch (Exception $e)
		{
			echo "Error: " . print_r($e, true) . "<br/>";
			return false;
		}
		
	}
	
	
	static function syncEmployers()
	{
		$last_employer_sync = get_option('last_employer_sync');
		$last_sync = date('Y-m-d', strtotime($last_employer_sync));
		
		$total = JRAEmployerOperations::getJRAEmployersCount($last_sync);
		
		// Gets an array of all existing employers in the DB
		$existing_employers_array = JobReadyEmployerOperations::getAllJobReadyEmployersForSelect();
		//echo "Existing Count: " . count($existing_employers_array) . "<br/>";
		
		$limit = 100;
		$update_count = 0;
		$create_count = 0;
		
		for($offset=0; $offset<= $total; $offset+= 100)
		{
			//echo "Offset: " . $offset . " to " . ($offset + $limit) . "<br/>";
			$args = array(	'last_update'	=> $last_sync,
							'offset'		=> $offset,
							'limit'			=> $limit );
			$result = JRAEmployerOperations::getJRAEmployers($args);
			
			// Convert the XML to an Object
			$employers= xmlToObject($result);
			
			foreach($employers as $employer)
			{
				$neca_employer = new JobReadyEmployer();
				
				$neca_employer->job_ready_id = (int) $employer->id;
				$neca_employer->party_id = (string) $employer->{'party-identifier'};
				$neca_employer->legal_name = (string) $employer->{'legal-name'};
				$neca_employer->trading_name = (string) $employer->{'trading-name'};
				$neca_employer->status_id = 'AC';
				
				if(array_key_exists($neca_employer->party_id, $existing_employers_array))
				{
					$neca_employer->id = $existing_employers_array[$neca_employer->party_id];
					$update_count++;
				}
				else
				{
					$create_count++;
				}

				// Save the NECA Employer to the DB
				$neca_employer->save();
			}
		}
		
		// Send an email to the administrator
		$subject = 'NECA Education + Careers - Employers Sync successful';
		$body_content = "	<h1>JobReady Employers Sync</h1>
		Update Count: " . $update_count . "<br/>
		Create Count: " . $create_count . "<br/>";
		
		//echo $body_content;
		
		wp_mail('james@smoothdevelopments.com.au', $subject, $body_content);
		
		
		// Set the last_sync date
		update_option( 'last_employer_sync', date("Y-m-d") );
	}
	

	
}