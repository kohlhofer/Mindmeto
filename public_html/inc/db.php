<?php

	require_once(MINDMETO_ROOT.'inc/config.php');

	class DB {
    
	    var $dbHost, $dbName, $dbUser, $dbPassword;
	    var $dbConnection, $dbResults;
    
	    function DB( $dbHost = DB_HOST, $dbName = DB_NAME, $dbUser = DB_USERNAME, $dbPassword = DB_PASSWORD ) {
    
	        $this->dbHost = $dbHost;
	        $this->dbName = $dbName;
	        $this->dbUser = $dbUser;
	        $this->dbPassword = $dbPassword;
    
	        $this->dbConnection = @mysql_connect($this->dbHost, $this->dbUser, $this->dbPassword) or die( mysql_error() );
	        if( $this->dbConnection ) {
        
	            @mysql_select_db( $this->dbName ) or die( mysql_error() );
	            return true;
            
	        }
        
	        return false;
        
	    }
    
	    function query( $sql ) {
        
	        $queryResults = @mysql_query( $sql, $this->dbConnection ) or die( mysql_error() );
	        $this->dbResults = new DBResults( $queryResults );
        
	        return $this->dbResults;
        
	    }
    
	    function sanitize( $sCode ) {
        
			if ( function_exists( "mysql_real_escape_string" ) ) { 
	    
	            $sCode = mysql_real_escape_string( $sCode );
            
			} else {
            
		    	$sCode = addslashes( $sCode );
            
			}
			return $sCode;
    
	    }
	
		function fetchLastInsertId() {
			
			$result = $this->query("SELECT LAST_INSERT_ID() as last_id");
			$resultRow = $result->getRow();
			
			return $resultRow['last_id'];
			
		}
    
	    function fetchUserDetails( $userId ) {
    
	    	$result = $this->query('SELECT * FROM '.DB_TBL_USERS.' WHERE user_id="'.$this->sanitize($userId).'"');
	    	if( $result->numRows() > 0 ) {
	    		$row = $result->getRow();
				$row['user_twitter_data'] = unserialize( $row['user_twitter_data'] );
				return $row;
	    	}
    	
	    	return false;
    
	    }
    
		function fetchUserId( $ip, $sessionId ) {

			$result = $this->query( "SELECT user_id FROM ".DB_TBL_USERS." WHERE LENGTH(user_oauth_token) > 0 AND LENGTH(user_oauth_token_secret) > 0 AND user_ip='".$this->sanitize($ip)."' AND user_session_id='".$this->sanitize($sessionId)."'" );
			if( $result->numRows() > 0 ) {
				$row = $result->getRow();
				return $row['user_id'];
			}
			
			return false;
			
		}

	};

	class DBResults {
  
	    var $results, $row;
    
	    function DBResults( $results ) { 
    
	    	$this->results = $results;
    	
	    }
	    function getResults() {
        
	        return $this->results;
    
	    }
	    function getResultsArray() {
    
	    	return mysql_fetch_array($this->results);
    
	    }
    
	    function numRows() {
        
	        return mysql_num_rows($this->results);
        
	    }
	    function getRow() {
        
	        $this->row = mysql_fetch_array($this->results);
	        return $this->row;
    
	    }
	    function getField($valueName) {
        
	        return $this->row[$valueName];
        
	    }
    
	};

	$db = new DB;

?>