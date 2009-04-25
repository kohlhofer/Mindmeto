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
    </div>
    <p><a href="<?=BASE_URL?>logout.php">Logout</a></p>
</div>

<?php include_once('./tmp/footer.php'); ?>