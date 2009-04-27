<?php include_once('./tmp/header.php'); ?>

<div id="wrap">
    <div id="header">
        <h1>Hello <?=$userDetailsJSON->screen_name?>, here're your reminders...</h1>
    </div>
    <div id="list">
        <?php if( isset( $existingReminders ) && count( $existingReminders ) > 0 ):

			echo '<ul>';
				while( $reminder = $existingReminders->getResultsArray() ) {
					
					echo '<li>'.$reminder['reminder_text'].'</li>';
					
				}
			echo '</ul>';
	
		endif; ?>
		User timezone: GMT<?=substr( $session->userDetails['user_timezone'], 0, stripos($session->userDetails['user_timezone'], ":"))?><br />
		Default time: <?=$session->userDetails['user_default_time']?><br />
		<?php echo ( $session->userDetails['user_allow_reminders'] ) ? "Reminders are ON" : "Reminders are OFF"; ?><br />
		<?php echo ( $session->userDetails['user_allow_confirmations'] ) ? "Confirmations are ON" : "Confirmations are OFF"; ?>
    </div>
    <p><a href="<?=BASE_URL?>logout.php">Logout</a></p>
</div>

<?php include_once('./tmp/footer.php'); ?>