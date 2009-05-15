<?php

	class Reminder {
		
		function Reminder() {}
		function fetch( $userId ) {
			
			global $db;
			
			$results = $db->query("SELECT * FROM ".DB_TBL_REMINDERS." WHERE reminder_user_id='".$db->sanitize($userId)."' AND reminder_sent=0 ORDER BY reminder_timestamp ASC");
			return $results;
			
		}
		
		function fetchLatestPublic( $id ) {
			
			global $db;
			
			$id = (isset($id) && is_numeric($id)) ? intval($id) : false;
			
			if( $id !== false ) {
				$results = $db->query("SELECT r.reminder_id, r.reminder_full_text, u.user_twitter_data FROM ".DB_TBL_REMINDERS." AS r, ".DB_TBL_USERS." AS u WHERE r.reminder_public = 1 AND  u.user_id = r.reminder_user_id AND r.reminder_id > ".$db->sanitize($id)." ORDER BY r.reminder_added_timestamp DESC LIMIT 6");
			} else {
				$results = $db->query("SELECT r.reminder_id, r.reminder_full_text, u.user_twitter_data FROM ".DB_TBL_REMINDERS." AS r, ".DB_TBL_USERS." AS u WHERE r.reminder_public = 1 AND u.user_id = r.reminder_user_id ORDER BY r.reminder_added_timestamp DESC LIMIT 6");
			}
			
			if( $results->numRows() > 0 ) return $results;
			
			return false;
			
		}
		
		function fetchLatestPublicHTML( $id = false ) {
			
			$latestPublicReminders = $this->fetchLatestPublic( $id );
			$publicRemindersHTML = "";

			if( $latestPublicReminders ) {

				$i = 0;

				while( $publicReminder = $latestPublicReminders->getRow() ) {

					$twitterData = unserialize( $publicReminder['user_twitter_data'] );
					$publicRemindersHTML .= '<li';

					if( $i % 2 != 0 ) $publicRemindersHTML .= ' class="odd"';

					$publicRemindersHTML .= '><div class="reminder">'.	
												'<b>@mindmeto</b> '.autolinkUrls($publicReminder['reminder_full_text']).
											'</div>'.
											'<div class="reminder-meta">'.
												'<a href="http://twitter.com/'.$twitterData->screen_name.'"><img src="'.$twitterData->profile_image_url.'" class="avatar" alt="'.$twitterData->screen_name.'"  /> '.$twitterData->screen_name.'</a>'.
											'</div>'.
										'</li>';

					$i = ( $i ) ? 0 : 1;

				}

			}
			
			return $publicRemindersHTML;
			
		}
	    
		// Passing a tweetId of value -1 means we're adding the reminder via the web interface
		function add( $userId, $tweetId, $text, $fulltext, $context, $timestamp, $public = 0 ) {
			
			global $db;
			
			if( $tweetId > -1 ) $result = $db->query("SELECT reminder_id FROM ".DB_TBL_REMINDERS." WHERE reminder_tweet_id='".$db->sanitize($tweetId)."'");
			if( $tweetId == -1 || $result->numRows() < 1 ) {
			
				$db->query("INSERT INTO ".DB_TBL_REMINDERS." (reminder_id, reminder_user_id, reminder_tweet_id, reminder_text, reminder_full_text, reminder_context_flag, reminder_timestamp, reminder_added_timestamp, reminder_public ) VALUES ('', '".$db->sanitize($userId)."', '".$db->sanitize($tweetId)."', '".$db->sanitize($text)."', '".$db->sanitize($fulltext)."', '".$db->sanitize($context)."', '".$db->sanitize(date('Y-m-d H:i:s', $timestamp))."', NOW(), '".$db->sanitize($public)."' )");
				return true;
				
			}
			
			return false;
			
		}
		
		function parse( $query ) {
			
			$query = trim($query);
			$queryDetails = array();
			
			return $this->findDateContext( $query );
			
		}
		
		/*
			findDateContext: convert human readable dates into epoch timestamps
			Returns array on success, false on failure
		*/
		function findDateContext( $query ) {

			$contextFlags = array('in', 'on', 'by', 'next', 'every', 'tomorrow', 'at');
			$possibleDates = array();
			$i = 0;
			
			$query = trim($query);
			$query = rtrim( $query, "@.!?;:-=*&^%£@+ ()" );
			
			foreach( $contextFlags as $flag ) {
				
				// A bit of an ugly hack, here. We expand the query string to add an extra
				// whitespace to account for date contexts such as 'tomorrow' that often
				// appear at the end of a query string (and thus have no whitespace padding)
				$pos = strripos( $query.' ', ' '.$flag.' ' );
				
				if( $pos !== false ) {
					$newDate = substr( $query, $pos, strlen($query));
					$newDate = trim(str_replace( array(' the ', ' at ', ' by ', ' and ', ' an ', ' in ', ' on ', ' one ', ' two ', ' three ', ' four ', ' five ', ' six ', ' seven ', ' eight ', ' nine ', ' ten ', ' twelve '), 
												 array(' ', ' ', ' ', ' ', ' 1 ', ' ',' ', ' 1 ', ' 2 ', ' 3 ', ' 4 ', ' 5 ', ' 6 ', ' 7 ', ' 8 ', ' 9 ', ' 10 ', ' 11 ', ' 12 '), 
												 $newDate));
					$epoch = strtotime( $newDate );
					
					if( $epoch !== false ) {
						$possibleDates[$i]['reminder'] = substr( $query, 0, $pos );
						$possibleDates[$i]['flag'] = $flag;
						$possibleDates[$i]['epoch'] = $epoch;
						$possibleDates[$i]['recurring'] = ($flag == "every") ? true : false;
						$i++;
					}
				} 
				
			}
			
			if( count( $possibleDates ) > 0 ) return $possibleDates[0];

			return false;
			
		}
		
	};

?>