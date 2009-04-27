<?php

	require_once(MINDMETO_ROOT.'db.php');

	class Session {
		
		var $loggedIn = false, $userDetails = NULL, $userId = NULL, $userSessionId = NULL;
		
		function Session() { 
		
			session_start();  
			$this->loggedIn = $this->verifySession();
		
		}
		
		function verifySession() {
		
			global $db; 

      		if( isset( $_COOKIE['userId'] ) && strlen( $_COOKIE['userId'] ) > 0 && 
				isset( $_COOKIE['userSessionId'] ) && strlen( $_COOKIE['userSessionId'] ) > 0 ) {

         		$this->userId = $_SESSION['userId'] = $_COOKIE['userId'];
         		$this->userSessionId = $_SESSION['userSessionId'] = $_COOKIE['userSessionId'];

      		}

      		if( isset( $_SESSION['userId'] ) && strlen( $_SESSION['userSessionId'] ) > 0 &&
 				isset( $_SESSION['userSessionId'] ) && strlen( $_SESSION['userSessionId'] ) > 0 ) {

         		if( !$db->confirmSessionId( $_SESSION['userId'], $_SESSION['userSessionId'] ) ) {

            		unset($_SESSION['userId']);
            		unset($_SESSION['userSessionId']);

            		return false;

         		}

         		$this->userDetails = $db->fetchUserDetails( $_SESSION['userId'] );

         		return true;

      		}
      		return false;
		
		}
		
		function createSession( $userId, $token, $secret ) {
		
			global $db;

			if( strlen($userId) > 0 && strlen($token) > 0 && strlen($secret) > 0 ) {

				$userId = trim( $userId );

				$this->userId = $_SESSION['userId'] = $userId;
      			$this->userSessionId = $_SESSION['userSessionId'] = $this->generateRandomID();
			
				$result = $db->query("SELECT user_id FROM ".DB_TBL_USERS." WHERE user_id='".$db->sanitize($userId)."'");
				if( $result->numRows() > 0 ) {
			
					// This is an existing user, so we update their details
					$db->query("UPDATE ".DB_TBL_USERS." SET user_session_id='".$db->sanitize($this->userSessionId)."', user_oauth_token='".$db->sanitize($token)."', user_oauth_token_secret='".$db->sanitize($secret)."' WHERE user_id='".$db->sanitize($userId)."'");

				} else {
			
					// This is a new user
					$db->query("INSERT INTO ".DB_TBL_USERS." (user_id, user_oauth_token, user_oauth_token_secret, user_session_id) VALUES ('".$db->sanitize($userId)."', '".$db->sanitize($token)."', '".$db->sanitize($secret)."', '".$db->sanitize($this->userSessionId)."')");

				}

				setcookie( "userId", $this->userId, time()+COOKIE_EXPIRE, COOKIE_PATH, ".mindmeto.com" );
         		setcookie( "userSessionId", $this->userSessionId, time()+COOKIE_EXPIRE, COOKIE_PATH, ".mindmeto.com" );
				$this->userDetails = $db->fetchUserDetails( $this->userId );

				return true;
         	
         	}

         	return false;
			
		}
		
		function logout(){

      		setcookie( "userId", NULL, time()-COOKIE_EXPIRE, COOKIE_PATH, ".mindmeto.com" );
     		setcookie( "userSessionId", NULL, time()-COOKIE_EXPIRE, COOKIE_PATH, ".mindmeto.com" );

      		unset( $_SESSION['userId'] );
      		unset( $_SESSION['userSessionId'] );
			unset( $_SESSION['oauthState'] );
			unset( $_SESSION['oauthRequestToken'] );
	    	unset( $_SESSION['oauthRequestTokenSecret'] );
	
			$this->userId = NULL;
  			$this->userSessionId = NULL;
      		$this->loggedIn = false;

   		}
		
	   function generateRandomID(){
			
			return md5($this->generateRandStr(16));

	   }

	   function generateRandStr($length){

	      $randstr = "";

	      for($i=0; $i<$length; $i++) {

	         $randnum = mt_rand(0,61);
	         if($randnum < 10) {
	            $randstr .= chr($randnum+48);
	         } else if($randnum < 36) {
	            $randstr .= chr($randnum+55);
	         } else {
	            $randstr .= chr($randnum+61);
	         }

	      }

	      return $randstr;

	   }
		
	};

	$session = new Session;
	
?>