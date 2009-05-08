<?php

	require_once(MINDMETO_ROOT.'inc/oauth/twitterOAuth.php');
	require_once(MINDMETO_ROOT.'inc/twitter/helper.php');
	require_once(MINDMETO_ROOT.'inc/twitter/options.php');
	require_once(MINDMETO_ROOT.'inc/reminder.php');

	class TwitterBot { 
		
		var $twitterOAuth, $reminder;
		
		function TwitterBot() {
			
			$this->reminder = new Reminder();
			$this->twitterOAuth = new TwitterOAuth(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_TOKEN_SECRET );
			
		}

		function getUserSetting( $userId, $setting ) {
			
			global $db;
			
			$result = $db->query("SELECT ".$setting." AS value FROM ".DB_TBL_USERS." WHERE user_id='".$db->sanitize($userId)."'");
			if( $result->numRows() > 0 ) {
				$data = $result->getRow();
				return $data['value'];
			}
			
			return false;
			
		}
		function updateUserSettings( $userId, $setting, $value ) {
			
			global $db;
			
			$result = $db->query("SELECT ".$setting." FROM ".DB_TBL_USERS." WHERE user_id='".$db->sanitize($userId)."'");
			if( $result->numRows() > 0 ) {
				
				$db->query("UPDATE ".DB_TBL_USERS." SET ".$setting."='".$db->sanitize($value)."' WHERE user_id='".$db->sanitize($userId)."'");
				
			} else {
				
				$db->query("INSERT INTO ".DB_TBL_USERS." (user_id, ".$setting.") VALUES ('".$db->sanitize($userId)."', '".$db->sanitize($value)."')");
				
			}
			
			return false;
			
		}
		function sendUpcomingReminders() {
			
			global $db;

			// Remove reminders older than a day
			$result = $db->query("SELECT reminder_id FROM ".DB_TBL_REMINDERS." WHERE reminder_timestamp < DATE_SUB(NOW(), INTERVAL 1 DAY)");
								
			$sql = "SELECT r.* , u.user_timezone, u.user_default_time, u.user_id ".
				 "FROM ".DB_TBL_REMINDERS." AS r ".
				 "LEFT JOIN ".DB_TBL_USERS." AS u ON u.user_id = r.reminder_user_id ".
				 "WHERE (".
					"(".
						"DATE_FORMAT( r.reminder_timestamp, '%H:%i' ) != '00:00' ".
						"AND DATE_ADD( CONVERT_TZ( NOW(), '+00:00', IFNULL(u.user_timezone, '+00:00') ), INTERVAL ".REMINDER_PERIOD." MINUTE ) > r.reminder_timestamp ".
					")".
				") OR (".
					"(".
						"DATE_FORMAT( r.reminder_timestamp,  '%H:%i' ) = '00:00' ".
						"AND DATE( r.reminder_timestamp ) <= DATE( CONVERT_TZ( NOW(), '+00:00', IFNULL( u.user_timezone, '+00:00' ) ) ) ".
						"AND DATE_FORMAT( CONVERT_TZ( NOW() , '+00:00', IFNULL(u.user_timezone, '+00:00') ), '%H:%i' ) >= IFNULL( u.user_default_time, '8' ) + ':00' ".
					")".
				")".
				"ORDER BY CONVERT_TZ( r.reminder_timestamp, '+00:00', IFNULL(u.user_timezone, '+00:00') ) ASC";

			$result = $db->query( trim($sql) );

			while( $reminder = $result->getRow() ) {

				$reminderText = $reminder['reminder_text'].' | See all reminders: http://mindmeto.com/list.php';
				if( $this->sendDM( $reminder['reminder_user_id'], $reminderText, TWITTER_DM_REMINDER ) ) {
					
					$db->query("DELETE FROM ".DB_TBL_REMINDERS." WHERE reminder_id='".$db->sanitize($reminder['reminder_id'])."'");
					
				}
				
			}
			
		}
		
		// Send a DM from the MindMeTo bot
		function sendDM( $id, $message, $type=TWITTER_DM_CONFIRMATION ) {
		
			if( $id != TWITTER_ID &&
				(( $type == TWITTER_DM_CONFIRMATION && $this->getUserSetting( $id, 'user_allow_confirmations' ) !== 0 ) || 
				( $type == TWITTER_DM_REMINDER && $this->getUserSetting( $id, 'user_allow_reminders' ) !== 0 )) ) {
		
				try {
				
					$data = array('user' => $id, 'text' => $message );
					$this->twitterOAuth->OAuthRequest('https://twitter.com/direct_messages/new.json', $data, 'POST');
				
				} catch ( Exception $e ) {
				
					return false;
				
				}
			
				return true;
				
			}
			
			return false;
			
		}
		
		// Delete a Direct Message. Returns true on success
		function deleteDM( $id ) {
			
			try {
				
				$this->twitterOAuth->OAuthRequest('https://twitter.com/direct_messages/destroy/'.$id.'.json', array(), 'POST');
				
			} catch ( Exception $e ) {
				
				return false;
				
			}
			
			return true;
			
		}
		
		function parseCommand( $userId, $command ) {
			
			global $db;
			
			$commandParsed = true;
			$command = trim(strtolower($command));
			
			if( substr( $command, 0, 12) == "timezone gmt" ) {	
				
				$timezone = trim(substr( $command, 12, strlen($command) ));
				
				if( trim($command) == "timezone gmt" ) $timezone = 0;
				if( is_numeric( $timezone ) ) {
					
					$dmTimezone = (intval($timezone) >= 0) ? '+'.$timezone : $timezone;
					$finalTimezone = (intval($timezone) >= 0) ? '+'.$timezone.':00' : $timezone.':00';
				
					$this->updateUserSettings( $userId, 'user_timezone', $finalTimezone );
					return("All done! Your timezone has been set to GMT$dmTimezone" );
					
				} else {
					
					return("Whoops! You must provide a numeric value (number of hours relative to GMT) to set a timezone." );
					
				}

			} else if( substr( $command, 0, 12) == "default time" ) {
				
				$defaultTime = trim(substr($command, 13, strlen($command)));
				
				if( is_numeric( $defaultTime ) && $defaultTime >= 0 && $defaultTime <= 23 ) {
					
					$this->updateUserSettings( $userId, 'user_default_time', $defaultTime );
					return("All done! Your default reminder time has been set to ".$defaultTime.".");
					
				} else {
					
					return("Whoops! You must provide a numeric value (0 to 23) to set a default reminder time.");
					
				}

			} else if( $command == "list reminders" ) {
				
				return("Visit http://mindmeto.com/list.php to view your reminders.");

			} else if( substr( $command, 0, 8) == "cancel #" ) {
				
				$id = trim(substr( $command, 8, strlen( $command )));
				
				if( is_numeric( $id ) ) {
				
					$db->query("DELETE FROM ".DB_TBL_REMINDERS." WHERE reminder_user_id='".$db->sanitize($userId)."' AND reminder_id='".$db->sanitize($id)."'");
				
					$result = $db->query("SELECT ROW_COUNT() AS num_reminders_deleted");
					$results = $result->getRow();
				
					if( $results['num_reminders_deleted'] > 0 ) {
				
						return("All done! The reminder with ID #$id has been removed.");
				
					} else {
						
						return("There was a problem deleting the reminder you specified.");
						
					}
				
				} else {
					
					return("You must provide a numeric ID to delete a reminder. If you need to check a reminders ID, visit http://mindmeto.com/list.php");
					
				}

			} else if( $command == "confirmations on" ) {
				
				$this->updateUserSettings( $userId, 'user_allow_confirmations', 1 );
				return("All done! You have now turned Direct Message confirmations on.");
				
			} else if( $command == "confirmations off" ) {
				
				$this->updateUserSettings( $userId, 'user_allow_confirmations', 0 );
				return("All done! You have now turned Direct Message confirmations off (this will be your last one).");

			} else if( $command == "reminders on" ) {
				
				$this->updateUserSettings( $userId, 'user_allow_reminders', 1 );
				return("All done! You have now turned Direct Message reminders on.");
				
			} else if( $command == "reminders off" ) {
				
				$this->updateUserSettings( $userId, 'user_allow_reminders', 0 );
				return("All done! You have now turned Direct Message reminders off");

			} else {
				
				$commandParsed = false;
				
			}
			
			return $commandParsed;
			
		}
		function parseReminder( $type, $command, $userId, $twitterId ) {
			
			global $db;
			
			$reminderData = $this->reminder->parse( $command );
	
			if( is_array($reminderData) ) {
		
				// Make sure the user isn't setting a reminder in the past
				$reminderSetInFuture = ( $reminderData['epoch'] > time() ) ? true : false;
				$public = ( $type == "replies" ) ? 1 : 0;
		
				if( $reminderSetInFuture && $this->reminder->add( $userId, $twitterId, $reminderData['reminder'], $command, $reminderData['epoch'], $public ) ) {
			
					// Send a successful DM!
					$reminderId = $db->fetchLastInsertId();
				
					$convertedTimestamp = convertDefaultTime( $userId, $reminderData['epoch'] );
					$reminderData['epoch'] = ( $convertedTimestamp !== false ) ? $convertedTimestamp : $reminderData['epoch'];
					return( "Done! Your reminder (ID #$reminderId) has been set for ".date('l jS \of F Y h:i:s A', $reminderData['epoch']). " | See other reminders at http://mindmeto.com/list.php" );
			
				} else {
					
					return( "Whoops! There was a problem setting your reminder. If this problem persists, please get in touch!" );
					
				}
				
				// Now we delete the DM to save on space
				if( TWITTER_DELETE_DMS && $twitterId > -1 ) $this->deleteDM( trim( $twitterId ) );
		
			} else {
		
				// The reminder was not recognized. Send the user a regretful DM
				return ( "Whoops! I do not understand the command/reminder you just sent: ".$command );
		
			}
			
			return false;
			
		}
		
		function parseTweets( $type, $updates, $currentLastId ) {
			
			global $db;
			
			$firstReminder = true;
			$newLastId = $currentLastId;
			
			if( is_array( $updates ) && count( $updates ) > 0 ) {
			
				foreach( $updates as $update ) {
					
					// Grab the latest tweets ID
					if( $firstReminder == true ) {
					
						$newLastId = trim($update->id);
						$firstReminder = false;
					
					}
				
					$userId = ($type == "dm") ? trim($update->sender_id) : trim($update->user->id);
					if( $type == "dm" ) $commandResponse = $this->parseCommand( $userId, $update->text );

					// Handle configuration commands
					if( $type == "dm" && $commandResponse !== false ) {
						
						$this->sendDM( $userId, $commandResponse );
						
					} else {
				
						// Get rid of the @mindmeto's in messages
						if( $type != "dm" ) {
							$update->text = trim(substr($update->text, strripos($update->text, "@mindmeto")+10, strlen($update->text) ));
						}
	
						$reminderResult = $this->parseReminder( $type, $update->text, $userId, $update->id );
						if( $reminderResult !== false ) $this->sendDM( $userId, $reminderResult );
						
					}
					
					if( $type == "dm" ) {
						$this->updateUserSettings( $userId, 'user_twitter_data', serialize($update->sender) );
					} else if( $type == "replies" ) {
						$this->updateUserSettings( $userId, 'user_twitter_data', serialize($update->user) );
					}
			
				}
				
			}
			
			return $newLastId;
			
		}
		
		function collectNewReplies() {

			$options = new OptionsHandler();
			$currentLastId = trim($options->getValue('last_reply_id'));

			try {
				
				if( $currentLastId > 0 ) {
					$data = array('since_id'=>$currentLastId, 'count'=>'200');
				} else {
					$data = array('count'=>'200');
				}
				$updates = $this->twitterOAuth->OAuthRequest('https://twitter.com/statuses/mentions.json', $data, 'GET');
	
			} catch ( Exception $e ) {
				
				die("There was a problem grabbing MindMeTo's @replies (".$e->getMessage().")");
				
			}
			
			$updatesJSON = json_decode($updates);
			
			if( count( $updatesJSON ) > 0 ) {
				
				$lastId = $this->parseTweets( "replies", $updatesJSON, $currentLastId );
				$options->setValue('last_reply_id', $lastId);
				
			}
			
		}
		function collectNewDMs() {
			
			global $db;
			
			$options = new OptionsHandler();
			$currentLastId = trim($options->getValue('last_dm_id'));
			
    		try {
				
				if( $currentLastId > 0 ) {
					$data = array('since_id'=>$currentLastId, 'count'=>'200');
				} else {
					$data = array('count'=>'200');
				}
				$updates = $this->twitterOAuth->OAuthRequest('https://twitter.com/direct_messages.json', $data, 'GET');
	
			} catch ( Exception $e ) {
				
				die("There was a problem grabbing MindMeTo's DMs (".$e->getMessage().")");
				
			}
			
			$updatesJSON = json_decode($updates);

			if( count( $updatesJSON ) > 0 ) {
				
				$lastId = $this->parseTweets( "dm", $updatesJSON, $currentLastId );
				$options->setValue('last_dm_id', $lastId);
				
			}
			
		}
		
		function collectNewFollowers() {

			$imapConnection = imap_open ("{".MAIL_SERVER."/pop3/notls}INBOX", MAIL_USERNAME, MAIL_PASSWORD) or die("There was a problem connecting to the MailMeTo email server: (". imap_last_error().")");
			$imapHeaders = @imap_headers($imapConnection) or die(); //die("There was a problem fetching mail from the MailMeTo email server (".imap_last_error().")");
		
			$numEmails = sizeof($imapHeaders); 
			
			for($i = 1; $i < $numEmails+1; $i++) { 

				$removeEmail = false;

				$rawHeader = $this->generateEmailHeaderArray( imap_fetchheader($imapConnection, $i) );
				
				if( isset( $rawHeader['X-Twitteremailtype'] ) && isset( $rawHeader['X-Twittersenderid'] ) && trim($rawHeader['X-Twitteremailtype']) == 'is_following' ) {

					$to = new TwitterOAuth(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_TOKEN_SECRET );
		    		try {
				
						$to->OAuthRequest('https://twitter.com/friendships/create/'.trim($rawHeader['X-Twittersenderid']).'.json', array(), 'POST');
						$removeEmail = true;
			
					} catch ( Exception $e ) {
						
						$removeEmail = false;
						
					}
					
				} else { 
					
					$removeEmail = true;
					
				}
				
				if( $removeEmail === true ) {
					
					@imap_delete($imapConnection, $i); 
					
				}

			}
			
			@imap_expunge($imapConnection);
			
		}
		
		// Helper function to grab non-traditional email headers (such as the ones Twitter sends)
		function generateEmailHeaderArray($header) {
			
			$header_array = explode("\n", rtrim($header));
			$header_array = array_filter($header_array);

			$new_header_array = array();
			foreach ($header_array as $key => $line) {

				if (preg_match('/^([^:\s]+):\s(.+)/',$line,$m)) {

					$current_header = $m[1];
					$current_data = $m[2];
					
					if (!isset($new_header_array[$current_header])) {
						$new_header_array[$current_header] = $current_data;
					} else {
						
						if (!is_array($new_header_array[$current_header])) {
							
							$new_header_array[$current_header] = array($new_header_array[$current_header],$current_data);
								
						} else {
							$new_header_array[$current_header][] = $current_data;
						}
					}
				} else {
					
					if (is_array($new_header_array[$current_header])) {
						$new_header_array[$current_header][count($new_header_array[$current_header])-1] .= $line;
					} else {
						$new_header_array[$current_header] .= $line;
					}
					
				}
			}

			return $new_header_array;
			
		}

		
	};

?>