<?php

	define(MINDMETO_ROOT, '');
	
	require_once(MINDMETO_ROOT.'inc/session.php');
	require_once(MINDMETO_ROOT.'inc/reminder.php');

	$reminder = new Reminder();
	$latestPublicReminders = $reminder->fetchLatestPublic(0);
	$publicReminders = "";
	
	if( $latestPublicReminders ) {
		
		$i = 0;
		
		while( $publicReminder = $latestPublicReminders->getRow() ) {
		
			$twitterData = unserialize( $publicReminder['user_twitter_data'] );
			$publicReminders .= '<li';
			
			if( $i % 2 != 0 ) $publicReminders .= ' class="odd"';
			
			$publicReminders .= '><div class="reminder">'.	
										'<b>@mindmeto</b> '.$publicReminder['reminder_full_text'].
									'</div>'.
									'<div class="reminder-meta">'.
										'<a href="http://twitter.com/'.$twitterData->screen_name.'"><img src="'.$twitterData->profile_image_url.'" class="avatar" alt="'.$twitterData->screen_name.'"  /> '.$twitterData->screen_name.'</a>'.
									'</div>'.
								'</li>';
								
			$i = ( $i ) ? 0 : 1;
		
		}
		
	}

	$headerCode = <<<JS
		<script>
			$(document).ready(function() {
				
				//setInterval("updateTicker()", 30000);

			});
		</script>
JS;
	
	include(MINDMETO_ROOT.'tmp/welcome.php');

?>