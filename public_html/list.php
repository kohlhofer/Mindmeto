<?php

define(MINDMETO_ROOT, '');

require_once(MINDMETO_ROOT.'inc/session.php');
require_once(MINDMETO_ROOT.'inc/oauth/twitterOAuth.php');
require_once(MINDMETO_ROOT.'inc/reminder.php');
require_once(MINDMETO_ROOT.'inc/twitter/bot.php');

$content = NULL;
$oauthState = $_SESSION['oauthState'];
$oauthSessionToken = $_SESSION['oauthRequestToken'];
$oauthToken = $_REQUEST['oauthToken'];
$section = $_REQUEST['section'];

if( !$session->loggedIn ) {
	
	if( isset($_REQUEST['oauth_token']) && $_SESSION['oauthState'] == 'start' ) $_SESSION['oauthState'] = $oauthState = 'returned';
	handleTwitterAuthentication( $oauthState );

}

if( $session->loggedIn ) {

	$bot = new TwitterBot();
	$reminders = $bot->reminder;
	
	if( isset( $_POST['command'] ) ) {

		$commandResponse = $bot->parseCommand( "web", $session->userDetails['user_twitter_data']->id, $_POST['command'] );
		$queryResult = NULL;

		if( $commandResponse !== false ) {
			
			$queryResult = $commandResponse;
			$session->userDetails = $db->fetchUserDetails( $session->userId );
			
		} else {
	
			$reminderResult = $bot->parseReminder( "web", $_POST['command'], $session->userId, -1 );
			if( $reminderResult !== false ) $queryResult = $reminderResult; 
			
		}

	}

	$existingReminders = $reminders->fetch( $session->userId );
	
	$headerCode = <<<JS
			<script>
				$(document).ready(function() {

					if( $('#reminder-web-result') ) {
						
						$('#reminder-web-result').effect("highlight", { 'color': '#fdef28' }, 1500);
						
					}

				});
			</script>
JS;
	
	include( 'tmp/account/reminders.php' );
	
} 

?>