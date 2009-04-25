<?php

	define(MINDMETO_ROOT, '');
	
	require_once(MINDMETO_ROOT.'inc/session.php');
	$session->logout();
	
	header('Location: list.php');

?>