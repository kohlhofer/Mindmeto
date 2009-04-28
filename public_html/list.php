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
	
	if( isset($_REQUEST['oauth_token']) && $_SESSION['oauthState'] === 'start' ) $_SESSION['oauthState'] = $oauthState = 'returned';
	handleTwitterAuthentication( $oauthState );

}

if( $session->loggedIn ) {

	$to = new TwitterOAuth(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, $session->userDetails['user_oauth_token'], $session->userDetails['user_oauth_token_secret'] );
	try {
			
		$userDetails = $to->OAuthRequest('https://twitter.com/account/verify_credentials.json', array(), 'GET');
			
	} catch ( Exception $e ) {

		header('Location: logout.php');
			
	}
		
	$userDetailsJSON = json_decode( $userDetails );
	$bot = new TwitterBot();
	$reminders = $bot->reminder;
	
	if( isset( $_POST['command'] ) ) {

		$commandResponse = $bot->parseCommand( $userDetailsJSON->id, $_POST['command'] );
		$queryResult = NULL;

		if( $commandResponse !== false ) {
			
			$queryResult = $commandResponse;
			$session->userDetails = $db->fetchUserDetails( $session->userId );
			
		} else {
	
			$reminderResult = $bot->parseReminder( $_POST['command'], $session->userId, -1 );
			if( $reminderResult !== false ) $queryResult = $reminderResult; 
			
		}

	}

	$existingReminders = $reminders->fetch( $session->userDetails['user_id'] );

	include( 'tmp/account/reminders.php' );
	
} 

?>