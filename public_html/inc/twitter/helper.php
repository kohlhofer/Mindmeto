<?php

function debugDump() {

	echo '<pre>';
		print_r($_SESSION);
		print_r($_COOKIE);
		var_dump($session);
	echo '</pre>';

}

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

				header("Location: logout.php");
					
			}
				
			$userDetailsJSON = json_decode( $userDetails );

			$session->loggedIn = $session->createSession( $userDetailsJSON->id, $tok['oauth_token'], $tok['oauth_token_secret'] );
			if( !$session->loggedIn ) {
				
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

function convertDefaultTime( $userId, $timestamp ) {
	
	global $db;
	
	if( date('Hi', $timestamp) == '0000' ) {
		
		// This reminder is set to occur at a default time, so poll the database to find out when!
		$defaultTime = DEFAULT_REMINDER_TIME;
		$result = $db->query("SELECT user_default_time FROM ".DB_TBL_USERS." WHERE user_id='".$db->sanitize($userId)."'");
	
		if( $result->numRows() > 0 ) {
			$results = $result->getRow();
			$defaultTime = $results['user_default_time'];
		}
		
		return mktime($defaultTime, date("i", $timestamp), date("s", $timestamp), date("m", $timestamp), date("d", $timestamp), date("Y", $timestamp));
		
	}
	
	return false;

}

?>