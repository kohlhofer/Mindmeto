<?php

	define('MINDMETO_ROOT', 'public_html/');

	require(MINDMETO_ROOT.'inc/db.php');
	require(MINDMETO_ROOT.'inc/twitter/bot.php');

	$bot = new TwitterBot();

	switch( $argv[1] ) {
		
		case 'reminders':
		
			$bot->sendUpcomingReminders();
		
		break;
		case 'email':
		
			$bot->collectNewFollowers();
		
		break;
		case 'dm':
		
			$bot->collectNewDMs();
		
		break;
		case 'replies':
		
			$bot->collectNewReplies();
		
		break;
		case 'ticker':
		
			/* Fetch the latest reminder data, convert it to a JSON object
			   and then save it to a cache */
		
			$reminder = new Reminder();
			$options = new OptionsHandler();
			$reminderData = array();
			
			$lastId = $options->getValue('last_ticker_id');
			$latestPublicReminders = $reminder->fetchLatestPublic( $lastId );
			
			if( $latestPublicReminders ) {
				
				$i = 0; $latestId = $lastId;
				while( $reminderData = $latestPublicReminders->getRow() ) {
					
					if( $i == 0 ) $latestId = $reminderData['reminder_id'];
					$reminderData[$i] = $reminderData;
					$i++;
					
				}
				
			}
			
			$json = fopen(MINDMETO_ROOT.'cache/ticker.json', 'w');
			fwrite($json, json_encode( $reminderData ));
			fclose($json);
		
		break;
		
	}


?>