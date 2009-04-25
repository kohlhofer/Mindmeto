<?php

	class Reminder {
		
		function Reminder() {}
		function fetch( $userId ) {
			
			global $db;
			
			$results = $db->query("SELECT * FROM ".DB_TBL_REMINDERS." WHERE reminder_user_id='".$db->sanitize($userId)."'");
			return $results;
			
		}
	    
		
		function add( $userId, $tweetId, $text, $timestamp, $recurring ) {
			
			global $db;

			$recurring = ($recurring == true) ? 1 : 0;
			
			$result = $db->query("SELECT reminder_id FROM ".DB_TBL_REMINDERS." WHERE reminder_tweet_id='".$db->sanitize($tweetId)."'");
			if( $result->numRows() < 1 ) {
			
				$db->query("INSERT INTO ".DB_TBL_REMINDERS." (reminder_id, reminder_user_id, reminder_tweet_id, reminder_text, reminder_timestamp, reminder_added_timestamp, reminder_recurring) VALUES ('', '".$db->sanitize($userId)."', '".$db->sanitize($tweetId)."', '".$db->sanitize($text)."', '".$db->sanitize(date('Y-m-d H:i:s', $timestamp))."', NOW(), '".$db->sanitize($recurring)."')");
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
			
			foreach( $contextFlags as $flag ) {
				
				// A bit of an ugly hack, here. We expand the query string to add an extra
				// whitespace to account for date contexts such as 'tomorrow' that often
				// appear at the end of a query string (and thus have no whitespace padding)
				$pos = strripos( $query.' ', ' '.$flag.' ' );
				
				if( $pos !== false ) {
					$newDate = substr( $query, $pos, strlen($query));
					$newDate = trim(str_replace( array(' at ', ' by ', ' and ', ' an ', ' in ', ' on ', ' one ', ' two ', ' three ', ' four ', ' five ', ' six ', ' seven ', ' eight ', ' nine ', ' ten ', ' twelve '), 
												 array(' ', ' ', ' ', ' 1 ', ' ',' ', ' 1 ', ' 2 ', ' 3 ', ' 4 ', ' 5 ', ' 6 ', ' 7 ', ' 8 ', ' 9 ', ' 10 ', ' 11 ', ' 12 '), 
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