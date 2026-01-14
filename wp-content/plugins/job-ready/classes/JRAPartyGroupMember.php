<?php
/*
 * JRAPartyGroupMember class + JRAPartyGroupMemberOperations class
 * Created by: James Witika
 * Company: Smooth Developments
 */

if(!defined('JR_ROOT_FILE')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class JRAPartyGroupMember
{
	var $group_type; 	// reference A valid party group type
	var $name; 			//reference A valid party group value
	
	function __construct()
	{
		$this->group_type = ''; // reference A valid party group type
		$this->name = ''; 		// reference A valid party group value
	}
}


class JRAPartyGroupMemberOperations
{
	function __construct()
	{

	}
	

	// Create XML layout for all Party Group Members
	static function createJRAPartyGroupMemberXML( $party_group_members )
	{
		$xml = '<party-group-members>';
		
		foreach($party_group_members as $party_group_member)
		{
			$xml .= '<party-group-member>
							<group-type>'.$party_group_member->group_type.'</group-type>
							<name>'.$party_group_member->name.'</name>
					 </party-group-member>';
		}
		
		$xml .= '</party-group-members>';
		
		return $xml;
	}
}