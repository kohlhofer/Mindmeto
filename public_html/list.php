<?php

define(MINDMETO_ROOT, '');

require_once(MINDMETO_ROOT.'inc/session.php');
require_once(MINDMETO_ROOT.'inc/oauth/twitterOAuth.php');
require_once(MINDMETO_ROOT.'inc/reminder.php');

$content = NULL;
$oauthState = $_SESSION['oauthState'];
$oauthSessionToken = $_SESSION['oauthRequestToken'];
$oauthToken = $_REQUEST['oauthToken'];
$section = $_REQUEST['section'];

function handleTwitterAuthentication( $state ) {

	global $session;
	
	/*
	 * 'default': Get a request token from Twitter for new user
	 * 'returned': The user has authorized the app on Twitter and been returned
	 */
	switch ($state) {
	    case 'returned':

			$userDetailsJSON = NULL;
		
			try {
								
		      	$to = new TwitterOAuth(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, $_SESSION['oauthRequestToken'], $_SESSION['oauthRequestTokenSecret']);
		      	$tok = $to->getAccessToken();
				$userDetails = $to->OAuthRequest('https://twitter.com/account/verify_credentials.json', array(), 'GET');
					
			} catch ( Exception $e ) {
				
				header('Location: logout.php');
					
			}
				
			$userDetailsJSON = json_decode( $userDetails );

			if( !$session->createSession( $userDetailsJSON->id, $tok['oauth_token'], $tok['oauth_token_secret'] ) ) {

				header('Location: logout.php');
	
			}
			
	    break;
		default:

			try {

	    		$to = new TwitterOAuth(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET);
	    		$tok = $to->getRequestToken();
	
			} catch( Exception $e ) {

				header('Location: logout.php');
				
			}
			
			$_SESSION['oauthRequestToken'] = $token = $tok['oauth_token'];
    		$_SESSION['oauthRequestTokenSecret'] = $tok['oauth_token_secret'];
    		$_SESSION['oauthState'] = "start";

    		$oAuthRequestLink = $to->getAuthorizeURL($token);
	
			include( 'tmp/account/login.php' );

	    break;
	
	}
	
}

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
	
	$reminders = new Reminder();
	$existingReminders = $reminders->fetch( $session->userDetails['user_id'] );

	include( 'tmp/account/reminders.php' );
	
}

?>