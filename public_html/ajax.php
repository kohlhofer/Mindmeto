<?php

	define(MINDMETO_ROOT, '');
	require_once(MINDMETO_ROOT.'inc/db.php');

	switch( $_REQUEST['a'] ) {
		
		/* 
			Search the reminder table for the latest reminders set and feed them in JSON
			format for Javascript usage
		*/
		case "ticker":
		
			$newReminders = array();
			
			$id = ( isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) ? intval($_REQUEST['id']) : false;
			
			if( $id !== false ) {
				$results = $db->query("SELECT r.reminder_id, r.reminder_text, u.user_twitter_data FROM ".DB_TBL_REMINDERS." AS r, ".DB_TBL_USERS." AS u WHERE u.user_id = r.reminder_user_id AND reminder_id > ".$db->sanitize($id)." ORDER BY reminder_added_timestamp DESC LIMIT 10");
			} else {
				$results = $db->query("SELECT r.reminder_id, r.reminder_text, u.user_twitter_data FROM ".DB_TBL_REMINDERS." AS r, ".DB_TBL_USERS." AS u WHERE u.user_id = r.reminder_user_id ORDER BY reminder_added_timestamp DESC LIMIT 10");
			}
			
			$i = 0; $latestId = 0;
			if( $results->numRows() > 0 ) {
				
				while( $reminder = $results->getRow() ) {
					
					if( $i == 0 ) $latestId = $reminder['reminder_id'];
					
					$twitterData = unserialize($reminder['user_twitter_data']);
					
					if( $twitterData->screen_name !== NULL && $twitterData->profile_image_url ) {
					
						$newReminders['reminders'][$i]['username'] = $twitterData->screen_name;
						$newReminders['reminders'][$i]['avatar'] = $twitterData->profile_image_url;
						$newReminders['reminders'][$i]['reminder'] = $reminder['reminder_text'];
						$i++;
					
					}
					
				}
				
			}
			
			$newReminders['latestId'] = $latestId;
			echo json_encode($newReminders);
			
		break;
		
	}


?>