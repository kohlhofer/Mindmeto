<?php include_once('./tmp/header.php'); ?>

<div id="about">
	<div id="about-inner" class="center">
		<img src="public/img/logos/logged-in.png" alt="MindMeTo" />
	</div>
</div>			
<div id="content-container">
	<div id="content" class="center clearfix">

		<div class="left-column">

			<div id="reminders">
				<?php 
				if( $queryResult !== NULL ) {
					echo '<div id="reminder-web-result">';
					echo $queryResult;
					echo '</div>';
				}
				if( isset( $existingReminders ) && $existingReminders->numRows() > 0 ) {

					echo '<ul>';
					while( $reminder = $existingReminders->getResultsArray() ) {

						echo '<li class="clearfix" id="reminder-'.$reminder['reminder_id'].'">'.
							'<div class="reminder-data">'.
							'<a href="ajax.php?a=remove&id='.$reminder['reminder_id'].'" onclick="return removeReminder(\''.$reminder['reminder_id'].'\')" class="remove"></a> '.$reminder['reminder_text'].
							'</div>'.
							'<div class="reminder-meta">';

						$timestamp = convertDefaultTime( $session->userId, strtotime($reminder['reminder_timestamp']));
						if( $timestamp !== false ) {
							echo date( 'jS M \a\t g:iA', $timestamp);
						} else {
							echo date('jS M \a\t g:iA', strtotime($reminder['reminder_timestamp']));
						}

						echo '</div></li>';

					}
					echo '</ul>';

				} else {

					echo 'You currently have no reminders set!';

				} ?>

			</div>

		</div>
		<div id="reminder-web-input" class="right-column">

			<div class="speech">Add a new reminder</div>

			<div id="reminder-web-form" class="clearfix">
				<form action="#" method="post">
					<textarea name="command" class="large-input" maxlength="200"></textarea>
					<input type="submit" class="button" value="Go!" />
				</form>
			</div>

			<div id="reminder-settings">
				Your timezone is currently set to GMT<b><?=substr( $session->userDetails['user_timezone'], 0, stripos($session->userDetails['user_timezone'], ":"))?></b><br />
				Reminders without a time set will arrive at hour <b><?=$session->userDetails['user_default_time']?></b><br />
				<?php echo ( $session->userDetails['user_allow_reminders'] ) ? "Direct Message reminder messages are <b>ON</b>" : "Direct Message reminder messages are <b>OFF</b>"; ?><br />
				<?php echo ( $session->userDetails['user_allow_confirmations'] ) ? "Direct Message confirmation messages are <b>ON</b>" : "Direct Message reminder messages are <b>OFF</b>"; ?>
			</div>

		</div>

	</div>
</div>

<?php include_once('./tmp/footer.php'); ?>