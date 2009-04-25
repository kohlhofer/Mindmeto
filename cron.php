<?php

	define('MINDMETO_ROOT', 'public_html/experiments/mindmeto/');

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
		
	}


?>