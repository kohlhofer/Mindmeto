<?php

function convertDefaultTime( $userId, $timestamp ) {
	
	global $db;
	
	if( date('Hi', $timestamp) == '0000' ) {
		
		// This reminder is set to occur at a default time, so poll the database to find out when!
		$defaultTime = DEFAULT_REMINDER_TIME;
		$result = $db->query("SELECT user_default_time FROM ".DB_TBL_USERS." WHERE user_id='".$db->sanitize($userId)."'");
	
		if( $result->numRows() > 0 ) {
			$results = $result->getRow();
			$defaultTime = $results['user_default_time'];
		}
		
		return mktime($defaultTime, date("i", $timestamp), date("s", $timestamp), date("m", $timestamp), date("d", $timestamp), date("Y", $timestamp));
		
	}
	
	return false;

}

?>