<?php

	define(MINDMETO_ROOT, '');
	function isAjax() {
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
	}
	
	/* This is an AJAX request, so serve some JSON headers */
	if( isAjax() ) {
		
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');
		
	}
	
	require_once(MINDMETO_ROOT.'inc/session.php');
	require_once(MINDMETO_ROOT.'inc/twitter/helper.php');

	switch( $_REQUEST['a'] ) {
		
		/*
			Handles the removal of a reminder from the web interface
		*/
		case "remove":
			
			$id = ( isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) ? intval($_REQUEST['id']) : false;
			
			$result = false;
			
			if( $id !== false && $session->loggedIn ) {
				
				$result = cancelReminder( $session->userId, $id );
				
			}
			
			if( !isAjax() ) {
				
				header("Location: list.php");
				
			} else {
				
				echo json_encode( $result );
				
			}
		
		break;
		
		/* 
			Search the reminder table for the latest reminders set and feed them in JSON
			format for Javascript usage
		*/
		case "ticker":
		
			$newReminders = array();
			
			$id = ( isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) ? intval($_REQUEST['id']) : false;
			
			if( $id !== false ) {
				$results = $db->query("SELECT r.reminder_id, r.reminder_full_text, u.user_twitter_data FROM ".DB_TBL_REMINDERS." AS r, ".DB_TBL_USERS." AS u WHERE u.user_id = r.reminder_user_id AND reminder_id > ".$db->sanitize($id)." ORDER BY reminder_added_timestamp DESC LIMIT 10");
			} else {
				$results = $db->query("SELECT r.reminder_id, r.reminder_full_text, u.user_twitter_data FROM ".DB_TBL_REMINDERS." AS r, ".DB_TBL_USERS." AS u WHERE u.user_id = r.reminder_user_id ORDER BY reminder_added_timestamp DESC LIMIT 10");
			}
			
			$i = 0; $latestId = 0;
			if( $results->numRows() > 0 ) {
				
				while( $reminder = $results->getRow() ) {
					
					if( $i == 0 ) $latestId = $reminder['reminder_id'];
					
					$twitterData = unserialize($reminder['user_twitter_data']);
					
					if( $twitterData->screen_name !== NULL && $twitterData->profile_image_url ) {
					
						$newReminders['reminders'][$i]['username'] = $twitterData->screen_name;
						$newReminders['reminders'][$i]['avatar'] = $twitterData->profile_image_url;
						$newReminders['reminders'][$i]['reminder'] = $reminder['reminder_full_text'];
						$i++;
					
					}
					
				}
				
			}
			
			$newReminders['latestId'] = $latestId;
			echo json_encode($newReminders);
			
		break;
		
	}


?>