<?php
/*
 * JRAPartyDocument class + JRAPartyDocumentOperations
 * Created by: James Witika
 * Company: Smooth Developments
 */

if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class JRAPartyDocument
{
	var $id; 					// integer		Read-only
	var $party_id;				// string		Party Identifier
	var $name; 					// string
	var $description; 			// string 		Mandatory - The party identifier of a valid party.
	var $document_category;		// reference 	Mandatory – Options: (General, Personal, etc.)
	var $document_type; 		// reference
	var $order_by; 				// integer
	var $online; 				// boolean
	var $web_public; 			// boolean
	var $filename; 				// string		File contents for upload as multipart/form-data
	var $url; 					// string		URL where document file can be downloaded
	
	function __construct()
	{
		$this->id = ''; 									// integer		Read-only
		$this->party_id = '';								// string		Party Identifier
		$this->name = ''; 									// string
		$this->description = ''; 							// string 		Mandatory - The party identifier of a valid party.
		$this->document_category = 'Application Form';		// reference 	Mandatory – Options: (General, Personal, etc.)
		$this->document_type = 'Resources';					// reference
		$this->order_by = ''; 								// integer
		$this->online = ''; 								// boolean
		$this->web_public = false; 							// boolean
		$this->filename = ''; 								// string		File contents for upload as multipart/form-data
		$this->url = ''; 									// string		URL where document file can be downloaded
	}
}

class JRAPartyDocumentOperations
{
	function __construct()
	{
		
	}
	
	
	static function createJRAPartyDocument($party_id, $document)
	{
		global $jr_api_headers;
		
		$webservice = '/webservice/parties/' . $party_id . '/documents/';
		$url = JR_API_SERVER . $webservice;
		$method = 'POST';
		
		$local_file = $document->filename;
		$post_fields = array(	'document[name]'		=> $document->name,
								'document[description]' => $document->description,
								'document[document-type]' => $document->document_type,
								'document[document-category]' => $document->document_category );
		
		$boundary = wp_generate_password( 24 );
		
		// Use the default Job Ready Headers
		$headers = $jr_api_headers;
		
		// Unset the Content Type
		unset($headers['Content-Type']);
		
		// Create a new Content Type
		$headers['Content-Type'] = 'multipart/form-data; boundary=' . $boundary;
		
		// Setup the payload
		$payload = '';

		// First, add the standard POST fields:
		foreach ( $post_fields as $name => $value )
		{
			$payload .= '--' . $boundary;
			$payload .= "\r\n";
			$payload .= 'Content-Disposition: form-data; name="' . $name .
			'"' . "\r\n\r\n";
			$payload .= $value;
			$payload .= "\r\n";
		}

		// Upload the file
		if ( $local_file ) {
			$payload .= '--' . $boundary;
			$payload .= "\r\n";
			$payload .= 'Content-Disposition: form-data; name="' . 'document[filename]' .
					'"; filename="' . basename( $local_file ) . '"' . "\r\n";
			$payload .= 'Content-Type: application/pdf' . "\r\n";
			$payload .= "\r\n";
			$payload .= file_get_contents( $local_file );
			$payload .= "\r\n";
		}
		$payload .= '--' . $boundary . '--';
		
		//var_dump($payload);
		
		$args = array(	'method'	=> $method,
						'headers'	=> $headers,
						'body'		=> $payload,
						'timeout'	=> 500
						);
 		
		// Call the Job Ready API
		try {
			
			//make POST request
			$response = wp_remote_post( $url, $args );
			
			// Get the response
			$result = wp_remote_retrieve_body( $response );
			
			// Convert the XML to an Object
			// Access attribute with '-' hyphens using this syntax: "$result_object->{'party-identifier'}"
			$result_object = xmlToObject($result);
			
			return $result_object;
		}
		catch (Exception $e)
		{
			$error = $e->getMessage();
			send_error_email($url, $method, $xml, $error);
			return false;
		}
	}
	
	
	static function createJRAPartyDocumentXML( $document )
	{
		$xml = '	<document>
				        <name>'.htmlspecialchars($document->name, ENT_XML1).'</name>
				        <description>'.htmlspecialchars($document->description, ENT_XML1).'</description>
				        <document-category>'.$document->document_category.'</document-category>
				        <document-type>'.$document->document_type.'</document-type>
				        <online>0</online>
				        <web-public>0</web-public>';
		if(isset($document->filename) && $document->filename != '')
		{
			$xml .= '		<filename>'.$document->filename.'</filename>';
		}
		
		if(isset($document->url) && $document->url != '')
		{
			$xml .= '		<url>'.$document->url.'</url>';
		}
		
		$xml .= '	</document>';

		return $xml;
	}
}