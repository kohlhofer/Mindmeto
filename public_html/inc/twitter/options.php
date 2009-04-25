<?php

	class OptionsHandler {
	
		function OptionsHandler() {} 
		function getValue( $option ) {
			
			global $db;
			
			$result = $db->query("SELECT value FROM ".DB_TBL_OPTIONS." WHERE name='".$db->sanitize($option)."'");
			if( $result->numRows() > 0 ) {
				$data = $result->getRow();
				return $data['value'];
			}
			
			return false;
			
		}
		
		function setValue( $name, $value ) {
			
			global $db;
			
			$result = $db->query("SELECT value FROM ".DB_TBL_OPTIONS." WHERE name='".$db->sanitize($name)."'");
			if( $result->numRows() > 0 ) {
				
				$db->query("UPDATE ".DB_TBL_OPTIONS." SET value='".$db->sanitize($value)."' WHERE name='".$db->sanitize($name)."'");
				
			} else {
				
				$db->query("INSERT INTO ".DB_TBL_OPTIONS." (name, value) VALUES ('".$db->sanitize($name)."', '".$db->sanitize($value)."')");
				
			}
			
			return false;
			
		}
		
	};

?>