<?php include_once('./tmp/header.php'); ?>

			<div id="about">
				<img src="public/img/logos/logged-in.png" alt="MindMeTo" />
			</div>
			<div id="two-column" class="clearfix">
				<div id="left-column">
					
					<div id="reminders">
						
						<?php if( $queryResult !== NULL ) echo $queryResult; ?>
						
				        <?php if( isset( $existingReminders ) && count( $existingReminders ) > 0 ):

							echo '<ul>';
								while( $reminder = $existingReminders->getResultsArray() ) {

									echo '<li class="clearfix">'.
										 '<div class="reminder-data">'.
										 	'<a href="#" class="remove"></a> '.$reminder['reminder_text'].
										 '</div>'.
										 '<div class="reminder-meta">'.
											date( 'jS M \a\t g:iA', convertDefaultTime( $session->userId, strtotime($reminder['reminder_timestamp']))).
										 '</div>'.
										 '</li>';

								}
							echo '</ul>';

						else: ?>

							You currently have no reminders set!

						<?php endif; ?>
					</div>
				</div>
				<div id="right-column">

					<div id="reminder-web-input">

						<div class="speech">Want to set reminders and configure MindMeTo without using Twitter? Alright, then!</div>
						
						<div id="reminder-web-form">
							<form action="#" method="post">
								<input type="text" name="command" class="large-input" />

								<input type="submit" value="Go!" />
							</form>
						</div>
						
						User timezone: GMT<?=substr( $session->userDetails['user_timezone'], 0, stripos($session->userDetails['user_timezone'], ":"))?><br />
						Default time: <?=$session->userDetails['user_default_time']?><br />
						<?php echo ( $session->userDetails['user_allow_reminders'] ) ? "Reminders are ON" : "Reminders are OFF"; ?><br />
						<?php echo ( $session->userDetails['user_allow_confirmations'] ) ? "Confirmations are ON" : "Confirmations are OFF"; ?>

					</div>

				</div>
			</div>

<?php include_once('./tmp/footer.php'); ?>