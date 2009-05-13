<?php

	define(MINDMETO_ROOT, '');
	
	require_once(MINDMETO_ROOT.'inc/session.php');
	require_once(MINDMETO_ROOT.'inc/twitter/bot.php');

	$reminders = new Reminder();
	$latestPublicReminders = $reminders->fetchLatestPublicHTML();

	$headerCode = <<<JS
		<script>
			$(document).ready(function() {
				
				//setInterval("updateTicker()", 30000);

			});
		</script>
JS;
	
	include(MINDMETO_ROOT.'tmp/welcome.php');

?>